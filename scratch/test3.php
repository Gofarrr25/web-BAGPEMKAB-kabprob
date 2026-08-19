<?php
$html = file_get_contents('https://greatfon.com/v/ghoffaarrr');
preg_match_all('/<a[^>]*href="([^"]+)"[^>]*>.*?<img[^>]*src="([^"]+)"/is', $html, $matches);
print_r($matches[1]);
print_r($matches[2]);
