<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logs = Spatie\Activitylog\Models\Activity::where('log_name', 'admin_log')->latest()->limit(5)->get();
foreach($logs as $l) {
    echo $l->description . " | " . $l->properties->toJson() . "\n";
}
