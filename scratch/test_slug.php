<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$start = microtime(true);

$page = App\Models\Page::find(36);
$baseSlug = Illuminate\Support\Str::slug('HALO SAE');
$slug = $baseSlug ?: 'page';
$count = 1;
while (App\Models\Page::where('slug', $slug)->where('id', '!=', 36)->exists()) {
    $slug = ($baseSlug ?: 'page') . '-' . $count;
    $count++;
}

echo "Slug generated: $slug in " . (microtime(true) - $start) . " seconds\n";
