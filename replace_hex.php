<?php
$viewsDir = __DIR__ . '/resources/views';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        
        // Replace hardcoded hex colors
        $newContent = str_replace('#849f73', '#2563eb', $content); // brand-olive -> blue-600
        $newContent = str_replace('#7b9466', '#1d4ed8', $newContent); // darker olive -> blue-700
        $newContent = str_replace('#688055', '#1e40af', $newContent); // darkest olive -> blue-800
        
        if ($content !== $newContent) {
            file_put_contents($file->getPathname(), $newContent);
            echo "Updated Hex in: " . $file->getPathname() . "\n";
        }
    }
}
echo "Done.\n";
