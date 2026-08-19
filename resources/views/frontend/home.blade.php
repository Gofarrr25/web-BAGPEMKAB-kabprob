@extends('layouts.public')

@section('content')
<!-- Hero Slider Carousel (Sesuai Rujukan Diskominfo) -->
<div class="relative w-full h-[450px] md:h-[550px] overflow-hidden bg-gray-900 group" id="heroCarousel">
    
    <!-- Slides Wrapper -->
    <div class="w-full h-full relative" id="carouselSlides">
        @if(isset($banners) && count($banners) > 0)
            @foreach($banners as $index => $banner)
                @php
                    $imgSrc = Str::startsWith($banner->image_path, 'http') ? $banner->image_path : asset('storage/' . $banner->image_path);
                @endphp
                <div class="carousel-slide absolute inset-0 transition-opacity duration-700 ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}" data-index="{{ $index }}">
                    <!-- Background Image dengan Overlay Gradient -->
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $imgSrc }}');">
                        <div class="absolute inset-0 bg-black/40"></div>
                    </div>
                    
                    <!-- Content Container -->
                    <div class="container mx-auto h-full px-6 md:px-16 flex items-center relative z-20">
                        <div class="max-w-3xl text-white space-y-4">
                            @if($index === 0)
                                <h2 class="text-3xl md:text-5xl font-extrabold leading-tight textToRead drop-shadow-md">
                                    Selamat Datang di Website
                                </h2>
                                <h3 class="text-2xl md:text-4xl font-bold text-amber-300 leading-snug textToRead drop-shadow">
                                    {{ $siteSettings['site_name'] ?? 'Bagian Pemerintahan Kabupaten Probolinggo' }}
                                </h3>
                            @else
                                <h2 class="text-2xl md:text-4xl font-extrabold leading-tight textToRead drop-shadow-md">
                                    {{ $banner->title }}
                                </h2>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <!-- Fallback Slide 1 -->
            <div class="carousel-slide absolute inset-0 transition-opacity duration-700 ease-in-out opacity-100 z-10" data-index="0">
                <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://diskominfo.probolinggokab.go.id/slider_img/slider_sae.png');">
                    <div class="absolute inset-0 bg-black/40"></div>
                </div>
                <div class="container mx-auto h-full px-6 md:px-16 flex items-center relative z-20">
                    <div class="max-w-3xl text-white space-y-4">
                        <h2 class="text-3xl md:text-5xl font-extrabold leading-tight textToRead drop-shadow-md">
                            Selamat Datang di Website
                        </h2>
                        <h3 class="text-2xl md:text-4xl font-bold text-amber-300 leading-snug textToRead drop-shadow">
                            {{ $siteSettings['site_name'] ?? 'Bagian Pemerintahan Kabupaten Probolinggo' }}
                        </h3>
                    </div>
                </div>
            </div>
            <!-- Fallback Slide 2 -->
            <div class="carousel-slide absolute inset-0 transition-opacity duration-700 ease-in-out opacity-0 z-0" data-index="1">
                <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://diskominfo.probolinggokab.go.id/slider_img/slider_kadis_hudan.jpg');">
                    <div class="absolute inset-0 bg-black/40"></div>
                </div>
                <div class="container mx-auto h-full px-6 md:px-16 flex items-center relative z-20">
                    <div class="max-w-3xl text-white space-y-4">
                        <h2 class="text-2xl md:text-4xl font-extrabold leading-tight textToRead drop-shadow-md">
                            Bagian Pemerintahan Sekretariat Daerah Kabupaten Probolinggo
                        </h2>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Navigation Arrow Left -->
    <button onclick="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-black/50 hover:bg-black/80 text-white rounded-full flex items-center justify-center text-xl z-30 transition shadow-lg border border-white/20">
        <i class="fas fa-chevron-left"></i>
    </button>

    <!-- Navigation Arrow Right -->
    <button onclick="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-black/50 hover:bg-black/80 text-white rounded-full flex items-center justify-center text-xl z-30 transition shadow-lg border border-white/20">
        <i class="fas fa-chevron-right"></i>
    </button>

    <!-- Slide Indicators -->
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-2 z-30" id="carouselIndicators">
        <!-- Generated by JS -->
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Area 70%: Grid Berita -->
        <div class="lg:w-[70%] bg-white p-6 shadow-sm border border-gray-100 rounded-xl">
            <div class="flex justify-between items-end mb-6 border-b border-gray-100 pb-4">
                <h2 class="text-2xl md:text-3xl font-semibold text-[#1a365d] textToRead">Informasi Terbaru</h2>
                <a href="/informasi" class="text-blue-500 hover:text-blue-700 transition text-sm flex items-center gap-1">Lihat Semua <i class="fas fa-chevron-right text-[10px]"></i></a>
            </div>

            <!-- Grid Berita Dinamis -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(isset($latestPosts) && count($latestPosts) > 0)
                    @foreach($latestPosts as $lPost)
                    <article class="bg-white shadow-sm border border-gray-100 overflow-hidden relative group flex flex-col h-full hover:shadow-md transition">
                        <a href="{{ url('/informasi/' . $lPost->slug) }}" class="flex-grow flex flex-col block">
                            <div class="relative overflow-hidden h-52 w-full bg-gray-100">
                                @if($lPost->image)
                                    <img src="{{ asset('storage/' . $lPost->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-200">
                                        <i class="fas fa-newspaper text-4xl"></i>
                                    </div>
                                @endif
                                
                                @php
                                    $monthsId = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                    $day = $lPost->created_at->format('d');
                                    $monthInt = (int)$lPost->created_at->format('m');
                                    $year = $lPost->created_at->format('Y');
                                    $monthName = $monthsId[$monthInt];
                                @endphp
                                <div class="absolute top-0 left-0 flex shadow">
                                    <div class="bg-[#2563eb] text-white font-bold text-lg px-3 py-1.5 flex items-center justify-center">
                                        {{ $day }}
                                    </div>
                                    <div class="bg-white text-gray-800 font-bold text-[11px] md:text-xs px-3 py-1.5 flex items-center justify-center tracking-wide">
                                        {{ $monthName }} {{ $year }}
                                    </div>
                                </div>
                            </div>
                            <div class="p-5 flex-grow">
                                <h3 class="font-semibold text-[#1a365d] text-lg leading-snug group-hover:text-blue-600 transition textToRead">
                                    {{ $lPost->title }}
                                </h3>
                            </div>
                        </a>
                    </article>
                    @endforeach
                @else
                    <div class="col-span-full py-12 text-center text-gray-400 bg-gray-50 rounded-lg">
                        <i class="fas fa-newspaper text-4xl mb-2 text-gray-300"></i>
                        <p>Belum ada berita dipublikasikan.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Area 30%: Sidebar Widget -->
        <div class="lg:w-[30%] space-y-6">
            <div class="bg-white shadow-sm border border-gray-100 p-6 rounded-xl">
                
                @if(isset($homeWidgets) && count($homeWidgets) > 0)
                    @foreach($homeWidgets as $widget)
                    @php
                        $imgSrc = Str::startsWith($widget->image_path, ['http://', 'https://']) ? $widget->image_path : asset('storage/' . $widget->image_path);
                    @endphp
                    <div class="mb-6 last:mb-0">
                        <h3 class="font-bold text-gray-800 text-lg mb-2 textToRead">{{ $widget->title }}</h3>
                        
                        @if($widget->image_path)
                            @if(!empty($widget->link_url) && $widget->link_url !== '#')
                                <a href="{{ $widget->link_url }}" target="{{ Str::startsWith($widget->link_url, ['http://', 'https://']) ? '_blank' : '_self' }}">
                                    <img src="{{ $imgSrc }}" class="w-full h-auto object-cover border border-gray-200 hover:opacity-90 rounded-lg transition" alt="{{ $widget->title }}">
                                </a>
                            @else
                                <img src="{{ $imgSrc }}" class="w-full h-auto object-cover border border-gray-200 rounded-lg" alt="{{ $widget->title }}">
                            @endif
                        @elseif(!empty($widget->link_url) && $widget->link_url !== '#')
                            <a href="{{ $widget->link_url }}" target="{{ Str::startsWith($widget->link_url, ['http://', 'https://']) ? '_blank' : '_self' }}" class="block w-full text-center px-4 py-3 bg-brand-blue text-white font-bold rounded-lg hover:bg-brand-dark transition shadow-sm text-sm">
                                Buka {{ $widget->title }}
                            </a>
                        @endif
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-6 text-gray-400">
                        <p class="text-xs italic">Belum ada widget</p>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

