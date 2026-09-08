@extends('layouts.public')

@section('title', 'Galeri Video Kegiatan - Bagian Pemerintahan')

@section('content')
<div class="bg-gray-100 min-h-screen py-10">
    <div class="container mx-auto px-4 lg:px-8 max-w-6xl">
        
        <!-- Search Bar -->
        <div class="bg-white p-4 md:p-4 md:p-6 rounded-xl shadow-sm border border-gray-100 mb-8 flex justify-between items-center">
            <form action="{{ url('/galeri-video') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Galeri Video..." class="w-full flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-600 outline-none">
                <button type="submit" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-sm shadow transition flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
        </div>

        <!-- Video Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-8 items-start">
            @forelse($videos as $index => $video)
                @php 
                    $vidCount = ($video->galleryItems ? $video->galleryItems->count() : 0);
                    if ($video->video_url || $video->file_path) {
                        $vidCount += 1;
                    }
                    
                    $isSingleVideo = $vidCount <= 1;
                    $embedUrl = null;
                    $isLocal = 0;
                    $localUrl = null;
                    
                    if ($isSingleVideo) {
                        $targetVideoUrl = $video->video_url;
                        $targetFilePath = $video->file_path;
                        
                        if (!$targetVideoUrl && !$targetFilePath && $video->galleryItems && $video->galleryItems->count() > 0) {
                            $firstItem = $video->galleryItems->first();
                            $targetVideoUrl = $firstItem->video_url;
                            $targetFilePath = $firstItem->file_path;
                        }

                        if ($targetVideoUrl) {
                            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $targetVideoUrl, $matches);
                            if(isset($matches[1])) {
                                $embedUrl = "https://www.youtube.com/embed/" . $matches[1];
                                $youtubeId = $matches[1];
                            }
                        } else if ($targetFilePath) {
                            $isLocal = 1;
                            $localUrl = asset('storage/' . $targetFilePath);
                        }
                    } else {
                        // Jika album, coba ambil youtube_id dari video pertama untuk thumbnail maxresdefault
                        if ($video->galleryItems && $video->galleryItems->count() > 0) {
                            $firstItem = $video->galleryItems->first();
                            if ($firstItem->video_url) {
                                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $firstItem->video_url, $matches);
                                if(isset($matches[1])) {
                                    $youtubeId = $matches[1];
                                }
                            }
                        }
                    }
                    
                    // Setup maxresdefault as primary thumbnail if youtubeId exists
                    $thumbnailUrl = $video->thumbnail_url;
                    $maxresThumbnailUrl = null;
                    if (isset($youtubeId) && $youtubeId) {
                        $maxresThumbnailUrl = "https://img.youtube.com/vi/{$youtubeId}/maxresdefault.jpg";
                        $hqThumbnailUrl = "https://img.youtube.com/vi/{$youtubeId}/hqdefault.jpg";
                    } else if ($video->youtube_id) {
                        $maxresThumbnailUrl = "https://img.youtube.com/vi/{$video->youtube_id}/maxresdefault.jpg";
                        $hqThumbnailUrl = "https://img.youtube.com/vi/{$video->youtube_id}/hqdefault.jpg";
                    }
                @endphp

                @if($isSingleVideo)
                <div class="group block w-full bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col">
                @else
                <a href="{{ route('frontend.video.detail', $video->id) }}" class="group block w-full bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col">
                @endif
                    
                    <!-- Thumbnail Area -->
                    <div class="relative w-full aspect-video shrink-0 bg-gray-100">
                        @if($isSingleVideo)
                            <!-- Thumbnail State for Single Video -->
                            <div class="absolute inset-0 w-full h-full overflow-hidden rounded-t-2xl cursor-pointer" onclick="playVideo('{{ $embedUrl }}', '{{ $isLocal }}', '{{ $localUrl }}')">
                                @if($maxresThumbnailUrl)
                                    <img src="{{ $maxresThumbnailUrl }}" onerror="this.onerror=null; this.src='{{ $hqThumbnailUrl }}';" class="w-full h-full object-cover scale-[1.02] group-hover:scale-105 transition-transform duration-500" alt="{{ $video->title }}">
                                @else
                                    <img src="{{ $thumbnailUrl }}" class="w-full h-full object-cover scale-[1.02] group-hover:scale-105 transition-transform duration-500" alt="{{ $video->title }}">
                                @endif
                            </div>
                            <!-- Ikon Play Bawah Kanan -->
                            <div class="absolute bottom-4 right-4 w-12 h-12 bg-red-600 rounded-full flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform duration-300 pointer-events-none z-10">
                                <i class="fas fa-play ml-1"></i>
                            </div>
                        @else
                            <!-- Thumbnail State for Album -->
                            <div class="absolute inset-0 w-full h-full overflow-hidden rounded-t-2xl">
                                @if($maxresThumbnailUrl)
                                    <img src="{{ $maxresThumbnailUrl }}" onerror="this.onerror=null; this.src='{{ $hqThumbnailUrl }}';" class="w-full h-full object-cover scale-[1.02] group-hover:scale-105 transition-transform duration-500" alt="{{ $video->title }}">
                                @else
                                    <img src="{{ $thumbnailUrl }}" class="w-full h-full object-cover scale-[1.02] group-hover:scale-105 transition-transform duration-500" alt="{{ $video->title }}">
                                @endif
                            </div>
                            <!-- Ikon Play Bawah Kanan -->
                            <div class="absolute bottom-4 right-4 w-12 h-12 bg-red-600 rounded-full flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform duration-300 z-10">
                                <i class="fas fa-play ml-1"></i>
                            </div>
                        @endif
                        
                        @php
                            $monthsId = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                            $day = $video->created_at->format('d');
                            $monthInt = (int)$video->created_at->format('m');
                            $year = $video->created_at->format('Y');
                            $monthName = $monthsId[$monthInt];
                            $formattedDate = strtoupper($day . ' ' . $monthName . ' ' . $year);
                        @endphp
                        
                        <!-- Tanggal (Sesuai Submenu Berita) -->
                        <div class="absolute -bottom-5 left-6 md:left-7 bg-brand-blue text-white text-[13px] md:text-sm font-bold px-5 py-2.5 shadow-sm whitespace-nowrap z-30 pointer-events-none">
                            {{ $formattedDate }}
                        </div>
                    </div>

                    <!-- Content Area -->
                    <div class="p-6 md:p-7 pt-10 md:pt-10 flex flex-col">
                        <h3 class="font-bold text-gray-800 text-lg md:text-xl leading-tight mb-0 group-hover:text-red-600 transition-colors duration-300">
                            @if($isSingleVideo)
                                <span class="cursor-pointer" onclick="playVideo('{{ $embedUrl }}', '{{ $isLocal }}', '{{ $localUrl }}')">{{ $video->title }}</span>
                            @else
                                {{ $video->title }}
                            @endif
                        </h3>
                        
                        @if($vidCount > 1)
                            <!-- Album Video Badge (Di Bawah Judul, Bentuk Konsisten Dengan Tanggal) -->
                            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center">
                                <div class="bg-red-600 text-white text-[13px] md:text-sm font-bold px-5 py-2.5 shadow-sm flex items-center gap-2">
                                    <span>Album Video</span>
                                    <span class="opacity-80 text-xs border-l border-white/30 pl-2">{{ $vidCount }} Video</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @if($isSingleVideo)
                </div>
                @else
                </a>
                @endif
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
