<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logs = \App\Models\Activity::latest()->take(10)->get();
foreach($logs as $l) {
    echo $l->created_at . ' | ' . $l->event . ' | ' . $l->subject_type . ' | ' . ($l->properties['module'] ?? 'N/A') . ' | ' . ($l->properties['type'] ?? 'N/A') . "\n";
}
