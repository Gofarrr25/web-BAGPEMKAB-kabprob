<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$banner = \App\Models\Banner::create([
    'user_id' => 1,
    'title' => 'Test Banner',
    'image_path' => 'test.jpg',
    'is_active' => true
]);

$gallery = \App\Models\Gallery::create([
    'user_id' => 1,
    'title' => 'Test Gallery',
    'type' => 'image',
    'file_path' => 'test.jpg'
]);

echo "Banner ID: " . $banner->id . "\n";
echo "Gallery ID: " . $gallery->id . "\n";

$bLog = \App\Models\Activity::where('subject_type', \App\Models\Banner::class)->where('subject_id', $banner->id)->first();
$gLog = \App\Models\Activity::where('subject_type', \App\Models\Gallery::class)->where('subject_id', $gallery->id)->first();

echo "Banner Log: " . ($bLog ? "YES" : "NO") . "\n";
echo "Gallery Log: " . ($gLog ? "YES" : "NO") . "\n";
