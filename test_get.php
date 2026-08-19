<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = \Illuminate\Http\Request::create('/admin/dashboard', 'GET');
$response = app()->handle($request);
$logs = \App\Models\Activity::latest()->take(3)->get();
foreach($logs as $l) {
    echo $l->created_at . ' | ' . $l->event . ' | ' . ($l->properties['type'] ?? 'N/A') . "\n";
}
