<?php
$html = file_get_contents('https://diskominfo.probolinggokab.go.id/halaman/struktur-organisasi');
file_put_contents('scratch/struktur_html.txt', $html);
