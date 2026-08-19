<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel - Diskominfo Probolinggo')</title>
    
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

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
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Nunito', 'sans-serif'] },
                    colors: { 
                        brand: { dark: '#003b5c', green: '#849f73', light: '#f4f6f9' },
                        'brand-blue': '#2563eb'
                    }
                }
            }
        }
    </script>
    @stack('styles')
</head>
<body class="bg-brand-light text-gray-800 font-sans antialiased overflow-hidden flex h-screen">

    <!-- Mobile Backdrop -->
    <div id="sidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden transition-opacity"></div>

    <!-- Sidebar -->
    <aside id="adminSidebar" class="bg-brand-dark text-white w-64 flex-shrink-0 flex flex-col transition-all duration-300 fixed lg:relative inset-y-0 left-0 -translate-x-full lg:translate-x-0 z-40 shadow-xl overflow-hidden">
        
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
            <div x-data="{ open: {{ request()->routeIs('admin.posts.*', 'admin.categories.*', 'admin.documents.*', 'admin.galleries.*', 'admin.banners.*', 'admin.instagram.*', 'admin.pages.*', 'admin.contact-settings.*') ? 'true' : 'false' }} }" class="mt-6">
                
                <!-- Toggle Button -->
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-gray-400 hover:text-white transition-colors focus:outline-none sidebar-text mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Manajemen Konten</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="{'rotate-180': open}"></i>
                </button>
                
                <!-- Collapsible Submenus -->
                <div x-show="open" 
                     x-collapse 
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
                    
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                        <i class="fas fa-tags w-6 text-center text-lg"></i>
                        <span class="ml-3 font-semibold text-sm sidebar-text">Kategori Berita</span>
                    </a>

                    <a href="{{ route('admin.documents.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.documents.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                        <i class="fas fa-file-pdf w-6 text-center text-lg"></i>
                        <span class="ml-3 font-semibold text-sm sidebar-text">Dokumen PPID</span>
                    </a>

                    <a href="{{ route('admin.galleries.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.galleries.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                        <i class="fas fa-images w-6 text-center text-lg"></i>
                        <span class="ml-3 font-semibold text-sm sidebar-text">Galeri & Video</span>
                    </a>
                    
                    <a href="{{ route('admin.banners.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.banners.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                        <i class="fas fa-image w-6 text-center text-lg"></i>
                        <span class="ml-3 font-semibold text-sm sidebar-text">Banner Slider</span>
                    </a>

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
                </div>
            </div>

            <!-- System Control (Superadmin Eksklusif) -->
            @role('Superadmin')
            <a href="{{ route('admin.organization-members.index') }}" class="flex items-center px-3 py-3 text-gray-300 hover:bg-white/10 hover:text-white rounded-lg transition-colors {{ request()->routeIs('admin.organization-members.*') ? 'bg-white/10 text-white font-bold' : '' }}">
                <i class="fas fa-sitemap w-6 text-center text-lg"></i>
                <span class="ml-3 font-semibold text-sm sidebar-text">Struktur Organisasi</span>
            </a>

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
            &copy; 2026 Diskominfo<br>Kab. Probolinggo
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <!-- Header -->
        <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-6 shadow-sm z-10 relative">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-700 focus:outline-none p-2 rounded-md hover:bg-gray-100">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h2 class="text-xl font-bold text-gray-800 hidden md:block">@yield('page_title', 'Dashboard Panel')</h2>
            </div>

            <!-- User Menu -->
            <div class="flex items-center gap-6 relative">
                
                <a href="/" target="_blank" class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition flex items-center gap-2 bg-blue-50 px-3 py-2 rounded-md">
                    <i class="fas fa-external-link-alt"></i> Lihat Website
                </a>

                <div class="relative">
                    <!-- Tombol Klik Dropdown -->
                    <button onclick="toggleUserDropdown(event)" class="flex items-center gap-3 focus:outline-none hover:bg-gray-50 p-2 rounded-lg transition">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-gray-800">{{ auth()->user()->name ?? 'Administrator' }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->roles->pluck('name')->first() ?? 'Admin' }}</p>
                        </div>
                        <div class="w-10 h-10 bg-brand-green rounded-full flex items-center justify-center text-white font-bold shadow-md border-2 border-white pointer-events-none">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                        <i class="fas fa-chevron-down text-xs text-gray-400 pointer-events-none"></i>
                    </button>

                    <!-- Dropdown Content (Hidden by default) -->
                    <div id="userDropdown" class="hidden absolute right-0 mt-3 w-48 bg-white border border-gray-100 shadow-xl rounded-md overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                            <p class="text-sm text-gray-800 font-bold">Login sebagai:</p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? 'admin@email.com' }}</p>
                        </div>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition">
                            <i class="fas fa-user-circle mr-2"></i> Profil Saya
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-semibold transition">
                                <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Scrollable Area -->
        <main class="flex-1 overflow-y-auto bg-brand-light p-6">
            
            <!-- Centered Toast Notification -->
            @if(session('success'))
            <div id="toast-success" class="fixed inset-0 flex items-center justify-center z-[9999] pointer-events-none opacity-0 transition-opacity duration-300">
                <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8 flex flex-col items-center justify-center transform scale-90 transition-transform duration-300 w-[90%] max-w-sm pointer-events-auto border border-gray-100 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4 text-green-500">
                        <i class="fas fa-check text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Berhasil!</h3>
                    <p class="text-sm text-gray-500 font-medium">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div id="toast-error" class="fixed inset-0 flex items-center justify-center z-[9999] pointer-events-none opacity-0 transition-opacity duration-300">
                <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8 flex flex-col items-center justify-center transform scale-90 transition-transform duration-300 w-[90%] max-w-sm pointer-events-auto border border-gray-100 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4 text-red-500">
                        <i class="fas fa-times text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Terjadi Kesalahan!</h3>
                    <p class="text-sm text-gray-500 font-medium">{{ session('error') }}</p>
                </div>
            </div>
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
            if (window.innerWidth < 1024) {
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
            if (window.innerWidth >= 1024) {
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
                    icon.classList.add('fa-eye-slash', 'bi-eye', 'text-blue-600');
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('fa-eye-slash', 'bi-eye', 'text-blue-600');
                    icon.classList.add('fa-eye', 'bi-eye-slash', 'text-gray-400');
                }
            }
        }

        // Tutup dropdown jika klik di luar area
        document.addEventListener('click', function() {
            const menu = document.getElementById('userDropdown');
            if (menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
                menu.style.display = 'none';
            }
        });

        // Toast Notification Handler
        document.addEventListener('DOMContentLoaded', function() {
            const showToast = (id) => {
                const toast = document.getElementById(id);
                if (!toast) return;
                
                // Trigger animation
                requestAnimationFrame(() => {
                    toast.classList.remove('opacity-0');
                    toast.classList.add('opacity-100');
                    const innerBox = toast.querySelector('div');
                    if(innerBox) {
                        innerBox.classList.remove('scale-90');
                        innerBox.classList.add('scale-100');
                    }
                });

                // Auto hide after 3 seconds
                setTimeout(() => {
                    toast.classList.remove('opacity-100');
                    toast.classList.add('opacity-0');
                    const innerBox = toast.querySelector('div');
                    if(innerBox) {
                        innerBox.classList.remove('scale-100');
                        innerBox.classList.add('scale-90');
                    }
                    // Remove from DOM after transition completes
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                }, 3000);
            };

            showToast('toast-success');
            showToast('toast-error');
        });
    </script>
    @stack('scripts')
</body>
</html>
