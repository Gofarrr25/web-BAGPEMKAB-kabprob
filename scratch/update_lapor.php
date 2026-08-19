<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$page = \App\Models\Page::where('slug', 'like', '%lapor%')->first();
if (!$page) {
    $page = \App\Models\Page::create([
        'title' => 'Lapor SP4N',
        'slug' => 'lapor-sp4n',
        'content' => '',
        'is_active' => true
    ]);
    echo "Created new page. ";
} else {
    echo "Found existing page. ";
}

$html = '
<div class="text-center space-y-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <img src="https://diskominfo.probolinggokab.go.id/frontend/img/sp4n.jpg" class="w-full block" alt="Lapor SP4N">
        
        <div class="p-8">
            <h3 class="mb-6 font-bold text-2xl text-gray-800 text-center">
                Klik Logo untuk Aspirasi dan Pengaduan
            </h3>
            
            <a href="https://www.lapor.go.id/instansi/pemerintah-kabupaten-probolinggo" target="_blank" class="inline-block hover:scale-105 transition-transform duration-300">
                <img src="https://diskominfo.probolinggokab.go.id/frontend/img/logoLAPOR.png" class="mx-auto block w-64 md:w-80" alt="Logo Lapor"> 
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden space-y-0">
        <img src="https://diskominfo.probolinggokab.go.id/frontend/img/cara_melapor.jpg" class="w-full block" alt="Cara Melapor">
        <img src="https://diskominfo.probolinggokab.go.id/frontend/img/Melapor-yang-baik.jpg" class="w-full block" alt="Melapor Yang Baik">
    </div>
</div>
';

$page->content = $html;
$page->save();

echo "Updated content for: " . $page->title;
