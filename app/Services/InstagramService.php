<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InstagramService
{
    /**
     * Normalisasi URL & Username Instagram dari input teks
     */
    public static function extractUsername(string $input): string
    {
        $input = trim($input);
        
        // Hapus karakter @ jika pengguna memasukkan @username
        $input = ltrim($input, '@');

        // Jika input berupa URL penuh (misal: https://www.instagram.com/bagpemerintahan_probolinggokab/)
        if (preg_match('/instagram\.com\/([A-Za-z0-9_.-]+)/i', $input, $matches)) {
            $parsed = trim($matches[1], '/');
            if ($parsed && !in_array(strtolower($parsed), ['p', 'reel', 'tv', 'stories', 'explore'])) {
                return $parsed;
            }
        }

        // Jika input sudah berupa username (hanya huruf, angka, underscore, titik)
        if (preg_match('/^[A-Za-z0-9_.-]+$/', $input)) {
            return $input;
        }

        return 'bagpemerintahan_probolinggokab';
    }

    /**
     * Dapatkan URL Profil Lengkap dari username
     */
    public static function getProfileUrl(?string $username = null): string
    {
        if (!$username) {
            $username = Setting::where('key', 'instagram_username')->value('value')
                ?? self::extractUsername(Setting::where('key', 'instagram_url')->value('value') ?? '');
        }
        $cleanUser = self::extractUsername($username ?: 'bagpemerintahan_probolinggokab');
        return "https://www.instagram.com/" . $cleanUser;
    }

    /**
     * Update Setting Instagram di Database secara konsisten
     */
    public static function updateAccountConfig(string $inputUrlOrUsername): array
    {
        $username = self::extractUsername($inputUrlOrUsername);
        $fullUrl = "https://www.instagram.com/" . $username;
        $displayName = ucwords(str_replace(['_', '.'], ' ', $username));

        Setting::updateOrCreate(['key' => 'instagram_url'], ['value' => $fullUrl]);
        Setting::updateOrCreate(['key' => 'instagram_username'], ['value' => $username]);
        Setting::updateOrCreate(['key' => 'instagram_name'], ['value' => $displayName]);

        return [
            'username' => $username,
            'url' => $fullUrl,
            'name' => $displayName,
        ];
    }

    public static function getLatestPosts(int $limit = 6): array
    {
        $username = Setting::where('key', 'instagram_username')->value('value')
            ?? self::extractUsername(Setting::where('key', 'instagram_url')->value('value') ?? '');

        if (!$username) {
            return [];
        }

        $formattedPosts = [];
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.9',
        ];

        // 1. Coba fetch dari Picuki (Public Instagram Viewer)
        try {
            $response = Http::timeout(8)->withHeaders($headers)->get("https://www.picuki.com/profile/{$username}");
            if ($response->successful() && strpos($response->body(), 'box-photo') !== false) {
                $html = $response->body();
                preg_match_all('/<div class="box-photo">.*?<a href="(.*?)".*?<img.*?src="(.*?)".*?alt="(.*?)".*?<\/div>/is', $html, $matches);
                
                if (!empty($matches[0])) {
                    for ($i = 0; $i < min($limit, count($matches[0])); $i++) {
                        $postUrl = $matches[1][$i];
                        $image = $matches[2][$i];
                        $caption = strip_tags($matches[3][$i]);
                        
                        $formattedPosts[] = [
                            'id' => uniqid(),
                            'image' => $image,
                            'caption' => Str::limit(trim($caption), 150),
                            'post_url' => $postUrl,
                            'embed_url' => null,
                            'created_at' => date('d M Y'),
                            'username' => $username,
                        ];
                    }
                    return $formattedPosts;
                }
            }
        } catch (\Exception $e) {
            Log::warning("Picuki scrape failed: " . $e->getMessage());
        }

        // 2. Coba fetch dari Dumpor (Alternatif)
        try {
            $response = Http::timeout(8)->withHeaders($headers)->get("https://dumpor.com/v/{$username}");
            if ($response->successful()) {
                $html = $response->body();
                preg_match_all('/<a class="item" href="(.*?)">.*?<img.*?src="(.*?)".*?<\/a>/is', $html, $matches);
                
                if (!empty($matches[0])) {
                    for ($i = 0; $i < min($limit, count($matches[0])); $i++) {
                        $postUrl = "https://dumpor.com" . $matches[1][$i];
                        $image = $matches[2][$i];
                        
                        $formattedPosts[] = [
                            'id' => uniqid(),
                            'image' => $image,
                            'caption' => "Lihat postingan di Instagram",
                            'post_url' => "https://instagram.com/{$username}",
                            'embed_url' => null,
                            'created_at' => date('d M Y'),
                            'username' => $username,
                        ];
                    }
                    return $formattedPosts;
                }
            }
        } catch (\Exception $e) {
            Log::warning("Dumpor scrape failed: " . $e->getMessage());
        }

        // 3. Fallback ke RSSHub
        try {
            $response = Http::timeout(8)->withHeaders($headers)->get("https://rsshub.app/instagram/user/{$username}");
            if ($response->successful()) {
                $xml = simplexml_load_string($response->body());
                if ($xml && isset($xml->channel->item)) {
                    $count = 0;
                    foreach ($xml->channel->item as $item) {
                        if ($count >= $limit) break;
                        $desc = (string)$item->description;
                        preg_match('/<img.*?src=["\'](.*?)["\']/', $desc, $imgMatches);
                        $image = $imgMatches[1] ?? null;
                        $caption = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $desc));
                        
                        $formattedPosts[] = [
                            'id' => uniqid(),
                            'image' => $image,
                            'caption' => Str::limit(trim($caption), 150),
                            'post_url' => (string)$item->link,
                            'embed_url' => null,
                            'created_at' => date('d M Y', strtotime((string)$item->pubDate)),
                            'username' => $username,
                        ];
                        $count++;
                    }
                    return $formattedPosts;
                }
            }
        } catch (\Exception $e) {
            Log::error("RSSHub scrape failed: " . $e->getMessage());
        }

        return $formattedPosts;
    }
}
