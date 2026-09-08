@extends('layouts.public')

@section('title', $gallery->title . ' - Galeri Video')

@section('content')
<div class="bg-gray-100 min-h-screen py-12">
    <div class="container mx-auto px-4 lg:px-8">
        
        <div class="mb-8 max-w-6xl mx-auto">
            <a href="{{ url('/galeri-video') }}" class="inline-flex items-center gap-2 mb-5 px-5 py-2.5 bg-white border border-gray-200 rounded-lg text-brand-blue hover:text-brand-blue-hover hover:bg-gray-50 hover:border-gray-300 shadow-sm transition-all text-[14px] md:text-[15px] font-bold w-fit">
                <i class="fas fa-arrow-left"></i> Kembali ke Galeri
            </a>
            <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 leading-tight">{{ $gallery->title }}</h1>
            <p class="text-sm text-gray-500 mt-2"><i class="far fa-clock text-amber-600 mr-2"></i>Dipublikasikan pada {{ $gallery->created_at->format('d F Y') }}</p>
        </div>

        @php
            $allVideos = [];
            
            // Helper function to extract youtube data
            $getYoutubeData = function($url) {
                $embedUrl = null;
                $thumbnailUrl = null;
                if($url) {
                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $matches);
                    if(isset($matches[1])) {
                        $videoId = $matches[1];
                        $embedUrl = "https://www.youtube.com/embed/" . $videoId;
                        $thumbnailUrl = "https://img.youtube.com/vi/" . $videoId . "/hqdefault.jpg";
                    }
                }
                return ['embedUrl' => $embedUrl, 'thumbnailUrl' => $thumbnailUrl];
            };

            // Main Video
            if($gallery->video_url || $gallery->file_path) {
                $isLocal = !empty($gallery->file_path) && !$gallery->video_url;
                $ytData = $getYoutubeData($gallery->video_url);
                
                $allVideos[] = [
                    'title' => $gallery->title . ' (Utama)',
                    'embedUrl' => $ytData['embedUrl'],
                    'thumbnailUrl' => $ytData['thumbnailUrl'],
                    'isLocal' => $isLocal,
                    'localUrl' => $isLocal ? asset('storage/' . $gallery->file_path) : null,
                ];
            }
            
            // Sub Videos
            foreach($gallery->galleryItems as $idx => $item) {
                $isLocal = !empty($item->file_path) && !$item->video_url;
                $ytData = $getYoutubeData($item->video_url);
                
                $allVideos[] = [
                    'title' => $item->title ?? ('Video ' . ($idx + 1)),
                    'embedUrl' => $ytData['embedUrl'],
                    'thumbnailUrl' => $ytData['thumbnailUrl'],
                    'isLocal' => $isLocal,
                    'localUrl' => $isLocal ? asset('storage/' . $item->file_path) : null,
                ];
            }
        @endphp

        <div class="max-w-6xl mx-auto space-y-12">
            
            <!-- Video Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($allVideos as $vid)
                    <div class="bg-black rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-200 group cursor-pointer flex flex-col aspect-video relative"
                         data-embed="{{ $vid['embedUrl'] }}"
                         data-is-local="{{ $vid['isLocal'] ? '1' : '0' }}"
                         data-local-url="{{ $vid['localUrl'] }}"
                         onclick="playVideo(this.dataset.embed, this.dataset.isLocal, this.dataset.localUrl)">
                        @if($vid['thumbnailUrl'])
                            <img src="{{ $vid['thumbnailUrl'] }}" class="absolute inset-0 w-full h-full object-cover scale-[1.05] opacity-90 group-hover:opacity-100 group-hover:scale-110 transition duration-500" alt="{{ $vid['title'] }}">
                        @else
                            <div class="absolute inset-0 w-full h-full flex items-center justify-center bg-gray-800 text-gray-500 group-hover:bg-gray-700 transition duration-500">
                                <i class="fas fa-video text-5xl"></i>
                            </div>
                        @endif
                        
                        <!-- Ikon Play Tengah -->
                        <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/10 transition z-10">
                            <div class="w-14 h-14 bg-red-600 rounded-full flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-play ml-1 text-xl"></i>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center text-gray-400">
                        <i class="fas fa-video-slash text-5xl mb-3 block"></i>
                        <p>Tidak ada video dalam galeri ini.</p>
                    </div>
                @endforelse
            </div>
            
            @if(!empty($gallery->description))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6 lg:p-4 md:p-8 mt-8">
                    <div class="prose max-w-none text-gray-700">
                        {!! $gallery->description !!}
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>

<!-- Video Modal -->
<div id="videoModal" class="fixed inset-0 z-[100] hidden bg-black/95 flex items-center justify-center p-4 md:p-10 opacity-0 transition-opacity duration-300">
    
    <!-- Close Button (Visible on screen top-right) -->
    <button type="button" onclick="closeVideoModal()" class="absolute top-4 right-4 md:top-8 md:right-8 text-white hover:text-red-500 z-[110] bg-gray-900/80 hover:bg-gray-900 border border-gray-700 rounded-full w-10 h-10 md:w-12 md:h-12 flex items-center justify-center transition cursor-pointer shadow-lg">
        <i class="fas fa-times text-xl md:text-2xl"></i>
    </button>

    <div class="relative w-full max-w-5xl bg-black rounded-xl overflow-hidden shadow-2xl transform scale-95 transition-transform duration-300 border border-gray-800" id="videoModalContent">
        <!-- Video Container -->
        <div class="relative w-full aspect-video bg-black" id="videoContainer">
            <!-- Iframe or Video Tag will be injected here -->
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function playVideo(embedUrl, isLocal, localUrl) {
        const modal = document.getElementById('videoModal');
        
        const container = document.getElementById('videoContainer');
        const modalContent = document.getElementById('videoModalContent');
        
        container.innerHTML = ''; // clear previous
        
        if (isLocal === '1' && localUrl) {
            container.innerHTML = `<video src="${localUrl}" controls autoplay class="w-full h-full object-contain"></video>`;
        } else if (embedUrl) {
            container.innerHTML = `<iframe src="${embedUrl}?autoplay=1" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
        } else {
            container.innerHTML = `<div class="flex flex-col items-center justify-center h-full text-gray-500"><i class="fas fa-exclamation-triangle text-4xl mb-3"></i><p>Video tidak valid atau link rusak</p></div>`;
        }
        
        modal.classList.remove('hidden');
        // trigger reflow
        void modal.offsetWidth;
        modal.classList.remove('opacity-0');
        modalContent.classList.remove('scale-95');
        document.body.style.overflow = 'hidden'; // prevent background scrolling
    }
    
    function closeVideoModal() {
        const modal = document.getElementById('videoModal');
        const container = document.getElementById('videoContainer');
        const modalContent = document.getElementById('videoModalContent');
        
        modal.classList.add('opacity-0');
        modalContent.classList.add('scale-95');
        document.body.style.overflow = '';
        
        setTimeout(() => {
            modal.classList.add('hidden');
            container.innerHTML = ''; // stop playing
        }, 300);
    }
    
    // Close modal on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !document.getElementById('videoModal').classList.contains('hidden')) {
            closeVideoModal();
        }
    });
    
    // Close modal on clicking outside the video
    document.getElementById('videoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeVideoModal();
        }
    });
</script>
@endpush
