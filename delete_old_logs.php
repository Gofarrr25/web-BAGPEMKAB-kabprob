<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$deleted = \App\Models\Activity::whereNull('subject_type')
    ->where('description', 'like', 'Mengakses halaman%')
    ->delete();

echo "Deleted " . $deleted . " old 'Melihat Data' logs.\n";
