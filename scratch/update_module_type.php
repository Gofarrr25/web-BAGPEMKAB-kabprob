<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$menus = App\Models\Menu::all();
foreach($menus as $m) {
    $title = strtolower($m->title);
    $url = $m->url ?? '';
    $module = null;

    if (in_array($title, ['berita', 'informasi', 'artikel']) || str_starts_with($url, '/informasi') || str_starts_with($url, '/berita')) {
        $module = 'posts';
    } elseif (in_array($title, ['ppid', 'perencanaan kinerja', 'pengukuran kinerja', 'pelaporan kinerja', 'evaluasi kinerja', 'regulasi', 'informasi berkala', 'informasi serta merta', 'informasi setiap saat']) || str_starts_with($url, '/dokumen')) {
        $module = 'documents';
    } elseif (in_array($title, ['agenda', 'agenda kegiatan']) || str_starts_with($url, '/agenda')) {
        $module = 'agendas';
    } elseif (in_array($title, ['video', 'galeri', 'foto', 'galery']) || str_starts_with($url, '/galeri') || str_starts_with($url, '/page/video') || str_starts_with($url, '/page/galery')) {
        $module = 'galleries';
    } elseif ($title === 'struktur organisasi' || str_starts_with($url, '/struktur-organisasi')) {
        $module = 'members';
    } elseif ($title === 'kontak' || $title === 'hubungi kami' || str_starts_with($url, '/kontak')) {
        $module = 'contact';
    } elseif ($title === 'banner' || str_starts_with($url, '/banner')) {
        $module = 'banners';
    } elseif (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
        $module = 'custom';
    } else {
        $module = 'page';
    }

    $m->module_type = $module;
    $m->save();
}

echo "Done.\n";