<!-- GALERI VIDEO SECTION -->
@if(isset($videos) && count($videos) > 0)
<div class="bg-gray-900 py-12 text-white">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="flex items-center justify-between mb-8">
            <div>
                <span class="text-xs font-bold text-red-500 uppercase tracking-widest">DOKUMENTASI DENGAN VIDEO</span>
                <h2 class="text-2xl md:text-3xl font-bold mt-1 textToRead">Galeri Video Kegiatan</h2>
            </div>
            <a href="/galeri-video" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition flex items-center gap-1.5 shadow">
                Lihat Semua Video <i class="fas fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($videos as $vid)
                <div class="bg-gray-800 rounded-xl overflow-hidden shadow-lg border border-gray-700 group cursor-pointer video-home-card" 
                     data-video-id="{{ $vid->youtube_id }}"
                     data-watch-url="{{ $vid->watch_url }}"
                     data-thumbnail="{{ $vid->thumbnail_url }}"
                     data-title="{{ $vid->title }}">
                    <div class="relative w-full h-48 bg-black overflow-hidden">
                        <img src="{{ $vid->thumbnail_url }}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition duration-500" alt="{{ $vid->title }}">
                        <div class="absolute inset-0 flex items-center justify-center bg-black/30 group-hover:bg-black/10 transition">
                            <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center text-white shadow-xl group-hover:scale-110 transition duration-300">
                                <i class="fas fa-play text-lg ml-0.5"></i>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-white text-sm line-clamp-2 group-hover:text-red-400 transition">{{ $vid->title }}</h4>
                        <p class="text-xs text-gray-400 mt-2"><i class="far fa-clock text-red-400 mr-1"></i> {{ $vid->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Logo Carousel Section Bawah (Link Terkait) -->
@if(isset($relatedLinks) && count($relatedLinks) > 0)
<div class="bg-white py-10 border-t border-gray-100 text-center">
    <h3 class="text-center font-bold text-gray-400 uppercase tracking-widest text-xs mb-6">LINK TERKAIT</h3>
    <div class="container mx-auto px-4 flex flex-wrap justify-center gap-8 items-center">
        @foreach($relatedLinks as $link)
            @php 
                $domain = parse_url($link->url, PHP_URL_HOST); 
                $logoUrl = $link->logo_url ? $link->logo_url : "https://logo.clearbit.com/{$domain}";
            @endphp
            <a href="{{ $link->url }}" target="_blank" title="{{ $link->name }}" class="flex items-center justify-center w-48 h-16 md:w-64 md:h-20 filter grayscale hover:grayscale-0 transition opacity-80 hover:opacity-100">
                <img src="{{ $logoUrl }}" 
                     class="w-full h-full object-contain" 
                     alt="{{ $link->name }}"
                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($link->name) }}&background=fff&color=333&size=128';">
            </a>
        @endforeach
    </div>
