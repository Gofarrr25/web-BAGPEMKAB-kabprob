<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$gallery = \App\Models\Gallery::create([
    'user_id' => 1,
    'title' => 'Test Gallery ' . time(),
    'type' => 'image',
    'file_path' => 'test.jpg'
]);

$log = \App\Models\Activity::latest()->first();
echo "Gallery ID: " . $gallery->id . "\n";
if ($log && $log->subject_id == $gallery->id && $log->subject_type == \App\Models\Gallery::class) {
    echo "Log created: " . $log->description . " | Module: " . $log->properties['module'] . "\n";
} else {
    echo "NO LOG CREATED FOR GALLERY!\n";
    if ($log) {
        echo "Last log was for: " . $log->subject_type . " ID: " . $log->subject_id . "\n";
    }
}
