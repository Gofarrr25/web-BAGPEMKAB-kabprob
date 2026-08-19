<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$menus = \App\Models\Menu::onlyTrashed()->whereIn('title', ['PROFIL', 'LAYANAN', 'DOKUMEN', 'HUBUNGI'])->get();
foreach ($menus as $menu) {
    echo "Restoring menu: " . $menu->title . "\n";
    $menu->restore();
}
echo "Done.\n";
