@extends('layouts.public')

@section('title', 'Galeri Video Kegiatan - Bagian Pemerintahan')

@section('content')
<div class="bg-gray-100 min-h-screen py-10">
    <div class="container mx-auto px-4 lg:px-8 max-w-6xl">
        
        <!-- Video Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($videos as $index => $video)
                <div class="bg-white rounded-xl shadow-md overflow-hidden group cursor-pointer border border-gray-200 transition transform hover:-translate-y-1 hover:shadow-xl" 
                     data-index="{{ $index }}"
                     onclick="openLightbox(Number(this.getAttribute('data-index')))">
                    
                    <div class="relative w-full h-48 bg-black overflow-hidden">
                        <img src="{{ $video->thumbnail_url }}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition duration-500" alt="{{ $video->title }}">
                        
                        <!-- Ikon Play YouTube Merah di Tengah -->
                        <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/10 transition">
                            <div class="w-14 h-14 bg-red-600 rounded-full flex items-center justify-center text-white shadow-2xl group-hover:scale-110 transition duration-300">
                                <i class="fas fa-play text-xl ml-1"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <h3 class="font-bold text-gray-800 text-sm line-clamp-2 leading-snug group-hover:text-blue-600 transition">{{ $video->title }}</h3>
                        <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                            <i class="far fa-clock text-amber-600"></i> {{ $video->created_at->format('d M Y') }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 bg-white rounded-xl border border-gray-200 shadow-sm">
                    <i class="fas fa-video-slash text-5xl text-gray-300 mb-3"></i>
                    <h3 class="font-bold text-gray-700">Belum Ada Video Kegiatan</h3>
                    <p class="text-xs text-gray-500">Video dokumentasi kegiatan belum diunggah oleh administrator.</p>
                </div>
            @endforelse
        </div>

        @if(method_exists($videos, 'links'))
            <div class="mt-8">
                {{ $videos->links() }}
            </div>
        @endif

    </div>
</div>

<!-- ========================================== -->
<!-- LIGHTBOX MODAL PLAYER -->
<!-- ========================================== -->
<div id="videoLightboxModal" class="fixed inset-0 z-[999] flex items-center justify-center hidden bg-black/90 backdrop-blur-sm p-4 transition-all duration-300">
    
    <!-- Tombol Close Top Right -->
    <button onclick="closeLightbox()" class="absolute top-4 right-6 text-white/80 hover:text-white text-4xl font-bold focus:outline-none z-[1010] transition">
        &times;
    </button>

    <!-- Navigation Arrow Left -->
    <button onclick="prevVideo()" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-black/60 hover:bg-black/90 text-white rounded-full flex items-center justify-center text-xl z-[1010] shadow-xl border border-white/20 transition">
        <i class="fas fa-chevron-left"></i>
    </button>

    <!-- Navigation Arrow Right -->
    <button onclick="nextVideo()" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-black/60 hover:bg-black/90 text-white rounded-full flex items-center justify-center text-xl z-[1010] shadow-xl border border-white/20 transition">
        <i class="fas fa-chevron-right"></i>
    </button>

    <!-- Modal Content -->
    <div class="w-full max-w-4xl mx-auto z-[1000] relative">
        <div id="playerContainer" class="relative w-full rounded-2xl overflow-hidden shadow-2xl bg-black border border-gray-800" style="padding-top: 56.25%;">
            <!-- YouTube Player akan di-render di sini oleh YouTube IFrame API -->
            <div id="ytPlayerWrapper" class="absolute top-0 left-0 w-full h-full">
                <div id="ytPlayer"></div>
            </div>

            <!-- Fallback UI: ditampilkan jika embed gagal -->
            <div id="embedFallback" class="absolute inset-0 flex flex-col items-center justify-center bg-gradient-to-br from-gray-900 to-gray-800 text-white hidden">
                <img id="fallbackThumb" src="" alt="Video Thumbnail" class="absolute inset-0 w-full h-full object-cover opacity-30">
                <div class="relative z-10 flex flex-col items-center gap-4 px-6 text-center">
                    <div class="w-20 h-20 bg-red-600 rounded-full flex items-center justify-center shadow-2xl">
                        <i class="fab fa-youtube text-4xl text-white"></i>
                    </div>
                    <p class="text-lg font-bold">Video tidak dapat diputar di sini</p>
                    <p class="text-sm text-gray-300 max-w-md">Browser atau jaringan Anda memblokir embed YouTube. Klik tombol di bawah untuk menonton langsung di YouTube.</p>
                    <a id="fallbackLink" href="#" target="_blank" rel="noopener noreferrer"
                       class="mt-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg transition transform hover:scale-105 flex items-center gap-2">
                        <i class="fab fa-youtube text-xl"></i>
                        Tonton di YouTube
                    </a>
                </div>
            </div>
        </div>
        <h3 id="lightboxTitle" class="text-white font-bold text-base mt-3 text-center px-4 line-clamp-1"></h3>
    </div>
</div>

@php
    $formattedVideos = collect($videos->items())->values()->map(function($v, $idx) {
        $isLocal = !empty($v->file_path) && $v->type === 'video';
        return [
            'index' => $idx,
            'title' => $v->title,
            'videoId' => $v->youtube_id,
            'embedUrl' => $v->embed_url ? $v->embed_url . '&autoplay=1' : null,
            'watchUrl' => $v->watch_url,
            'thumbnail' => $v->thumbnail_url,
            'isLocal' => $isLocal,
            'localUrl' => $isLocal ? asset('storage/' . $v->file_path) : null,
        ];
    });
@endphp

<script id="videoDataJson" type="application/json">
    @json($formattedVideos)
</script>

@push('scripts')
<script>
    const videoDataElement = document.getElementById('videoDataJson');
    const videoDataList = videoDataElement ? JSON.parse(videoDataElement.textContent) : [];

    let currentVideoIndex = 0;

    function openLightbox(index) {
        if (videoDataList.length === 0) return;
        currentVideoIndex = index;
        document.getElementById('videoLightboxModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        loadVideo();
    }

    function loadVideo() {
        const item = videoDataList[currentVideoIndex];
        if (!item) return;

        document.getElementById('lightboxTitle').innerText = item.title;

        // Reset fallback & player
        document.getElementById('embedFallback').classList.add('hidden');
        document.getElementById('ytPlayerWrapper').classList.remove('hidden');

        // Setup fallback data
        document.getElementById('fallbackThumb').src = item.thumbnail;
        document.getElementById('fallbackLink').href = item.watchUrl || '#';

        const wrapper = document.getElementById('ytPlayerWrapper');
        
        if (item.isLocal && item.localUrl) {
            // Putar video lokal dengan HTML5 player
            wrapper.innerHTML = `<video src="${item.localUrl}" controls autoplay class="w-full h-full outline-none bg-black"></video>`;
        } else if (item.embedUrl) {
            // Gunakan iframe langsung untuk YouTube agar lebih stabil
            wrapper.innerHTML = `<iframe src="${item.embedUrl}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen onerror="showFallback()"></iframe>`;
            
            // Fallback checking: since we can't easily detect cross-origin iframe errors natively in all browsers,
            // we assume it works if the embedUrl is valid.
        } else {
            // Data video tidak valid
            showFallback();
        }
    }

    function showFallback() {
        document.getElementById('ytPlayerWrapper').classList.add('hidden');
        document.getElementById('embedFallback').classList.remove('hidden');
    }

    function prevVideo() {
        if (videoDataList.length === 0) return;
        currentVideoIndex = (currentVideoIndex - 1 + videoDataList.length) % videoDataList.length;
        loadVideo();
    }

    function nextVideo() {
        if (videoDataList.length === 0) return;
        currentVideoIndex = (currentVideoIndex + 1) % videoDataList.length;
        loadVideo();
    }

    function closeLightbox() {
        document.getElementById('videoLightboxModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        
        // Hapus elemen iframe atau video agar playback berhenti
        const wrapper = document.getElementById('ytPlayerWrapper');
        if (wrapper) wrapper.innerHTML = '';
    }

    // Keyboard Shortcuts (Arrow Left, Arrow Right, ESC)
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('videoLightboxModal');
        if (!modal.classList.contains('hidden')) {
            if (e.key === 'Escape') {
                closeLightbox();
            } else if (e.key === 'ArrowLeft') {
                prevVideo();
            } else if (e.key === 'ArrowRight') {
                nextVideo();
            }
        }
    });

    // Close on dark background click
    document.getElementById('videoLightboxModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLightbox();
        }
    });
</script>
@endpush
@endsection
