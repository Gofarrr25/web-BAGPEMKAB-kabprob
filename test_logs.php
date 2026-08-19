<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$a = \App\Models\Activity::where('subject_type', \App\Models\Gallery::class)->get();
foreach($a as $l) {
    echo $l->event . ' - ' . $l->created_at . "\n";
}
echo "Total logs for Gallery: " . count($a) . "\n";
