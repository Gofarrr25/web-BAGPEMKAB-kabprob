<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$page = \App\Models\Page::where('slug', 'standar-pelayanan-publik-1')->first();
if ($page) {
    echo "IMAGE: " . $page->image . "\n";
    echo "PDF_FILE: " . $page->pdf_file . "\n";
    echo "CONTENT: " . $page->content . "\n";
} else {
    echo "Page not found\n";
}