</div>
@endif

<!-- INSTAGRAM FEED SECTION (REPLIKA DISKOMINFO PROBOLINGGO) -->
<div class="bg-gray-50 py-12 border-t border-gray-200">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            @php
                $igUsername = $siteSettings['instagram_username'] ?? \App\Services\InstagramService::extractUsername($siteSettings['instagram_url'] ?? '');
                $igUrl = !empty($siteSettings['instagram_url']) ? $siteSettings['instagram_url'] : 'https://www.instagram.com/' . $igUsername;
            @endphp

            <!-- WIDGET POSTS GRID ATAU SCRIPT EMBED -->
            <div class="bg-white">
                @if(!empty($siteSettings['instagram_embed_script']))
                    <!-- Menggunakan Script Embed dari Admin -->
                    <div class="w-full overflow-hidden p-2">
                        {!! $siteSettings['instagram_embed_script'] !!}
                    </div>
                @else
                    <!-- NATIVE INSTAGRAM EMBED WIDGET -->
                    <div class="w-full overflow-hidden flex justify-center bg-white py-4">
                        <blockquote class="instagram-media" 
                            data-instgrm-permalink="{{ $igUrl }}?utm_source=ig_embed&amp;utm_campaign=loading" 
                            data-instgrm-version="14" 
                            style=" background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:100%; min-width:326px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);">
                        </blockquote>
                        <script async src="https://www.instagram.com/embed.js"></script>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Refresh Instagram embed when navigating back/forward or if it fails to load initially
    document.addEventListener('DOMContentLoaded', function() {
        if (window.instgrm) {
            window.instgrm.Embeds.process();
        }
    });
</script>
@endpush


