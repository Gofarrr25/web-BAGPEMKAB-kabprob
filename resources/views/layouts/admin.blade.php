<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=5.0">
    <title>@yield('title', 'Admin Panel - Diskominfo Probolinggo')</title>
    
    {{-- Favicon --}}
    @php
        $faviconUrl = (isset($siteSettings['site_logo']) && $siteSettings['site_logo']) 
            ? asset('storage/' . $siteSettings['site_logo']) . '?v=' . time()
            : asset('favicon.png') . '?v=' . time();
    @endphp
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        /* Hide scrollbar for sidebar */
        #adminSidebar nav {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        #adminSidebar nav::-webkit-scrollbar {
            display: none; /* Chrome, Safari and Opera */
        }
    
    /* SweetAlert Compact Style */
    .compact-swal {
        width: 360px !important;
        padding: 1.25rem 1rem 1rem !important;
        border-radius: 1rem !important;
    }
    .compact-swal .swal2-icon {
        transform: scale(0.65);
        margin: 0 auto 0.5rem auto !important;
    }
    .compact-swal .swal2-title {
        font-size: 1.15rem !important;
        margin-bottom: 0.25rem !important;
        padding: 0 !important;
    }
    .compact-swal .swal2-html-container {
        font-size: 0.85rem !important;
        margin: 0.25rem 0 0.5rem 0 !important;
    }
    .compact-swal .swal2-actions {
        margin-top: 1rem !important;
        gap: 0.5rem;
    }
    .compact-swal .swal2-styled {
        padding: 0.5rem 1.25rem !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        border-radius: 0.5rem !important;
        margin: 0 !important;
    }
    
    /* CKEditor Responsive Fix */
    .ck-editor__editable img { max-width: 100% !important; height: auto !important; }
    .ck-editor__editable table { width: 100% !important; max-width: 100% !important; display: block; overflow-x: auto; }
    .ck-editor__editable { overflow-wrap: break-word; word-wrap: break-word; }
