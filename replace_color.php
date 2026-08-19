<?php
$viewsDir = __DIR__ . '/resources/views';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        
        // Replace 'brand-olive' with 'brand-blue'
        $newContent = str_replace('brand-olive', 'brand-blue', $content);
        
        // Also replace 'hover:bg-green-700' with 'hover:bg-blue-700' specifically in the context of these buttons
        // Let's just do a regex replace if it's near brand-blue.
        // Actually, let's just replace all 'hover:bg-green-700' with 'hover:bg-blue-700' if they were next to brand-olive originally.
        // Instead, I'll just change 'hover:bg-green-700' to 'hover:bg-blue-700' universally in frontend since it's meant to be a brand hover color there.
        // Let's do it safely.
        $newContent = str_replace('hover:bg-green-700', 'hover:bg-blue-700', $newContent);

        if ($content !== $newContent) {
            file_put_contents($file->getPathname(), $newContent);
            echo "Updated: " . $file->getPathname() . "\n";
        }
    }
}
echo "Done.\n";
