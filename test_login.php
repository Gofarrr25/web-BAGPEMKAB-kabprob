<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/login', 'POST', [
    'username' => 'test', 
    'password' => 'wrong',
    'captcha' => '1234'
]);
// Simulate session
$request->setLaravelSession(app('session.store'));
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
$kernel->terminate($request, $response);
