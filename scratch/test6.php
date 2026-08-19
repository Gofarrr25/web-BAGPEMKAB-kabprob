<?php
$options = [
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"
    ]
];
$context = stream_context_create($options);
$html = file_get_contents('https://imginn.com/ghoffaarrr/', false, $context);
file_put_contents('scratch/imginn_html.txt', $html);