<!-- Modal Pop-up Video YouTube dengan YouTube IFrame Player API -->
<div id="homeVideoModal" class="fixed inset-0 z-[100] flex items-center justify-center hidden" style="background-color: rgba(0,0,0,0.90);">
    <button onclick="closeHomeVideoModal()" class="absolute top-6 right-6 text-white hover:text-red-400 text-4xl font-bold focus:outline-none z-50 transition">&times;</button>
    <div class="w-full max-w-4xl px-4 relative">
        <div id="homePlayerContainer" class="relative w-full rounded-2xl overflow-hidden shadow-2xl bg-black" style="padding-top: 56.25%;">
            <!-- YouTube Player -->
            <div id="homeYtPlayerWrapper" class="absolute top-0 left-0 w-full h-full">
                <div id="homeYtPlayer"></div>
            </div>

            <!-- Fallback UI -->
            <div id="homeEmbedFallback" class="absolute inset-0 flex flex-col items-center justify-center bg-gradient-to-br from-gray-900 to-gray-800 text-white hidden">
                <img id="homeFallbackThumb" src="" alt="Video Thumbnail" class="absolute inset-0 w-full h-full object-cover opacity-30">
                <div class="relative z-10 flex flex-col items-center gap-4 px-6 text-center">
                    <div class="w-20 h-20 bg-red-600 rounded-full flex items-center justify-center shadow-2xl">
                        <i class="fab fa-youtube text-4xl text-white"></i>
                    </div>
                    <p class="text-lg font-bold">Video tidak dapat diputar di sini</p>
                    <p class="text-sm text-gray-300 max-w-md">Browser atau jaringan Anda memblokir embed YouTube. Klik tombol di bawah untuk menonton langsung.</p>
                    <a id="homeFallbackLink" href="#" target="_blank" rel="noopener noreferrer"
                       class="mt-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg transition transform hover:scale-105 flex items-center gap-2">
                        <i class="fab fa-youtube text-xl"></i>
                        Tonton di YouTube
                    </a>
                </div>
            </div>
        </div>
        <h3 id="homeVideoTitle" class="text-white font-bold text-base mt-3 text-center px-4 line-clamp-1"></h3>
    </div>
</div>

<!-- YouTube IFrame API (shared, only loads once) -->
<script>
    if (!document.querySelector('script[src*="youtube.com/iframe_api"]')) {
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScript = document.getElementsByTagName('script')[0];
        firstScript.parentNode.insertBefore(tag, firstScript);
    }
</script>

