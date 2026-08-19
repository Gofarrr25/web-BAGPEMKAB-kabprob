<?php
$html = file_get_contents('https://diskominfo.probolinggokab.go.id/lapor');
file_put_contents('scratch/lapor_html.txt', $html);
