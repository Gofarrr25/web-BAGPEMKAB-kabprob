<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/login', 'POST', ['username' => 'testuser']);
event(new \Illuminate\Auth\Events\Failed('web', null, ['username' => 'testuser']));
event(new \Illuminate\Auth\Events\Lockout($request));

$logs = Spatie\Activitylog\Models\Activity::where('log_name', 'Authentication')->latest()->limit(2)->get();
foreach($logs as $l) {
    echo $l->description . " | " . $l->properties->toJson() . "\n";
}