@push('scripts')
<script>
    let currentSlide = 0;
    let autoSlideTimer = null;

    function getSlides() {
        return document.querySelectorAll('.carousel-slide');
    }

    function initCarousel() {
        const slides = getSlides();
        const indicatorsContainer = document.getElementById('carouselIndicators');
        if (!indicatorsContainer || slides.length === 0) return;

        indicatorsContainer.innerHTML = '';
        slides.forEach((_, idx) => {
            const dot = document.createElement('button');
            dot.className = `w-3 h-3 rounded-full transition-all duration-300 ${idx === 0 ? 'bg-amber-400 w-8' : 'bg-white/50 hover:bg-white'}`;
            dot.onclick = () => goToSlide(idx);
            indicatorsContainer.appendChild(dot);
        });

        startAutoSlide();
    }

    function goToSlide(index) {
        const slides = getSlides();
        if (slides.length === 0) return;

        slides[currentSlide].classList.remove('opacity-100', 'z-10');
        slides[currentSlide].classList.add('opacity-0', 'z-0');

        currentSlide = (index + slides.length) % slides.length;

        slides[currentSlide].classList.remove('opacity-0', 'z-0');
        slides[currentSlide].classList.add('opacity-100', 'z-10');

        // Update Dots
        const dots = document.querySelectorAll('#carouselIndicators button');
        dots.forEach((dot, idx) => {
            if (idx === currentSlide) {
                dot.className = 'w-8 h-3 rounded-full bg-amber-400 transition-all duration-300';
            } else {
                dot.className = 'w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all duration-300';
            }
        });

        resetAutoSlide();
    }

    function nextSlide() {
        const slides = getSlides();
        goToSlide(currentSlide + 1);
    }

    function prevSlide() {
        const slides = getSlides();
        goToSlide(currentSlide - 1);
    }

    function startAutoSlide() {
        autoSlideTimer = setInterval(() => {
            nextSlide();
        }, 5000);
    }

    function resetAutoSlide() {
        if (autoSlideTimer) clearInterval(autoSlideTimer);
        startAutoSlide();
    }

    // ===== HOME VIDEO PLAYER (YouTube IFrame API) =====
    let homeYtPlayer = null;
    let homeEmbedCheckTimer = null;

    // Pastikan onYouTubeIframeAPIReady tidak menimpa yang sudah ada
    if (!window._ytApiReadyCallbacks) window._ytApiReadyCallbacks = [];
    window._ytApiReadyHome = false;

    const origOnReady = window.onYouTubeIframeAPIReady;
    window.onYouTubeIframeAPIReady = function() {
        window._ytApiReadyHome = true;
        if (origOnReady) origOnReady();
        window._ytApiReadyCallbacks.forEach(fn => fn());
    };

    function openHomeVideo(videoId, watchUrl, thumbnail, title) {
        document.getElementById('homeVideoModal').classList.remove('hidden');
        document.getElementById('homeVideoTitle').innerText = title;

        // Reset state
        document.getElementById('homeEmbedFallback').classList.add('hidden');
        document.getElementById('homeYtPlayerWrapper').classList.remove('hidden');
        document.getElementById('homeFallbackThumb').src = thumbnail;
        document.getElementById('homeFallbackLink').href = watchUrl;

        // Hancurkan player lama
        if (homeYtPlayer) {
            try { homeYtPlayer.destroy(); } catch(e) {}
            homeYtPlayer = null;
        }

        // Buat ulang container
        const wrapper = document.getElementById('homeYtPlayerWrapper');
        wrapper.innerHTML = '<div id="homeYtPlayer"></div>';

        function createPlayer() {
            try {
                homeYtPlayer = new YT.Player('homeYtPlayer', {
                    width: '100%',
                    height: '100%',
                    videoId: videoId,
                    playerVars: {
                        autoplay: 1,
                        rel: 0,
                        modestbranding: 1,
                        playsinline: 1,
                        origin: window.location.origin
                    },
                    events: {
                        onReady: function(event) {
                            event.target.playVideo();
                            clearTimeout(homeEmbedCheckTimer);
                        },
                        onError: function(event) {
                            console.warn('Home YouTube Player Error:', event.data);
                            showHomeFallback();
                        },
                        onStateChange: function(event) {
                            if (event.data === YT.PlayerState.PLAYING) {
                                document.getElementById('homeEmbedFallback').classList.add('hidden');
                                clearTimeout(homeEmbedCheckTimer);
                            }
                        }
                    }
                });

                // Safety timeout
                clearTimeout(homeEmbedCheckTimer);
                homeEmbedCheckTimer = setTimeout(function() {
                    if (homeYtPlayer && typeof homeYtPlayer.getPlayerState === 'function') {
                        const state = homeYtPlayer.getPlayerState();
                        if (state !== YT.PlayerState.PLAYING && state !== YT.PlayerState.BUFFERING && state !== YT.PlayerState.PAUSED) {
                            showHomeFallback();
                        }
                    } else {
                        showHomeFallback();
                    }
                }, 6000);
            } catch(e) {
                console.error('YouTube API error:', e);
                showHomeFallback();
            }
        }

        if (window._ytApiReadyHome || (typeof YT !== 'undefined' && YT.Player)) {
            createPlayer();
        } else {
            window._ytApiReadyCallbacks.push(createPlayer);
        }
    }

    function showHomeFallback() {
        document.getElementById('homeYtPlayerWrapper').classList.add('hidden');
        document.getElementById('homeEmbedFallback').classList.remove('hidden');
        clearTimeout(homeEmbedCheckTimer);
    }

    function closeHomeVideoModal() {
        document.getElementById('homeVideoModal').classList.add('hidden');
        clearTimeout(homeEmbedCheckTimer);
        if (homeYtPlayer) {
            try { homeYtPlayer.destroy(); } catch(e) {}
            homeYtPlayer = null;
        }
        const wrapper = document.getElementById('homeYtPlayerWrapper');
        if (wrapper) wrapper.innerHTML = '<div id="homeYtPlayer"></div>';
    }

    document.addEventListener('DOMContentLoaded', function() {
        initCarousel();

        // Bind video card clicks
        document.querySelectorAll('.video-home-card').forEach(item => {
            item.addEventListener('click', function() {
                const videoId = this.getAttribute('data-video-id');
                const watchUrl = this.getAttribute('data-watch-url');
                const thumbnail = this.getAttribute('data-thumbnail');
                const title = this.getAttribute('data-title');
                if (videoId) {
                    openHomeVideo(videoId, watchUrl, thumbnail, title);
                }
            });
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeHomeVideoModal();
        }
    });
</script>
@endpush
@endsection
