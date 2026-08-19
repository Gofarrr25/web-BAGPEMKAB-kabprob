<?php
use Illuminate\Support\Facades\Http;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::find(1);
\Illuminate\Support\Facades\Auth::login($user);

// Simulate CSRF bypass by calling controller directly
$controller = app()->make(\App\Http\Controllers\Admin\GalleryController::class);

$request = \Illuminate\Http\Request::create('/admin/galleries', 'POST', [
    'title' => 'Test Gallery Direct ' . time(),
    'type' => 'video',
    'video_url' => 'https://youtube.com/watch?v=' . time(),
]);

$controller->store($request);

$logs = \App\Models\Activity::latest()->take(3)->get();
foreach($logs as $l) {
    echo $l->created_at . ' | ' . $l->event . ' | ' . $l->subject_type . ' | ' . ($l->properties['module'] ?? 'N/A') . ' | ' . ($l->properties['type'] ?? 'N/A') . "\n";
}
