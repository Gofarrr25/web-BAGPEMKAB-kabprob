<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
        * Seed the application's database.
        */
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Create permissions
        $permissions = [
            'manage users',
            'manage settings',
            'manage posts',
            'manage documents',
            'manage agendas',
            'manage galleries',
            'manage banners',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Create roles and assign created permissions
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Superadmin']);
        $roleSuperAdmin->givePermissionTo(Permission::all());

        $roleAdminOPD = Role::firstOrCreate(['name' => 'Admin OPD']);
        $roleAdminOPD->givePermissionTo([
            'manage posts',
            'manage documents',
            'manage agendas',
            'manage galleries',
            'manage banners',
        ]);

        // 4. Create Master Superadmin Account
        $superadmin = User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Bapak Kepala Diskominfo',
                'email' => 'superadmin@probolinggokab.go.id',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $superadmin->assignRole($roleSuperAdmin);

        // 5. Create Sample Admin OPD Account
        $adminOpd = User::firstOrCreate(
            ['username' => 'admin_opd'],
            [
                'name' => 'Admin Kominfo',
                'email' => 'admin@probolinggokab.go.id',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $adminOpd->assignRole($roleAdminOPD);

        $adminDefault = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Utama',
                'email' => 'adminutama@probolinggokab.go.id',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $adminDefault->assignRole($roleAdminOPD);

        // 6. Create Initial Categories (Data Awal)
        $categories = ['Pemerintahan', 'Sosial', 'Ekonomi', 'Infrastruktur', 'Pendidikan'];
        foreach ($categories as $cat) {
            Category::firstOrCreate([
                'name' => $cat,
                'slug' => Str::slug($cat)
            ]);
        }

        // 7. Create Default Static Pages (Halaman Statis Awal)
        $defaultPages = [
            [
                'title' => 'Struktur Organisasi',
                'slug' => 'struktur-organisasi',
                'menu_group' => 'PROFIL',
                'content' => '<p>Bagan Struktur Organisasi Bagian Pemerintahan Kabupaten Probolinggo. Silakan unggah gambar bagan struktur terbaru melalui tombol Edit.</p>',
            ],
            [
                'title' => 'Visi dan Misi',
                'slug' => 'visi-dan-misi',
                'menu_group' => 'PROFIL',
                'content' => '<h3>VISI</h3><p>Terwujudnya Kabupaten Probolinggo yang Sejahtera, Berdaya Saing, Berkelanjutan, Sejahtera dan Berakhlak Mulia.</p><h3>MISI</h3><ol><li>Mewujudkan tata kelola pemerintahan yang bersih, efektif, transparan dan akuntabel.</li><li>Meningkatkan kualitas pelayanan publik berbasis teknologi informasi.</li></ol>',
            ],
            [
                'title' => 'Tugas dan Fungsi',
                'slug' => 'tugas-dan-fungsi',
                'menu_group' => 'PROFIL',
                'content' => '<p>Bagian Pemerintahan mempunyai tugas melaksanakan penyiapan perumusan kebijakan daerah, pengoordinasian pelaksanaan tugas Perangkat Daerah, pemantauan dan evaluasi pelaksanaan kebijakan daerah di bidang ketentraman, ketertiban umum dan perlindungan masyarakat serta pemerintahan umum.</p>',
            ],
            [
                'title' => 'Profil Pejabat',
                'slug' => 'profil-pejabat',
                'menu_group' => 'PROFIL',
                'content' => '<p>Daftar Pejabat Struktur dan Fungsional Bagian Pemerintahan Kabupaten Probolinggo.</p>',
            ],
            [
                'title' => 'Standar Pelayanan Publik',
                'slug' => 'standar-pelayanan-publik',
                'menu_group' => 'LAYANAN',
                'content' => '<p>Dokumen Standar Pelayanan Publik Bagian Pemerintahan Kabupaten Probolinggo. Unggah file PDF dokumen pelayanan pada tombol Edit.</p>',
            ],
            [
                'title' => 'Survei Kepuasan Masyarakat (SKM)',
                'slug' => 'survei-kepuasan-masyarakat',
                'menu_group' => 'PROFIL',
                'content' => '<p>Hasil Survei Kepuasan Masyarakat terhadap Pelayanan Publik Bagian Pemerintahan Kabupaten Probolinggo.</p>',
            ],
            [
                'title' => 'Layanan Pengaduan & Informasi',
                'slug' => 'layanan-pengaduan-informasi',
                'menu_group' => 'LAYANAN',
                'content' => '<p>Informasi alur pengaduan dan layanan aspirasi masyarakat Kabupaten Probolinggo.</p>',
            ]
        ];

        foreach ($defaultPages as $p) {
            \App\Models\Page::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'title' => $p['title'],
                    'menu_group' => $p['menu_group'],
                    'content' => $p['content'],
                    'is_active' => true,
                ]
            );
        }

        // 8. Create Default Global Settings
        $defaultSettings = [
            'site_name' => 'Bagian Pemerintahan Kabupaten Probolinggo',
            'office_address' => 'Jl. Panglima Sudirman No. 134 lt. 3 - Kraksaan - Probolinggo',
            'site_description' => 'Website Resmi Bagian Pemerintahan Sekretariat Daerah Kabupaten Probolinggo.',
            'phone' => '0335 844554',
            'email' => 'bagpemerintahan@probolinggokab.go.id',
            'instagram_url' => 'https://instagram.com/probolinggokab',
            'facebook_url' => 'https://facebook.com/probolinggokab',
            'youtube_url' => 'https://youtube.com/@probolinggokab',
        ];

        foreach ($defaultSettings as $key => $val) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $val]
            );
        }

        // 9. Create Default Instagram Feed Posts
        $defaultIg = [
            'instagram_username' => 'bagpemerintahan_probolinggokab',
            'instagram_name' => 'Bagian Pemerintahan ProbolinggoKab',
            'instagram_followers' => '1,327 followers',
            'instagram_posts_count' => '677 posts',
        ];
        foreach ($defaultIg as $k => $v) {
            \App\Models\Setting::updateOrCreate(['key' => $k], ['value' => $v]);
        }

        $igPosts = [
            [
                'image' => 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&q=80&w=600',
                'caption' => 'PERTUMBUHAN PDRB MENURUT LAPANGAN USAHA',
                'post_url' => 'https://instagram.com',
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80&w=600',
                'caption' => 'Sosialisasi Program Pelayanan Publik',
                'post_url' => 'https://instagram.com',
            ],
            [
                'image' => 'https://images.unsplash.com/photo-1577962917302-cd874c4e31d2?auto=format&fit=crop&q=80&w=600',
                'caption' => 'ASN TAAT PAJAK KABUPATEN PROBOLINGGO',
                'post_url' => 'https://instagram.com',
            ]
        ];

        foreach ($igPosts as $index => $ig) {
            \App\Models\InstagramPost::firstOrCreate(
                ['caption' => $ig['caption']],
                [
                    'image' => $ig['image'],
                    'post_url' => $ig['post_url'],
                    'order_index' => $index,
                    'is_active' => true,
                ]
            );
        }
        
        // 10. Create Default Navigation Menus (Manajemen Menu & Submenu)
        $menusData = [
            'PROFIL' => [
                ['title' => 'Struktur Organisasi', 'url' => '/page/struktur-organisasi'],
                ['title' => 'Visi dan Misi', 'url' => '/page/visi-dan-misi'],
                ['title' => 'Tugas dan Fungsi', 'url' => '/page/tugas-dan-fungsi'],
                ['title' => 'Profil Pejabat', 'url' => '/page/profil-pejabat'],
                ['title' => 'Survei Kepuasan Masyarakat', 'url' => '/page/survei-kepuasan-masyarakat'],
            ],
            'LAYANAN' => [
                ['title' => 'Standar Pelayanan Publik', 'url' => '/page/standar-pelayanan-publik'],
                ['title' => 'Layanan Pengaduan & Informasi', 'url' => '/page/layanan-pengaduan-informasi'],
            ],
            'DOKUMEN' => [
                ['title' => 'Perencanaan Kinerja', 'url' => '/dokumen/perencanaan-kinerja'],
                ['title' => 'Pengukuran Kinerja', 'url' => '/dokumen/pengukuran-kinerja'],
                ['title' => 'Pelaporan Kinerja', 'url' => '/dokumen/pelaporan-kinerja'],
            ],
            'INFORMASI' => [
                ['title' => 'Berita', 'url' => '/posts'],
                ['title' => 'PPID', 'url' => '/page/ppid'],
                ['title' => 'Galeri Foto & Video', 'url' => '/galleries'],
            ],
            'HUBUNGI' => [
                ['title' => 'Kontak Resmi', 'url' => '/kontak'],
                ['title' => 'Lapor SP4N', 'url' => 'https://www.lapor.go.id'],
            ]
        ];

        $orderGroup = 1;
        foreach ($menusData as $parentTitle => $children) {
            $parentMenu = \App\Models\Menu::firstOrCreate(
                ['title' => $parentTitle, 'parent_id' => null],
                ['order_index' => $orderGroup++, 'is_active' => true]
            );

            $orderChild = 1;
            foreach ($children as $child) {
                \App\Models\Menu::firstOrCreate(
                    ['title' => $child['title'], 'parent_id' => $parentMenu->id],
                    ['url' => $child['url'], 'order_index' => $orderChild++, 'is_active' => true]
                );
            }
        }

        $this->command->info('Database berhasil di-seed: Role, Permission, Akun, Kategori, Halaman Statis, Pengaturan, Instagram Feed, & Manajemen Menu.');
    }
}
