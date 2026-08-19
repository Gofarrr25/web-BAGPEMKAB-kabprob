<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/admin/pages/36', 'PUT', [
    "status" => "publish",
    "title" => "HALO SAE",
    "content" => "<p>Konten halaman <strong>HALO SAE</strong> belum diisi.</p>",
]);

Auth::loginUsingId(1);
$user = Auth::user();

$middleware = new \App\Http\Middleware\ActivityLogger();

$start = microtime(true);
$response = new \Illuminate\Http\Response('ok');
$middleware->handle($request, function($req) use ($response) {
    return $response;
});
$duration = microtime(true) - $start;

echo "Middleware Duration: $duration seconds\n";
