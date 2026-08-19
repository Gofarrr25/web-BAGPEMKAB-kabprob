<?php
$options = [
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"
    ]
];
$context = stream_context_create($options);
$html = @file_get_contents('https://www.pixwox.com/profile/ghoffaarrr/', false, $context);
echo "Length: " . strlen($html) . "\n";
echo substr($html, 0, 200);
