<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DOMDocument;

trait ProcessesBase64
{
    /**
     * Parse HTML and extract base64 images to physical files.
     *
     * @param string|null $html
     * @param string $folder
     * @return string|null
     */
    public function processBase64Images($html, $folder = 'uploads/ckeditor')
    {
        if (empty($html)) {
            return $html;
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        
        $encodedHtml = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        
        $dom->loadHTML($encodedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');
        $hasChanges = false;

        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            if (preg_match('/^data:image\/(\w+);base64,/', $src, $type)) {
                $hasChanges = true;
                
                $extension = strtolower($type[1]);
                if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $extension = 'jpg';
                }

                $base64Data = substr($src, strpos($src, ',') + 1);
                $imageData = base64_decode($base64Data);

                if ($imageData !== false) {
                    $fileName = $folder . '/' . Str::random(40) . '.' . $extension;
                    Storage::disk('public')->put($fileName, $imageData);

                    // Update src attribute with the public URL
                    $img->setAttribute('src', asset('storage/' . $fileName));
                }
            }
        }

        if ($hasChanges) {
            return $dom->saveHTML();
        }

        return $html;
    }
}
