<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$menus = \App\Models\Menu::get(['id', 'title', 'url', 'page_id', 'parent_id', 'module_type'])->toArray();

echo json_encode($menus, JSON_PRETTY_PRINT);
