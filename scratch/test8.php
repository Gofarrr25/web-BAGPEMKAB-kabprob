<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$menus = \App\Models\Menu::get(['id', 'title', 'url', 'page_id', 'parent_id'])->toArray();
$pages = \App\Models\Page::get(['id', 'title', 'slug', 'menu_group', 'parent_id', 'is_in_menu'])->toArray();

echo "MENUS:\n";
print_r($menus);
echo "\nPAGES:\n";
print_r($pages);