</style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Nunito', 'sans-serif'] },
                    colors: { 
                        brand: { dark: '#1a365d', green: '#849f73', light: '#f4f6f9' },
                        'brand-blue': {
                            DEFAULT: '#1a365d',
                            hover: '#112b4d',
                            light: '#eff6ff',
                            'light-hover': '#dbeafe',
                            pale: '#93c5fd',
                        }
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')

    </head>
<body class="bg-brand-light text-gray-800 font-sans antialiased overflow-hidden flex h-screen">

    <!-- Mobile Backdrop -->
    <div id="sidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-30 hidden xl:hidden transition-opacity"></div>

    <!-- Sidebar -->
    <aside id="adminSidebar" class="bg-brand-dark text-white w-64 flex-shrink-0 flex flex-col transition-all duration-300 fixed xl:relative inset-y-0 left-0 -translate-x-full xl:translate-x-0 z-40 shadow-xl overflow-hidden">
        
        <!-- Logo Area -->
        <div class="h-20 flex items-center justify-center border-b border-white/10 px-4 gap-2">
            @if(isset($siteSettings['site_logo']) && $siteSettings['site_logo'])
                <img src="{{ asset('storage/' . $siteSettings['site_logo']) }}" 
                     class="h-10 max-w-full object-contain" alt="Logo">
            @else
                <img src="https://diskominfo.probolinggokab.go.id/backend/gambar/logo_backend.png" 
                     class="h-10 transition-all filter brightness-0 invert" alt="Logo">
            @endif
            <div class="flex flex-col sidebar-text">
                <span class="font-extrabold text-white text-xs uppercase tracking-tight leading-tight">
                    {{ $siteSettings['site_name'] ?? 'Bagian Pemerintahan' }}
                </span>
                <span class="text-[9px] text-gray-300 font-semibold uppercase tracking-wider">
                    Admin Panel
                </span>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
            
            <p class="px-3 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-4 sidebar-text">Menu Utama</p>
            
            <a href="/admin/dashboard" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->is('admin/dashboard') ? 'bg-white/10 text-white font-bold' : '' }}">
                <i class="fas fa-home w-6 text-center text-lg"></i>
                <span class="ml-3 font-semibold text-sm sidebar-text">Dashboard</span>
            </a>

            <!-- Content Management (Semua Admin) -->
            <div x-data="{ open: {{ request()->routeIs('admin.posts.*', 'admin.categories.*', 'admin.documents.*', 'admin.galleries.*', 'admin.banners.*', 'admin.instagram.*', 'admin.pages.*', 'admin.contact-settings.*', 'admin.content-activities.*', 'admin.organization-members.*') ? 'true' : 'false' }} }" class="mt-6">
                
                <!-- Toggle Button -->
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-gray-400 hover:text-white transition-colors focus:outline-none sidebar-text mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Manajemen Konten</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="{'rotate-180': open}"></i>
                </button>
                
                <!-- Collapsible Submenus -->
                <div x-show="open" 
                      
                     class="space-y-1 overflow-hidden transition-all duration-300"
                     x-transition:enter="transition-all ease-in-out duration-300"
                     x-transition:enter-start="opacity-0 max-h-0"
                     x-transition:enter-end="opacity-100 max-h-screen"
                     x-transition:leave="transition-all ease-in-out duration-300"
                     x-transition:leave-start="opacity-100 max-h-screen"
                     x-transition:leave-end="opacity-0 max-h-0">
                     
                    <a href="{{ route('admin.posts.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.posts.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                        <i class="fas fa-newspaper w-6 text-center text-lg"></i>
                        <span class="ml-3 font-semibold text-sm sidebar-text">Berita & Artikel</span>
                    </a>
                    
                    @unlessrole('Staf')
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                        <i class="fas fa-tags w-6 text-center text-lg"></i>
                        <span class="ml-3 font-semibold text-sm sidebar-text">Kategori Berita</span>
                    </a>
                    @endunlessrole

                    <a href="{{ route('admin.documents.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.documents.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                        <i class="fas fa-file-pdf w-6 text-center text-lg"></i>
                        <span class="ml-3 font-semibold text-sm sidebar-text">Dokumen PPID</span>
                    </a>

                    <a href="{{ route('admin.galleries.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.galleries.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                        <i class="fas fa-images w-6 text-center text-lg"></i>
                        <span class="ml-3 font-semibold text-sm sidebar-text">Galeri & Video</span>
                    </a>

                    @unlessrole('Staf')
                    <a href="{{ route('admin.instagram.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.instagram.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                        <i class="fab fa-instagram w-6 text-center text-lg text-pink-400"></i>
                        <span class="ml-3 font-semibold text-sm sidebar-text">Instagram Feed</span>
                    </a>

                    <a href="{{ route('admin.pages.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.pages.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                        <i class="fas fa-file-alt w-6 text-center text-lg"></i>
                        <span class="ml-3 font-semibold text-sm sidebar-text">Daftar Halaman</span>
                    </a>
                    
                    <a href="{{ route('admin.contact-settings.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.contact-settings.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                        <i class="fas fa-address-book w-6 text-center text-lg"></i>
                        <span class="ml-3 font-semibold text-sm sidebar-text">Kontak</span>
                    </a>
                    @endunlessrole
                    
                    <a href="{{ route('admin.content-activities.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.content-activities.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                        <i class="fas fa-history w-6 text-center text-lg"></i>
                        <span class="ml-3 font-semibold text-sm sidebar-text">Riwayat Aktivitas Konten</span>
                    </a>
                    @unlessrole('Staf')
                    <a href="{{ route('admin.organization-members.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.organization-members.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                        <i class="fas fa-sitemap w-6 text-center text-lg"></i>
                        <span class="ml-3 font-semibold text-sm sidebar-text">Struktur Organisasi</span>
                    </a>
                    @endunlessrole
                </div>
            </div>
            <!-- Konten Beranda (Semua Admin) -->
            @unlessrole('Staf')
            <div x-data="{ open: {{ request()->routeIs('admin.banners.*', 'admin.home-widgets.*', 'admin.related-links.*', 'admin.survey-settings.*', 'admin.external-links.*') ? 'true' : 'false' }} }" class="mt-6 mb-4">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-gray-400 hover:text-white transition-colors focus:outline-none sidebar-text mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Konten Beranda</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="{'rotate-180': open}"></i>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="space-y-1" style="display: none;">
                    
                    <a href="{{ route('admin.banners.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.banners.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                        <i class="fas fa-image w-6 text-center text-lg"></i>
                        <span class="ml-3 font-semibold text-sm sidebar-text">Banner Slider</span>
                    </a>

                    <a href="{{ route('admin.home-widgets.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.home-widgets.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                        <i class="fas fa-th-large w-6 text-center text-lg"></i>
                        <span class="ml-3 font-semibold text-sm sidebar-text">Widget</span>
                    </a>
                    
                    <a href="{{ route('admin.related-links.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.related-links.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                        <i class="fas fa-link w-6 text-center text-lg"></i>
                        <span class="ml-3 font-semibold text-sm sidebar-text">Link Terkait</span>
                    </a>
                    
                    <a href="{{ route('admin.survey-settings.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.survey-settings.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                        <i class="fas fa-qrcode w-6 text-center text-lg"></i>
                        <span class="ml-3 font-semibold text-sm sidebar-text">Links Survey</span>
                    </a>
                    
                    <a href="{{ route('admin.external-links.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.external-links.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                        <i class="fas fa-external-link-alt w-6 text-center text-lg"></i>
                        <span class="ml-3 font-semibold text-sm sidebar-text">Tautan Eksternal</span>
                    </a>
                </div>
            </div>
            @endunlessrole

            <!-- System Control (Superadmin Eksklusif) -->
            @role('Superadmin')
            <p class="px-3 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6 sidebar-text">Sistem Kontrol</p>
            
            <a href="{{ route('admin.menus.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.menus.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                <i class="fas fa-sitemap w-6 text-center text-lg text-yellow-400"></i>
                <span class="ml-3 font-semibold text-sm sidebar-text">Manajemen Menu Navigasi</span>
            </a>

            <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-red-500 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-red-500 text-white font-bold' : '' }}">
                <i class="fas fa-users-cog w-6 text-center text-lg"></i>
                <span class="ml-3 font-semibold text-sm sidebar-text">Manajemen Admin</span>
            </a>

            <a href="{{ route('admin.activity-logs.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.activity-logs.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                <i class="fas fa-history w-6 text-center text-lg"></i>
                <span class="ml-3 font-semibold text-sm sidebar-text">Log Aktivitas</span>
            </a>

            <a href="{{ route('admin.settings.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.home-widgets.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                <i class="fas fa-cogs w-6 text-center text-lg"></i>
                <span class="ml-3 font-semibold text-sm sidebar-text">Pengaturan Global</span>
            </a>
            @endrole
        </nav>

        <!-- Footer Sidebar -->
        <div class="p-4 border-t border-white/10 text-xs text-center text-gray-400 sidebar-text">
            &copy; 2026 Bagian Pemerintahan<br>Kab. Probolinggo
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <!-- Header -->
        <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-6 shadow-sm z-50 relative">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-700 focus:outline-none p-2 rounded-md hover:bg-gray-100">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h2 class="text-xl font-bold text-gray-800 hidden md:block">@yield('page_title', 'Dashboard Panel')</h2>
            </div>

            <!-- User Menu -->
            <div class="flex items-center gap-6 relative">
                
                <a href="/" target="_blank" class="text-sm font-semibold text-brand-blue hover:text-brand-blue-hover transition flex items-center gap-2 bg-brand-blue-light px-3 py-2 rounded-md">
                    <i class="fas fa-external-link-alt"></i> Lihat Website
                </a>

                <div class="relative z-[9999]">
                    @php
                        $user = Auth::user();
                        $name = $user ? $user->name : 'Admin';
                        $role = $user ? $user->getRoleNames()->first() : 'Staff';
                        
                        $initials = '';
                        $words = explode(' ', $name);
                        foreach ($words as $w) {
                            if (mb_strlen($w) > 0) {
                                $initials .= mb_substr($w, 0, 1);
                            }
                            if (mb_strlen($initials) >= 2) break;
                        }
                        $initials = strtoupper($initials);
                    @endphp
                    <!-- Tombol Klik Dropdown -->
                    <button onclick="console.log('Avatar clicked → YES'); toggleUserDropdown(event)" class="flex items-center gap-3 focus:outline-none hover:bg-gray-50 p-2 rounded-lg transition relative z-[10000]">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-gray-800">{{ $name }}</p>
                            <p class="text-xs text-gray-500">{{ $role }}</p>
                        </div>
                        @php
                            $hasPhoto = false;
                            $v = '1';
                            if ($user && !empty($user->profile_photo_path)) {
                                $photoFile = storage_path('app/public/' . $user->profile_photo_path);
                                if (file_exists($photoFile)) {
                                    $hasPhoto = true;
                                    $v = filemtime($photoFile);
                                }
                            }
                        @endphp
                        @if($hasPhoto)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}?v={{ $v }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover shadow-md border-2 border-white pointer-events-none">
                        @else
                            <div class="w-10 h-10 bg-brand-green rounded-full flex items-center justify-center text-white font-bold shadow-md border-2 border-white pointer-events-none">
                                {{ $initials }}
                            </div>
                        @endif
                        <i class="fas fa-chevron-down text-xs text-gray-400 pointer-events-none"></i>
                    </button>

                    <!-- Dropdown Content -->
                    <div id="userDropdown" class="hidden absolute right-0 mt-3 w-48 bg-white border border-gray-100 shadow-xl rounded-md overflow-hidden z-[10000]">
                        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                            <p class="text-sm text-gray-800 font-bold">Login sebagai:</p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? 'admin@email.com' }}</p>
                        </div>
                        <a href="{{ route('admin.profile.edit') }}" onclick="console.log('Profil clicked → YES');" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand-blue-hover transition relative z-[10001]">
                            <i class="fas fa-user-circle mr-2"></i> Profil Saya
                        </a>
                        <a href="{{ route('logout') }}" onclick="console.log('Logout clicked → YES'); event.preventDefault(); console.log('Logout form submitted → YES'); document.getElementById('logout-form').submit();" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-semibold transition relative z-[10001]">
                            <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                        </a>
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Scrollable Area -->
        <main class="flex-1 overflow-y-auto bg-brand-light p-4 md:p-6">
            
            <!-- SweetAlert Notification -->
            @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        customClass: { popup: 'compact-swal' },
                        icon: 'success',
                        title: 'Berhasil!',
                        text: "{{ session('success') }}",
                        showConfirmButton: false,
                        timer: 3000
                    });
                });
            </script>
            @endif

            @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        customClass: { popup: 'compact-swal' },
                        icon: 'error',
                        title: 'Terjadi Kesalahan!',
                        text: "{{ session('error') }}",
                        showConfirmButton: true
                    });
                });
            </script>
            @endif

            <!-- Dynamic Content -->
            @yield('content')
            
        </main>
    </div>

    <!-- Script Native Anti-Gagal -->
    <script>
        // Fungsi Buka Tutup Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const texts = document.querySelectorAll('.sidebar-text');
            const backdrop = document.getElementById('sidebarBackdrop');
            
            // Mobile behavior
            if (window.innerWidth < 1280) {
                if (sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.remove('-translate-x-full');
                    if (backdrop) backdrop.classList.remove('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    if (backdrop) backdrop.classList.add('hidden');
                }
                return;
            }

            // Desktop behavior
            if (sidebar.classList.contains('w-64')) {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20');
                texts.forEach(el => el.style.display = 'none');
            } else {
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-64');
                setTimeout(() => {
                    texts.forEach(el => el.style.display = 'block');
                }, 150);
            }
        }

        // Close sidebar on resize if going from mobile to desktop
        window.addEventListener('resize', () => {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (window.innerWidth >= 1280) {
                sidebar.classList.remove('-translate-x-full');
                if (backdrop) backdrop.classList.add('hidden');
            } else {
                // if it was open on desktop but now mobile, hide it to prevent blocking screen
                if (!sidebar.classList.contains('-translate-x-full') && backdrop && backdrop.classList.contains('hidden')) {
                     sidebar.classList.add('-translate-x-full');
                }
            }
        });

        // Fungsi Buka Tutup User Menu (Top Right)
                function toggleUserDropdown(event) {
            event.stopPropagation();
            const menu = document.getElementById('userDropdown');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                menu.style.display = 'block'; 
               
                
                // Deep Debugging
                setTimeout(() => {
                    const rect = menu.getBoundingClientRect();
                    const style = window.getComputedStyle(menu);
                    
                    const aTag = menu.querySelector('a');
                    if(aTag) {
                        const aRect = aTag.getBoundingClientRect();
                        const aStyle = window.getComputedStyle(aTag);
                    }
                }, 50);
            } else {
                menu.classList.add('hidden');
                menu.style.display = 'none';
            }
        }

        // Universal Password Visibility Toggle
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId) || (btn ? (btn.previousElementSibling || btn.parentElement.querySelector('input')) : null);
            if (!input) return;
            const icon = btn ? btn.querySelector('i') : null;
            
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('fa-eye', 'bi-eye-slash', 'text-gray-400');
                    icon.classList.add('fa-eye-slash', 'bi-eye', 'text-brand-blue');
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('fa-eye-slash', 'bi-eye', 'text-brand-blue');
                    icon.classList.add('fa-eye', 'bi-eye-slash', 'text-gray-400');
                }
            }
        }

        // Tutup dropdown jika klik di luar area
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('userDropdown');
            if (menu && !menu.classList.contains('hidden')) {
                if (!menu.contains(event.target)) {
                    menu.classList.add('hidden');
                    menu.style.display = 'none';
                }
            }
        });

        



        // Global Delete Confirmation Function
        function confirmDelete(formElement, moduleName, itemName, hasFile = false) {
            let htmlText = 'Apakah Anda yakin ingin menghapus data <strong>"' + itemName + '"</strong>?';
            
            if (hasFile) {
                htmlText += '<br><br><span style="color: #d97706; font-size: 0.85em;"><i class="fas fa-exclamation-triangle"></i> Peringatan: File/gambar fisik yang terkait dengan data ini juga akan dihapus secara permanen dari server.</span>';
            }

            Swal.fire({
                        customClass: { popup: 'compact-swal' },
                title: 'Yakin hapus data?',
                html: htmlText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#0d6efd',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    formElement.submit();
                }
            });
        }

        // Global Delete Confirmation Function (AJAX/Fetch)
        function confirmAjaxDelete(callback, itemName = 'Item Ini') {
            Swal.fire({
                customClass: { popup: 'compact-swal' },
                title: 'Yakin hapus data?',
                html: 'Apakah Anda yakin ingin menghapus <strong>"' + itemName + '"</strong>?<br><span style="color: #d97706; font-size: 0.85em;"><i class="fas fa-exclamation-triangle"></i> Data ini akan dihapus secara permanen.</span>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#0d6efd',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        }

        // Global Toggle Confirmation Function
        function confirmToggle(formElement) {
            Swal.fire({
                        customClass: { popup: 'compact-swal' },
                title: 'Konfirmasi Status',
                text: 'Apakah Anda yakin ingin mengubah status aktif/nonaktif data ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Ubah',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    formElement.submit();
                }
            });
        }
    </script>
    @stack('scripts')

        <script src="{{ asset('js/file-upload-validator.js') }}"></script>
</body>
</html>









