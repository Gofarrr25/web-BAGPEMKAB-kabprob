<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/admin/pages/36', 'PUT', [
    "status" => "publish",
    "title" => "HALO SAE",
    "content" => "<p>Konten halaman <strong>HALO SAE</strong> belum diisi. Silakan gunakan tombol <strong>Kelola Konten</strong> untuk melengkapi teks, gambar, dan dokumen.</p>",
    "external_url" => "https://halosae.probolinggokab.go.id/",
    "seo_title" => null,
    "seo_description" => null,
    "menu_group" => null,
    "custom_menu_group" => null,
    "parent_id" => null,
    "order_index" => "0",
    "is_in_menu" => "1"
]);

// Auth as user 1
Auth::loginUsingId(1);

$controller = $app->make(\App\Http\Controllers\Admin\PageController::class);
$page = App\Models\Page::find(36);

$start = microtime(true);
$response = $controller->update($request, $page);
$duration = microtime(true) - $start;

echo "Duration: $duration seconds\n";
echo "Response status: " . $response->getStatusCode() . "\n";
