<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$menus = App\Models\Menu::with('page')->get();
$output = [];
foreach($menus as $m) {
    $output[] = [
        'id' => $m->id,
        'title' => $m->title,
        'url' => $m->url,
        'page_id' => $m->page_id,
        'module_type' => $m->module_type,
        'page_title' => $m->page ? $m->page->title : null,
        'page_slug' => $m->page ? $m->page->slug : null,
    ];
}
echo json_encode($output, JSON_PRETTY_PRINT);
