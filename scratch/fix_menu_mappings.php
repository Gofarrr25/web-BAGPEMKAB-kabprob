<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$menus = App\Models\Menu::all();
$pages = App\Models\Page::all()->keyBy('id');

foreach ($menus as $m) {
    echo "Processing Menu: {$m->title} (ID: {$m->id})\n";
    
    // 1. If it's a special module, clean it up!
    if (in_array($m->module_type, ['posts', 'documents', 'agendas', 'galleries', 'members'])) {
        // Unlink page_id because special modules don't need a static page
        if ($m->page_id) {
            echo "- Unlinking page_id {$m->page_id} because it is a special module.\n";
            $m->page_id = null;
        }

        // Fix the URL
        if ($m->module_type === 'posts') {
            $m->url = '/informasi';
        } elseif ($m->module_type === 'galleries') {
            if (stripos($m->title, 'video') !== false) {
                $m->url = '/galeri-video';
            } else {
                $m->url = '/galeri';
            }
        } elseif ($m->module_type === 'members') {
            $m->url = '/struktur-organisasi';
        } elseif ($m->module_type === 'documents') {
            // e.g. "Perencanaan Kinerja" -> "/dokumen/perencanaan-kinerja"
            if (strtolower($m->title) !== 'ppid') {
                $m->url = '/dokumen/' . Illuminate\Support\Str::slug($m->title);
            } else {
                // If it's just PPID, maybe go to a default category or /dokumen
                $m->url = '/dokumen/informasi-berkala'; 
            }
        }
        
        $m->save();
        echo "- Updated Special Module URL to {$m->url}\n";
        continue; // Done with this menu
    }

    // 2. If it's a 'page' module, it must have a unique page.
    if ($m->module_type === 'page' && $m->url && str_starts_with($m->url, '/page/')) {
        $expectedSlug = str_replace('/page/', '', $m->url);
        
        // Let's see if the page it points to actually matches this slug.
        $needsNewPage = false;

        if (!$m->page_id) {
            $needsNewPage = true;
        } else {
            $page = $pages->get($m->page_id);
            if (!$page || $page->slug !== $expectedSlug) {
                echo "- Mismatch! Menu points to page_id {$m->page_id} (slug: " . ($page->slug ?? 'NULL') . "), but URL says {$expectedSlug}.\n";
                // If another menu already "owns" this page_id correctly, we definitely need a new page.
                // Let's just create/link the correct page.
                $needsNewPage = true;
            }
        }

        if ($needsNewPage) {
            // Find if the correct page exists by slug
            $correctPage = App\Models\Page::where('slug', $expectedSlug)->first();
            
            if ($correctPage) {
                echo "- Found existing correct page by slug: {$expectedSlug}. Linking to page_id {$correctPage->id}.\n";
                $m->page_id = $correctPage->id;
            } else {
                echo "- Creating NEW page for slug: {$expectedSlug}.\n";
                $newPage = App\Models\Page::create([
                    'title' => $m->title,
                    'slug' => $expectedSlug,
                    'content' => '<p>Konten halaman <strong>' . e($m->title) . '</strong> belum diisi.</p>',
                    'status' => 'publish',
                    'is_active' => true,
                    'is_in_menu' => true,
                ]);
                $m->page_id = $newPage->id;
            }
            $m->save();
        }
    }
}
echo "Done.\n";
