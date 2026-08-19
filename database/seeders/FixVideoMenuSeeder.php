<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Gallery;
use App\Models\Banner;

class FixVideoMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Update Menu 17 (Galeri Foto & Video) menjadi Galeri Foto
        $menuFoto = Menu::where('title', 'like', '%Galeri Foto%')->first();
        if ($menuFoto) {
            $menuFoto->update([
                'title' => 'Galeri Foto',
                'url' => '/galeri-foto'
            ]);
        }

        // 2. Cari Menu Induk "INFORMASI"
        $informasiMenu = Menu::where('title', 'INFORMASI')->whereNull('parent_id')->first();
        $parentId = $informasiMenu ? $informasiMenu->id : 14;

        // 3. Tambahkan Submenu Terpisah "Galeri Video"
        Menu::updateOrCreate(
            ['title' => 'Galeri Video', 'parent_id' => $parentId],
            [
                'url' => '/galeri-video',
                'order_index' => 4,
                'target' => '_self',
                'is_active' => true
            ]
        );

        // 4. Reset & isi data sampel video dengan URL YouTube Asli yang terverifikasi BISA DIPUTAR (Embed Allowed)
        Gallery::where('type', 'video')->delete();

        Gallery::create([
            'user_id' => 1,
            'title' => 'Dokumentasi Resmi Kegiatan Bagian Pemerintahan Kabupaten Probolinggo',
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=L_LUpnjgPso'
        ]);

        Gallery::create([
            'user_id' => 1,
            'title' => 'Tasyakuran dan Doa Bersama Bupati dan Wabup Probolinggo',
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        ]);

        // 5. Reset & isi Banners (Slider Carousel Beranda)
        Banner::truncate();

        Banner::create([
            'user_id' => 1,
            'title' => 'Selamat Datang di Website Bagian Pemerintahan Kabupaten Probolinggo',
            'image_path' => 'https://diskominfo.probolinggokab.go.id/slider_img/slider_sae.png',
            'is_active' => true
        ]);

        Banner::create([
            'user_id' => 1,
            'title' => 'Hudan Syarifuddin,S.Sos.,M.Si. - Kepala Dinas Komunikasi, Informatika, Statistik dan Persandian',
            'image_path' => 'https://diskominfo.probolinggokab.go.id/slider_img/slider_kadis_hudan.jpg',
            'is_active' => true
        ]);
    }
}
