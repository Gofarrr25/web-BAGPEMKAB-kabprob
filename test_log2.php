<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logs = Spatie\Activitylog\Models\Activity::limit(5)->latest()->get();
foreach($logs as $l) {
    echo "ID: {$l->id} | subject_type: " . var_export($l->subject_type, true) . "\n";
}
