<!DOCTYPE html>
<html lang="id" class="overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=5.0">
    <title>@yield('title', $siteSettings['site_name'] ?? 'Bagian Pemerintahan Kabupaten Probolinggo')</title>
    @stack('meta')
    
    {{-- Favicon --}}
    @php
        $faviconUrl = (isset($siteSettings['site_logo']) && $siteSettings['site_logo']) 
            ? asset('storage/' . $siteSettings['site_logo']) . '?v=' . time()
            : asset('favicon.png') . '?v=' . time();
    @endphp
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&family=Poppins:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
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
    <style>
        body { font-family: 'Nunito', sans-serif; transition: font-size 0.2s ease, letter-spacing 0.2s ease, line-height 0.2s ease, font-weight 0.2s ease, filter 0.2s ease, background-color 0.2s ease, color 0.2s ease; }
        @media (hover: hover) {
            .dropdown-wrapper:hover .dropdown-menu {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
            }
        }
        
        /* Accessibility Styles */
        .acc-highlight-titles h1, .acc-highlight-titles h2, .acc-highlight-titles h3, .acc-highlight-titles h4, .acc-highlight-titles h5, .acc-highlight-titles h6 {
            border: 2px solid #FF5722 !important;
            padding: 2px !important;
        }
        .acc-highlight-links a {
            background-color: #FFEB3B !important;
            color: #000 !important;
            text-decoration: underline !important;
            font-weight: bold !important;
        }
        .acc-dyslexia * {
            font-family: 'OpenDyslexic', 'Comic Sans MS', sans-serif !important;
        }
        .acc-letter-spacing-1 { letter-spacing: 0.05em !important; }
        .acc-letter-spacing-2 { letter-spacing: 0.1em !important; }
        .acc-letter-spacing-3 { letter-spacing: 0.15em !important; }
        
        .acc-line-height-1 { line-height: 1.5 !important; }
        .acc-line-height-2 { line-height: 1.75 !important; }
        .acc-line-height-3 { line-height: 2.0 !important; }
        
        .acc-font-weight-1 * { font-weight: 500 !important; }
        .acc-font-weight-2 * { font-weight: 600 !important; }
        .acc-font-weight-3 * { font-weight: 700 !important; }
        
        .acc-contrast-dark { background-color: #121212 !important; color: #fff !important; }
        .acc-contrast-dark * { background-color: #121212 !important; color: #fff !important; border-color: #444 !important; }
        .acc-contrast-light { background-color: #fff !important; color: #000 !important; }
        .acc-contrast-light * { background-color: #fff !important; color: #000 !important; border-color: #ccc !important; }
        .acc-contrast-high { filter: contrast(150%) !important; }
        
        .acc-saturation-high { filter: saturate(200%) !important; }
        .acc-saturation-low { filter: saturate(50%) !important; }
        .acc-saturation-monochrome { filter: grayscale(100%) !important; }
        
        .acc-stop-animations * { animation: none !important; transition: none !important; scroll-behavior: auto !important; }
        
        .acc-large-cursor, .acc-large-cursor * { cursor: url('https://cdn.iconscout.com/icon/free/png-256/cursor-1438980-1214470.png'), auto !important; }
        
        /* CKEditor 5 Frontend Styles */
        .text-align-left { text-align: left !important; }
        .text-align-center { text-align: center !important; }
        .text-align-right { text-align: right !important; }
        .text-align-justify { text-align: justify !important; }
        
        figure.image { margin: 1.5rem auto; display: table; max-width: 100%; }
        figure.image img { max-width: 100%; height: auto; display: block; margin: 0 auto; }
        figure.image figcaption { text-align: center; font-size: 0.875rem; color: #6b7280; padding-top: 0.5rem; }
        
        .image-style-align-left { float: left; margin: 0 1.5rem 1.5rem 0 !important; }
        .image-style-align-right { float: right; margin: 0 0 1.5rem 1.5rem !important; }
        .image-style-align-center { display: block; margin-left: auto !important; margin-right: auto !important; text-align: center; }
        .image-style-side { float: right; margin: 0 0 1.5rem 1.5rem !important; max-width: 50%; }
        
        .prose table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; display: block; overflow-x: auto; }
        .prose table th, .prose table td { border: 1px solid #e5e7eb; padding: 0.75rem; }
        .prose table th { background-color: #f9fafb; font-weight: bold; }
        .media { margin: 1.5rem auto; text-align: center; display: block; }
        .media iframe { max-width: 100%; display: inline-block; }
        iframe { max-width: 100%; }
        
        /* Make container fully fluid on tablet/laptop to prevent awkward snapping */
        @media (max-width: 1279px) {
            .container { max-width: 100% !important; }
        }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen overflow-x-hidden">

    <!-- Wrapper Khusus untuk Filter Accessibility agar posisi fixed tidak rusak -->
    <div id="acc-content-wrapper" class="flex flex-col min-h-screen w-full transition-all duration-300">

    <!-- Header Navbar -->
    <header class="bg-white sticky top-0 z-50 border-b border-gray-100 shadow-sm">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between min-h-[5rem] py-2 lg:h-20 lg:py-0 w-full gap-2 sm:gap-4">
                
                <div class="flex-shrink flex-grow min-w-0 mr-1 sm:mr-2">
                    <a href="/" class="flex items-center gap-2 sm:gap-3">
                        @if(isset($siteSettings['site_logo']) && $siteSettings['site_logo'])
                            <img src="{{ asset('storage/' . $siteSettings['site_logo']) }}" class="h-9 sm:h-10 md:h-12 w-auto object-contain flex-shrink-0" alt="Logo">
                        @else
                            <img src="https://diskominfo.probolinggokab.go.id/backend/gambar/logo_frontend.png" class="h-9 sm:h-10 md:h-12 w-auto object-contain flex-shrink-0" alt="Logo">
                        @endif
                        <div class="flex flex-col min-w-0">
                            <span class="font-extrabold text-gray-900 text-[10px] sm:text-xs md:text-sm lg:text-base leading-tight tracking-tight uppercase whitespace-normal xl:whitespace-nowrap line-clamp-2">
                                {{ $siteSettings['site_name'] ?? 'BAGIAN PEMERINTAHAN KABUPATEN PROBOLINGGO' }}
                            </span>
                            <span class="text-[8px] sm:text-[10px] md:text-xs text-gray-500 font-bold tracking-wider uppercase whitespace-normal xl:whitespace-nowrap line-clamp-2 mt-0.5">
                                Sekretariat Daerah Kab. Probolinggo
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Tengah: Menu Navigasi (Hasil CRUD Manajemen Menu & Submenu) -->
                <nav class="hidden xl:flex items-center gap-6">
                    <a href="/" class="text-gray-600 hover:text-brand-blue-hover font-semibold text-sm uppercase tracking-wide">HOME</a>

                    @if(isset($headerNavMenus) && count($headerNavMenus) > 0)
                        @foreach($headerNavMenus as $menuItem)
                            @if(isset($menuItem->children) && count($menuItem->children) > 0)
                                <!-- Menu Utama yang memiliki Submenu Dropdown -->
                                <div class="relative py-4 dropdown-wrapper">
                                    <button onclick="toggleFrontendMenu(event, 'menu-{{ $menuItem->id }}')" class="text-gray-600 hover:text-brand-blue-hover font-semibold text-sm uppercase tracking-wide flex items-center focus:outline-none">
                                        @if($menuItem->icon)<i class="{{ $menuItem->icon }} mr-1.5 text-brand-blue"></i>@endif
                                        {{ $menuItem->title }} <i class="fas fa-chevron-down text-[10px] ml-1 pointer-events-none"></i>
                                    </button>
                                    <div id="menu-{{ $menuItem->id }}" class="dropdown-menu hidden absolute left-0 top-full mt-0 w-72 bg-white border border-gray-100 shadow-lg rounded-b-md z-50 max-h-[75vh] overflow-y-auto overflow-x-hidden" style="scrollbar-width: thin;">
                                        <ul class="py-2">
                                            @foreach($menuItem->children as $child)
                                                @if(isset($child->children) && count($child->children) > 0)
                                                    <li class="relative group/sub">
                                                        <a href="{{ $child->link_url ?? '#' }}" class="flex justify-between items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand-blue-hover">
                                                            <span>@if($child->icon)<i class="{{ $child->icon }} mr-1.5 text-gray-400"></i>@endif {{ $child->title }}</span>
                                                            <i class="fas fa-chevron-down text-[10px] text-gray-400"></i>
                                                        </a>
                                                        <!-- Sub-dropdown Level 3 -->
                                                        <ul class="relative left-0 top-0 mt-0 w-full bg-gray-50/50 border-t border-gray-100 hidden group-hover/sub:block z-50">
                                                            @foreach($child->children as $grandchild)
                                                                <li>
                                                                    <a href="{{ $grandchild->link_url ?? '#' }}" target="{{ $grandchild->target }}" class="block px-8 py-2 text-sm text-gray-700 hover:bg-white hover:text-brand-blue-hover">
                                                                        @if($grandchild->icon)<i class="{{ $grandchild->icon }} mr-1.5 text-gray-400"></i>@endif {{ $grandchild->title }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a href="{{ $child->link_url ?? '#' }}" target="{{ $child->target }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand-blue-hover">
                                                            @if($child->icon)<i class="{{ $child->icon }} mr-1.5 text-gray-400"></i>@endif
                                                            {{ $child->title }}
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @else
                                <!-- Single Menu Link -->
                                <a href="{{ $menuItem->link_url }}" target="{{ $menuItem->target }}" class="text-gray-600 hover:text-brand-blue-hover font-semibold text-sm uppercase tracking-wide">
                                    @if($menuItem->icon)<i class="{{ $menuItem->icon }} mr-1.5 text-brand-blue"></i>@endif
                                    {{ $menuItem->title }}
                                </a>
                            @endif
                        @endforeach
                    @else
                        <!-- Standalone Fallback -->
                        <a href="/page/struktur-organisasi" class="text-gray-600 hover:text-brand-blue-hover font-semibold text-sm uppercase tracking-wide">PROFIL</a>
                    @endif

                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-brand-blue-hover font-semibold text-sm uppercase tracking-wide">DASHBOARD</a>
                    @else
                        <a href="/login" class="text-gray-600 hover:text-brand-blue-hover font-semibold text-sm uppercase tracking-wide">LOGIN</a>
                    @endauth
                </nav>

                <div class="flex items-center gap-1.5 sm:gap-4 flex-shrink-0">
                    <div class="block flex-shrink-0">
                        @if(isset($siteSettings['berakhlak_logo']) && $siteSettings['berakhlak_logo'])
                            <img src="{{ asset('storage/' . $siteSettings['berakhlak_logo']) }}" class="h-7 sm:h-8 md:h-10 xl:h-12 w-auto object-contain" alt="BerAKHLAK">
                        @else
                            <img src="https://diskominfo.probolinggokab.go.id/frontend/images/img-berakhlak.png" class="h-7 sm:h-8 md:h-10 xl:h-12 w-auto object-contain" alt="BerAKHLAK Default">
                        @endif
                    </div>
                    <!-- Hamburger Menu Button (Mobile) -->
                    <button onclick="toggleMobileMenu()" class="xl:hidden text-gray-600 hover:text-brand-blue-hover focus:outline-none p-1.5 sm:p-2 rounded-md hover:bg-gray-100 transition flex-shrink-0">
                        <i class="fas fa-bars text-xl sm:text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Navigation (Off-canvas / Dropdown) -->
        <div id="mobileMenu" class="xl:hidden hidden bg-white border-b border-gray-100 shadow-md absolute w-full left-0 top-full max-h-[80vh] overflow-y-auto z-40">
            <nav class="flex flex-col p-4 gap-2">
                <a href="/" class="px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-brand-blue-hover font-bold rounded-lg transition uppercase">Home</a>

                @if(isset($headerNavMenus) && count($headerNavMenus) > 0)
                    @foreach($headerNavMenus as $menuItem)
                        @if(isset($menuItem->children) && count($menuItem->children) > 0)
                            <div class="flex flex-col">
                                <button onclick="toggleMobileSubmenu('mobile-sub-{{ $menuItem->id }}')" class="flex justify-between items-center px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-brand-blue-hover font-bold rounded-lg transition uppercase w-full text-left">
                                    <span>@if($menuItem->icon)<i class="{{ $menuItem->icon }} mr-2 text-brand-blue"></i>@endif{{ $menuItem->title }}</span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                                <div id="mobile-sub-{{ $menuItem->id }}" class="hidden flex-col pl-6 mt-1 gap-1">
                                    @foreach($menuItem->children as $child)
                                        @if(isset($child->children) && count($child->children) > 0)
                                            <div class="flex flex-col">
                                                <button onclick="toggleMobileSubmenu('mobile-sub-sub-{{ $child->id }}')" class="flex justify-between items-center px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50 hover:text-brand-blue-hover font-semibold rounded-lg transition w-full text-left">
                                                    <span>@if($child->icon)<i class="{{ $child->icon }} mr-2 text-gray-400"></i>@endif{{ $child->title }}</span>
                                                    <i class="fas fa-chevron-down text-xs"></i>
                                                </button>
                                                <div id="mobile-sub-sub-{{ $child->id }}" class="hidden flex-col pl-6 mt-1 gap-1 border-l-2 border-gray-100 ml-2">
                                                    @foreach($child->children as $grandchild)
                                                        <a href="{{ $grandchild->link_url ?? '#' }}" target="{{ $grandchild->target }}" class="px-4 py-2 text-sm text-gray-500 hover:text-brand-blue-hover transition">
                                                            @if($grandchild->icon)<i class="{{ $grandchild->icon }} mr-2 text-gray-400"></i>@endif{{ $grandchild->title }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <a href="{{ $child->link_url ?? '#' }}" target="{{ $child->target }}" class="px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50 hover:text-brand-blue-hover font-semibold rounded-lg transition">
                                                @if($child->icon)<i class="{{ $child->icon }} mr-2 text-gray-400"></i>@endif{{ $child->title }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $menuItem->link_url }}" target="{{ $menuItem->target }}" class="px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-brand-blue-hover font-bold rounded-lg transition uppercase">
                                @if($menuItem->icon)<i class="{{ $menuItem->icon }} mr-2 text-brand-blue"></i>@endif{{ $menuItem->title }}
                            </a>
                        @endif
                    @endforeach
                @else
                    <a href="/page/struktur-organisasi" class="px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-brand-blue-hover font-bold rounded-lg transition uppercase">Profil</a>
                @endif
                  @auth
                      <a href="{{ route('admin.dashboard') }}" class="px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-brand-blue-hover font-bold rounded-lg transition uppercase">DASHBOARD</a>
                  @else
                      <a href="/login" class="px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-brand-blue-hover font-bold rounded-lg transition uppercase">LOGIN</a>
                  @endauth
              </nav>
        </div>
    </header>

    <!-- Global Submenu Banner -->
    @if(!request()->is('/'))
        <div class="bg-brand-blue py-12 border-t border-white/10 shadow-inner relative overflow-hidden">
            <!-- Background Image/Pattern Overlay -->
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] pointer-events-none"></div>
            
            <div class="container mx-auto px-4 lg:px-8 relative z-10 flex flex-col items-start justify-center">
                @php
                    $currentPath = '/' . request()->path();
                    $breadcrumb = [];
                    $headerTitle = '';
                    
                    // Fallback title dari @yield atau segment URL terakhir
                    $fallbackTitle = $__env->yieldContent('title', ucwords(str_replace('-', ' ', request()->segment(count(request()->segments())) ?? 'Halaman')));
                    $fallbackTitle = explode(' - ', $fallbackTitle)[0];
                    $fallbackTitle = str_replace('&amp;', '&', $fallbackTitle); // Fix double encoding jika ada
                    
                    // Dapatkan URL saat ini dan bersihkan slash
                    $currentPath = trim(request()->path(), '/');
                    $segments = request()->segments();
                    $prefixPath = count($segments) > 0 ? trim($segments[0], '/') : '';
                    if ($prefixPath === 'page' && isset($segments[1])) {
                        $prefixPath = 'page/' . trim($segments[1], '/');
                    }
                    
                    // Ambil seluruh menu beserta relasi parent-nya
                    $allMenus = \App\Models\Menu::with('parent')->get();
                    $matchingMenus = collect();
                    $prefixMenus = collect();
                    
                    foreach($allMenus as $m) {
                        $menuUrl = $m->link_url;
                        if ($menuUrl) {
                            $menuUrl = str_replace(url('/'), '', $menuUrl);
                            $menuUrl = trim($menuUrl, '/');
                            
                            if ($menuUrl === $currentPath) {
                                $matchingMenus->push($m);
                            }
                            
                            // Untuk fallback halaman detail
                            if ($menuUrl === $prefixPath) {
                                $prefixMenus->push($m);
                            }
                        }
                    }
                    
                    // PRIORITAS UTAMA: Jika ada menu induk & submenu ber-URL sama, WAJIB pilih submenu (parent_id tidak null)
                    $activeMenu = $matchingMenus->whereNotNull('parent_id')->first() ?? $matchingMenus->first();
                    
                    if ($activeMenu) {
                        // WAJIB: Header title 100% mengikuti nama submenu dari database
                        $headerTitle = $activeMenu->title;
                        
                        // Bangun breadcrumb: Langsung Submenu
                        $breadcrumb[] = ['title' => $activeMenu->title, 'url' => '#'];
                    } else {
                        // Jika URL tidak persis cocok dengan menu (misal: halaman detail artikel)
                        // Gunakan submenu dari segment pertama (prefix) sebagai Header
                        $activePrefixMenu = $prefixMenus->whereNotNull('parent_id')->first() ?? $prefixMenus->first();
                        
                        if ($activePrefixMenu) {
                            $headerTitle = $activePrefixMenu->title;
                            
                            // Breadcrumb: Submenu -> Judul Spesifik
                            $breadcrumb[] = ['title' => $activePrefixMenu->title, 'url' => $activePrefixMenu->link_url ?? '#'];
                            $breadcrumb[] = ['title' => $fallbackTitle, 'url' => '#'];
                        } else {
                            $headerTitle = $fallbackTitle;
                            $breadcrumb[] = ['title' => ucwords(str_replace('-', ' ', $prefixPath)), 'url' => '#'];
                            if (count($segments) > 1 && $prefixPath !== $currentPath) {
                                $breadcrumb[] = ['title' => $fallbackTitle, 'url' => '#'];
                            }
                        }
                    }
                @endphp
                
                <!-- Title -->
                <h1 class="text-[24px] font-bold text-white drop-shadow-md mb-2">
                    {{ $headerTitle }}
                </h1>
                
                <!-- Breadcrumb -->
                <nav class="text-white text-[14px] font-normal flex items-center gap-2">
                    <a href="/" class="hover:text-gray-200 transition">Home</a>
                    @hasSection('breadcrumb')
                        @yield('breadcrumb')
                    @else
                        @if($breadcrumb)
                            @foreach($breadcrumb as $crumb)
                                <i class="fas fa-chevron-right text-[10px] text-white mx-1"></i>
                                @if($loop->last)
                                    <span class="text-white font-semibold">{{ $crumb['title'] }}</span>
                                @else
                                    <a href="{{ $crumb['url'] }}" class="hover:text-gray-200 transition">{{ $crumb['title'] }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endif
                </nav>
            </div>
            
            <!-- Optional Right Side Image like screenshot -->
            <div class="absolute right-0 bottom-0 opacity-20 pointer-events-none hidden md:block">
                @if(isset($siteSettings['berakhlak_logo']) && $siteSettings['berakhlak_logo'])
                    <img src="{{ asset('storage/' . $siteSettings['berakhlak_logo']) }}" class="h-32 object-cover grayscale" alt="Background Element">
                @else
                    <img src="https://diskominfo.probolinggokab.go.id/frontend/images/img-berakhlak.png" class="h-32 object-cover grayscale" alt="Background Element">
                @endif
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer Presisi Dinamis -->
    <footer class="bg-brand-blue text-white pt-10 pb-4 mt-12">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-8 mb-8">
                
                <!-- Kolom 1: Logo & Deskripsi Profil Footer -->
                <div class="md:col-span-4 lg:col-span-4">
                    @if(isset($siteSettings['footer_logo']) && $siteSettings['footer_logo'])
                        <img src="{{ asset('storage/' . $siteSettings['footer_logo']) }}" class="h-16 mb-4 object-contain" alt="Logo Footer">
                    @elseif(isset($siteSettings['site_logo']) && $siteSettings['site_logo'])
                        <img src="{{ asset('storage/' . $siteSettings['site_logo']) }}" class="h-16 mb-4 object-contain filter brightness-0 invert" alt="Logo Footer">
                    @else
                        <img src="https://diskominfo.probolinggokab.go.id/backend/gambar/logo_backend.png" class="h-16 mb-4 object-contain filter brightness-0 invert" alt="DISKOMINFO">
                    @endif

                    <div class="text-gray-300 text-sm leading-relaxed prose prose-sm max-w-none prose-p:my-1 prose-a:text-brand-blue-pale prose-invert break-words">
                        {!! $siteSettings['footer_description'] ?? 'Website Resmi Bagian Pemerintahan Sekretariat Daerah Kabupaten Probolinggo. Merupakan media informasi elektronik satu pintu meliputi penyimpanan dan pengelolaan informasi serta pelayanan publik kepada masyarakat.' !!}
                    </div>
                </div>

                <!-- Kolom 2: Links Survey & QR Code SKM -->
                <div class="md:col-span-2 lg:col-span-2">
                    <h3 class="text-xl font-bold text-white mb-4 textToRead md:min-h-[3.5rem] lg:min-h-0">{{ $siteSettings['survey_title'] ?? 'Links Survey' }}</h3>
                    @if(isset($siteSettings['survey_link']) && $siteSettings['survey_link'])
                        <a href="{{ $siteSettings['survey_link'] }}" target="_blank" class="bg-white p-2 rounded-md flex items-center justify-center w-full max-w-[128px] aspect-square mb-2 shadow-lg hover:scale-105 hover:shadow-xl transition-all cursor-pointer border-2 border-transparent hover:border-yellow-400">
                    @else
                        <div class="bg-white p-2 rounded-md flex items-center justify-center w-full max-w-[128px] aspect-square mb-2 shadow-lg">
                    @endif

                        @if(isset($siteSettings['survey_qr_image']) && $siteSettings['survey_qr_image'])
                            <img src="{{ asset('storage/' . $siteSettings['survey_qr_image']) }}" class="w-full h-full object-contain" alt="QR Code Survey">
                        @else
                            <img src="https://diskominfo.probolinggokab.go.id/backend/gambar/qr_code_kominfo.png" class="w-full h-full object-contain" alt="QR Code Survey">
                        @endif

                    @if(isset($siteSettings['survey_link']) && $siteSettings['survey_link'])
                        </a>
                    @else
                        </div>
                    @endif
                </div>

                <!-- Kolom 3: Tautan Eksternal -->
                <div class="md:col-span-2 lg:col-span-2">
                    <h3 class="text-xl font-bold text-white mb-4 textToRead md:min-h-[3.5rem] lg:min-h-0">{{ $siteSettings['external_link_title'] ?? 'Tautan Eksternal' }}</h3>
                    @if(isset($siteSettings['external_link_url']) && $siteSettings['external_link_url'])
                        <a href="{{ $siteSettings['external_link_url'] }}" target="_blank" class="bg-white p-2 rounded-md flex items-center justify-center w-full max-w-[128px] aspect-square mb-2 shadow-lg hover:scale-105 hover:shadow-xl transition-all cursor-pointer border-2 border-transparent hover:border-yellow-400">
                    @else
                        <div class="bg-white p-2 rounded-md flex items-center justify-center w-full max-w-[128px] aspect-square mb-2 shadow-lg">
                    @endif

                        @if(isset($siteSettings['external_link_qr_image']) && $siteSettings['external_link_qr_image'])
                            <img src="{{ asset('storage/' . $siteSettings['external_link_qr_image']) }}" class="w-full h-full object-contain" alt="QR Code Tautan Eksternal">
                        @else
                            <!-- Placeholder QR jika belum diupload -->
                            <img src="https://diskominfo.probolinggokab.go.id/backend/gambar/qr_code_kominfo.png" class="w-full h-full object-contain" alt="QR Code Default">
                        @endif

                    @if(isset($siteSettings['external_link_url']) && $siteSettings['external_link_url'])
                        </a>
                    @else
                        </div>
                    @endif
                </div>

                <!-- Kolom 4: Alamat Kantor & Kontak -->
                <div class="md:col-span-4 lg:col-span-4">
                    <h3 class="text-xl font-bold text-white mb-4 textToRead">Alamat Kantor</h3>
                    <p class="text-gray-300 text-sm mb-4 textToRead break-words">
                        {{ $siteSettings['office_address'] ?? 'Jl. Panglima Sudirman No. 134 lt. 3 - Kraksaan - Probolinggo' }}
                    </p>
                    <ul class="space-y-3 text-sm text-gray-300 textToRead">
                        <li class="flex items-start gap-3"><i class="fas fa-phone-alt mt-1 text-brand-blue-pale min-w-[16px]"></i> <span>{{ $siteSettings['phone'] ?? '0335 844554' }}</span></li>
                        <li class="flex items-start gap-3"><i class="fas fa-envelope mt-1 text-brand-blue-pale min-w-[16px]"></i> <span class="break-all">{{ $siteSettings['email'] ?? 'bagpemerintahan@probolinggokab.go.id' }}</span></li>
                    </ul>

                    @if(!empty($siteSettings['instagram_url']) || !empty($siteSettings['facebook_url']) || !empty($siteSettings['youtube_url']) || !empty($siteSettings['tiktok_url']))
                        <div class="flex items-center gap-3 mt-6">
                            @if(!empty($siteSettings['instagram_url']))
                                <a href="{{ $siteSettings['instagram_url'] }}" target="_blank" class="w-9 h-9 rounded bg-white/10 hover:bg-pink-600 flex items-center justify-center text-white transition shadow"><i class="fab fa-instagram"></i></a>
                            @endif
                            @if(!empty($siteSettings['facebook_url']))
                                <a href="{{ $siteSettings['facebook_url'] }}" target="_blank" class="w-9 h-9 rounded bg-white/10 hover:bg-brand-blue-hover flex items-center justify-center text-white transition shadow"><i class="fab fa-facebook-f"></i></a>
                            @endif
                            @if(!empty($siteSettings['tiktok_url']))
                                <a href="{{ $siteSettings['tiktok_url'] }}" target="_blank" class="w-9 h-9 rounded bg-white/10 hover:bg-black flex items-center justify-center text-white transition shadow"><i class="fab fa-tiktok"></i></a>
                            @endif
                            @if(!empty($siteSettings['youtube_url']))
                                <a href="{{ $siteSettings['youtube_url'] }}" target="_blank" class="w-9 h-9 rounded bg-white/10 hover:bg-red-600 flex items-center justify-center text-white transition shadow"><i class="fab fa-youtube"></i></a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="border-t border-white/10 pt-6 mt-4 text-center text-sm text-gray-400 flex flex-col md:flex-row justify-between items-center gap-2">
                <span>{{ $siteSettings['footer_copyright'] ?? (strtoupper($siteSettings['site_name'] ?? 'BAGIAN PEMERINTAHAN KABUPATEN PROBOLINGGO') . ' © ' . date('Y') . '. All Rights Reserved') }}</span>
                <span>Powered by Laravel</span>
            </div>
        </div>
    </footer>
    
    </div> <!-- END OF WRAPPER -->

    <!-- Tombol Accessibility & Back to Top -->
    <div class="fixed bottom-4 left-4 md:bottom-6 md:left-6 z-[90] flex flex-col gap-3">
        <button id="btn-accessibility-toggle" class="bg-brand-blue text-white w-12 h-12 rounded-full flex items-center justify-center text-2xl shadow-lg hover:bg-brand-blue-hover transition-transform hover:scale-110">
            <i class="fab fa-accessible-icon"></i>
        </button>
    </div>

    <!-- Accessibility Panel -->
    <div id="accessibility-panel" class="fixed top-0 left-0 h-full w-full max-w-[340px] bg-white shadow-2xl z-[100] transform -translate-x-full transition-transform duration-300 flex flex-col font-sans text-sm border-r border-gray-200">
        <div class="bg-brand-blue text-white flex justify-between items-center px-4 py-3">
            <h2 class="font-bold text-base">Menu Aksesibilitas</h2>
            <div class="flex gap-2">
                <button id="btn-acc-reset" class="bg-white text-brand-blue w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 shadow" title="Reset Semua"><i class="fas fa-undo"></i></button>
                <button id="btn-acc-close" class="bg-white text-brand-blue w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 shadow" title="Tutup"><i class="fas fa-times"></i></button>
            </div>
        </div>
        
        <div class="flex-grow overflow-y-auto p-4 bg-gray-50 text-gray-800">
            <!-- Language (Mock) -->
            <div class="mb-4">
                <select class="w-full border border-gray-300 rounded-md p-2 bg-white font-semibold">
                    <option>Bahasa Indonesia (Indonesian)</option>
                </select>
            </div>

            <!-- Penyesuaian Konten -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-500 mb-2">Penyesuaian Konten</h3>
                
                <div class="bg-white border border-gray-200 rounded-xl p-4 mb-3 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3 font-bold"><i class="fas fa-text-height text-lg"></i> Sesuaikan Ukuran Font</div>
                    <div class="flex items-center gap-4">
                        <button id="btn-acc-font-minus" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200"><i class="fas fa-minus text-xs"></i></button>
                        <span id="acc-font-size-val" class="font-bold w-10 text-center">100%</span>
                        <button id="btn-acc-font-plus" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200"><i class="fas fa-plus text-xs"></i></button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button id="btn-sorot-judul" class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col items-center justify-center gap-3 hover:bg-gray-50 shadow-sm transition">
                        <i class="fas fa-heading text-3xl"></i>
                        <span class="text-xs font-semibold text-center">Sorot Judul</span>
                    </button>
                    <button id="btn-sorot-tautan" class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col items-center justify-center gap-3 hover:bg-gray-50 shadow-sm transition">
                        <i class="fas fa-link text-3xl"></i>
                        <span class="text-xs font-semibold text-center">Sorot Tautan</span>
                    </button>
                    <button id="btn-font-disleksia" class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col items-center justify-center gap-3 hover:bg-gray-50 shadow-sm transition">
                        <i class="fas fa-font text-3xl"></i>
                        <span class="text-xs font-semibold text-center">Font Disleksia</span>
                    </button>
                    <button id="btn-jarak-huruf" class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col items-center justify-center gap-3 hover:bg-gray-50 shadow-sm transition">
                        <span class="font-serif text-3xl font-bold tracking-widest">|A|</span>
                        <span class="text-xs font-semibold text-center">Jarak Huruf</span>
                    </button>
                    <button id="btn-tinggi-baris" class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col items-center justify-center gap-3 hover:bg-gray-50 shadow-sm transition">
                        <i class="fas fa-text-height text-3xl"></i>
                        <span class="text-xs font-semibold text-center">Tinggi Baris</span>
                    </button>
                    <button id="btn-ketebalan-font" class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col items-center justify-center gap-3 hover:bg-gray-50 shadow-sm transition">
                        <i class="fas fa-bold text-3xl"></i>
                        <span class="text-xs font-semibold text-center">Ketebalan Font</span>
                    </button>
                </div>
            </div>

            <!-- Penyesuaian Warna -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-500 mb-2">Penyesuaian Warna</h3>
                <div class="grid grid-cols-3 gap-2">
                    <button id="btn-kontras-gelap" class="bg-white border border-gray-200 rounded-xl p-3 flex flex-col items-center justify-center gap-2 hover:bg-gray-50 shadow-sm transition">
                        <i class="fas fa-moon text-2xl"></i>
                        <span class="text-[10px] font-semibold text-center leading-tight">Kontras Gelap</span>
                    </button>
                    <button id="btn-kontras-terang" class="bg-white border border-gray-200 rounded-xl p-3 flex flex-col items-center justify-center gap-2 hover:bg-gray-50 shadow-sm transition">
                        <i class="fas fa-sun text-2xl"></i>
                        <span class="text-[10px] font-semibold text-center leading-tight">Kontras Terang</span>
                    </button>
                    <button id="btn-kontras-tinggi" class="bg-white border border-gray-200 rounded-xl p-3 flex flex-col items-center justify-center gap-2 hover:bg-gray-50 shadow-sm transition">
                        <i class="fas fa-adjust text-2xl"></i>
                        <span class="text-[10px] font-semibold text-center leading-tight">Kontras Tinggi</span>
                    </button>
                    <button id="btn-saturasi-tinggi" class="bg-white border border-gray-200 rounded-xl p-3 flex flex-col items-center justify-center gap-2 hover:bg-gray-50 shadow-sm transition">
                        <i class="fas fa-tint text-2xl"></i>
                        <span class="text-[10px] font-semibold text-center leading-tight">Saturasi Tinggi</span>
                    </button>
                    <button id="btn-saturasi-rendah" class="bg-white border border-gray-200 rounded-xl p-3 flex flex-col items-center justify-center gap-2 hover:bg-gray-50 shadow-sm transition">
                        <i class="fas fa-tint-slash text-2xl"></i>
                        <span class="text-[10px] font-semibold text-center leading-tight">Saturasi Rendah</span>
                    </button>
                    <button id="btn-monokrom" class="bg-white border border-gray-200 rounded-xl p-3 flex flex-col items-center justify-center gap-2 hover:bg-gray-50 shadow-sm transition">
                        <i class="fas fa-palette text-2xl"></i>
                        <span class="text-[10px] font-semibold text-center leading-tight">Monokrom</span>
                    </button>
                </div>
            </div>

            <!-- Alat -->
            <div class="mb-4">
                <h3 class="text-xs font-semibold text-gray-500 mb-2">Alat</h3>
                <div class="grid grid-cols-3 gap-2">
                    <button id="btn-panduan-membaca" class="bg-white border border-gray-200 rounded-xl p-3 flex flex-col items-center justify-center gap-2 hover:bg-gray-50 shadow-sm transition">
                        <i class="fas fa-book-reader text-2xl"></i>
                        <span class="text-[10px] font-semibold text-center leading-tight">Panduan Membaca</span>
                    </button>
                    <button id="btn-hentikan-animasi" class="bg-white border border-gray-200 rounded-xl p-3 flex flex-col items-center justify-center gap-2 hover:bg-gray-50 shadow-sm transition">
                        <i class="fas fa-pause-circle text-2xl"></i>
                        <span class="text-[10px] font-semibold text-center leading-tight">Hentikan Animasi</span>
                    </button>
                    <button id="btn-kursor-besar" class="bg-white border border-gray-200 rounded-xl p-3 flex flex-col items-center justify-center gap-2 hover:bg-gray-50 shadow-sm transition">
                        <i class="fas fa-mouse-pointer text-2xl"></i>
                        <span class="text-[10px] font-semibold text-center leading-tight">Kursor Besar</span>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="bg-white py-4 text-center text-sm font-bold shadow-md">
            Web Accessibility.
        </div>
    </div>
    
    <button onclick="window.scrollTo(0,0)" class="fixed bottom-6 right-6 bg-white text-gray-800 w-10 h-10 flex items-center justify-center shadow-lg hover:bg-gray-100 z-50">
        <i class="fas fa-chevron-up"></i>
    </button>
    
    <script>
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
            } else {
                mobileMenu.classList.add('hidden');
            }
        }

        function toggleMobileSubmenu(id) {
            const submenu = document.getElementById(id);
            if (submenu.classList.contains('hidden')) {
                submenu.classList.remove('hidden');
                submenu.classList.add('flex');
            } else {
                submenu.classList.add('hidden');
                submenu.classList.remove('flex');
            }
        }

        function toggleFrontendMenu(event, menuId) {
            event.preventDefault();
            event.stopPropagation();
            
            const allMenus = document.querySelectorAll('.dropdown-menu');
            allMenus.forEach(menu => {
                if(menu.id !== menuId) {
                    menu.classList.add('hidden');
                    menu.style.display = 'none';
                }
            });
            
            const targetMenu = document.getElementById(menuId);
            if(targetMenu.classList.contains('hidden')) {
                targetMenu.classList.remove('hidden');
                targetMenu.style.display = 'block';
            } else {
                targetMenu.classList.add('hidden');
                targetMenu.style.display = 'none';
            }
        }

        document.addEventListener('click', function(event) {
            // Abaikan klik di dalam menu itu sendiri
            if (event.target.closest('.dropdown-wrapper')) return;
            
            const allMenus = document.querySelectorAll('.dropdown-menu');
            allMenus.forEach(menu => {
                menu.classList.add('hidden');
                menu.style.display = 'none';
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const proseContainers = document.querySelectorAll('.prose');
            proseContainers.forEach(container => {
                const tables = container.querySelectorAll('table');
                tables.forEach(table => {
                    if (!table.parentElement.classList.contains('overflow-x-auto')) {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'overflow-x-auto w-full';
                        table.parentNode.insertBefore(wrapper, table);
                        wrapper.appendChild(table);
                    }
                });
            });
        });
    </script>

    {{-- Text-to-Speech Bahasa Indonesia (Auto) --}}
    <script src="{{ asset('js/tts-indonesia.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/accessibility.js') }}?v={{ time() }}"></script>

    @stack('scripts')
</body>
</html>
