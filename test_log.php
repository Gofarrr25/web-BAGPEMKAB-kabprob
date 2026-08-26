<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logs = Spatie\Activitylog\Models\Activity::limit(50)->latest()->get();
foreach($logs as $l) {
    $props = is_string($l->properties) ? json_decode($l->properties, true) : $l->properties;
    $type = $props['type'] ?? 'null';
    $module = $props['module'] ?? 'null';
    echo "ID: {$l->id} | {$l->log_name} | {$l->description} | {$l->subject_type} | {$l->event} | type: {$type} | mod: {$module}\n";
}
