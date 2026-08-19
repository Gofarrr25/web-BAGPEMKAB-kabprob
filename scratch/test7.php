<?php
$options = [
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0\r\n"
    ]
];
$context = stream_context_create($options);
$html = @file_get_contents('https://saveig.app/api/ajaxSearch', false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-type: application/x-www-form-urlencoded\r\nUser-Agent: Mozilla/5.0\r\n",
        'content' => http_build_query(['q' => 'https://www.instagram.com/ghoffaarrr/'])
    ]
]));
echo "Length: " . strlen($html) . "\n";
echo substr($html, 0, 500);
