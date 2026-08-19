<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$menus = \App\Models\Menu::whereIn('title', [
    'SOP Penetapan dan Pemutakhiran Daftar Informasi Publik', 
    'SOP Permintaan Data Statistik'
])->get();

foreach ($menus as $menu) {
    echo "Deleting menu: " . $menu->title . "\n";
    $menu->delete();
}
echo "Done.\n";
