<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$html = Illuminate\Support\Facades\Http::withoutVerifying()->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'])->get('https://www.picuki.com/profile/ghoffaarrr')->body();
echo "HTML Length: " . strlen($html) . "\n";
echo "Has box-photo: " . (strpos($html, 'box-photo') !== false ? 'YES' : 'NO') . "\n";
if (strpos($html, 'box-photo') === false) {
    echo substr($html, 0, 500);
}
