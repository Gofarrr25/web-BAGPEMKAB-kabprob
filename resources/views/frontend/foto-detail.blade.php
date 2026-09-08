@extends('layouts.public')

@section('title', $gallery->title . ' - Galeri Foto')

@section('content')
@php
    $latestGalleries = \App\Models\Gallery::where('type', 'image')->where('id', '!=', $gallery->id)->latest()->take(5)->get();
@endphp
<div class="bg-gray-50 min-h-screen py-12">
    <div class="container mx-auto px-4 lg:px-8">
        
        <div class="mb-8">
            <a href="{{ url('/galeri-foto') }}" class="text-brand-blue hover:text-brand-blue-hover font-bold text-sm flex items-center gap-2 mb-4">
                <i class="fas fa-arrow-left"></i> Kembali ke Galeri
            </a>
            <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 leading-tight">{{ $gallery->title }}</h1>
            <p class="text-sm text-gray-500 mt-2"><i class="far fa-calendar-alt text-brand-blue mr-2"></i>Dipublikasikan pada {{ $gallery->created_at->format('d F Y, H:i') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-10">
            <!-- Main Content Area -->
            <main class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-6 lg:p-4 md:p-8">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-8">
                        @if($gallery->file_path)
                            <div class="relative w-full aspect-[4/3] rounded-lg overflow-hidden cursor-pointer group shadow-sm bg-gray-100" onclick="openPhotoModal(this)" data-src="{{ asset('storage/' . $gallery->file_path) }}">
                                <img src="{{ asset('storage/' . $gallery->file_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" alt="Foto Utama">
                                
                                <!-- Dark Gradient Overlay from bottom -->
                                <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/80 to-transparent pointer-events-none opacity-60 group-hover:opacity-90 transition duration-300"></div>
                                
                                <!-- Icon top right -->
                                <div class="absolute top-3 right-3 bg-black/30 backdrop-blur-sm w-8 h-8 flex items-center justify-center rounded text-white shadow-sm pointer-events-none">
                                    <i class="fas fa-image text-xs"></i>
                                </div>
                            </div>
                        @endif
                        
                        @foreach($gallery->galleryItems as $item)
                            <div class="relative w-full aspect-[4/3] rounded-lg overflow-hidden cursor-pointer group shadow-sm bg-gray-100" onclick="openPhotoModal(this)" data-src="{{ asset('storage/' . $item->file_path) }}">
                                <img src="{{ asset('storage/' . $item->file_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" alt="Foto Tambahan">
                                
                                <!-- Dark Gradient Overlay from bottom -->
                                <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/80 to-transparent pointer-events-none opacity-60 group-hover:opacity-90 transition duration-300"></div>
                                
                                <!-- Icon top right -->
                                <div class="absolute top-3 right-3 bg-black/30 backdrop-blur-sm w-8 h-8 flex items-center justify-center rounded text-white shadow-sm pointer-events-none">
                                    <i class="fas fa-image text-xs"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(!empty($gallery->description))
                        <div class="prose max-w-none text-gray-800 mt-6 border-t border-gray-100 pt-6 ckeditor-content break-words">
                            {!! $gallery->description !!}
                        </div>
                    @endif

                </div>
            </main>

            <!-- Sidebar -->
            <aside class="lg:col-span-1 space-y-8">
                
                <!-- Search Box -->
                <div>
                    <form action="{{ url('/galeri-foto') }}" method="GET" class="flex shadow-xs">
                        <input type="text" name="q" placeholder="Cari Galeri..." class="w-full border border-gray-300 px-4 py-2.5 text-sm text-gray-700 outline-none rounded-l border-r-0 focus:ring-1 focus:ring-brand-blue">
                        <button type="submit" class="bg-brand-blue hover:bg-[#122543] text-white px-5 py-2.5 rounded-r transition flex items-center justify-center">
                            <i class="fas fa-search text-base"></i>
                        </button>
                    </form>
                </div>

                <!-- Informasi Lainnya List -->
                <div>
                    <h2 class="text-xl font-bold text-brand-blue mb-6 border-b border-gray-100 pb-2">Galeri Lainnya</h2>

                    <div class="space-y-6">
                        @foreach($latestGalleries as $lGallery)
                        <div class="flex gap-4 items-start group border-b border-gray-100 pb-5 last:border-0">
                            <a href="{{ route('frontend.foto.detail', $lGallery->id) }}" class="w-20 h-16 rounded overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-100 block">
                                @if($lGallery->file_path)
                                    <img src="{{ asset('storage/' . $lGallery->file_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $lGallery->title }}">
                                @elseif($lGallery->galleryItems && $lGallery->galleryItems->first())
                                    <img src="{{ asset('storage/' . $lGallery->galleryItems->first()->file_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $lGallery->title }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-200 text-xs">
                                        <i class="fas fa-image text-xl"></i>
                                    </div>
                                @endif
                            </a>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-bold text-brand-blue group-hover:text-brand-blue-hover transition line-clamp-2 leading-snug">
                                    <a href="{{ route('frontend.foto.detail', $lGallery->id) }}">{{ $lGallery->title }}</a>
                                </h3>
                                <div class="text-xs text-gray-400 mt-1 flex items-center gap-1 font-sans">
                                    <i class="far fa-calendar-alt text-xs"></i>
                                    <span>{{ $lGallery->created_at->format('d F Y') }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Back to All News Button -->
                <div>
                    <a href="{{ url('/galeri-foto') }}" class="w-full py-3 bg-brand-blue text-white font-bold rounded hover:bg-[#122543] transition shadow flex items-center justify-center gap-2 text-sm uppercase tracking-wider">
                        <i class="fas fa-images"></i> Indeks Galeri Foto
                    </a>
                </div>

            </aside>
        </div>
    </div>

    <!-- Photo Lightbox Modal -->
    <div id="photoModal" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-black/95 p-4 transition-opacity">
        <button onclick="closePhotoModal()" class="absolute top-6 right-6 text-white hover:text-gray-300 text-4xl focus:outline-none z-50 transition">
            &times;
        </button>
        <div class="max-w-6xl w-full text-center relative flex flex-col items-center">
            <img id="modalImage" src="" class="max-h-[85vh] mx-auto rounded shadow-2xl object-contain" alt="Preview Foto">
        </div>
    </div>
</div>

<style>
    /* Prevent overflow in content generated by CKEditor */
    .ckeditor-content {
        word-wrap: break-word;
        overflow-wrap: anywhere;
        word-break: break-word;
        max-width: 100%;
        overflow-x: hidden; /* Added precaution */
    }
    
    .ckeditor-content img,
    .ckeditor-content figure,
    .ckeditor-content figure img {
        max-width: 100% !important;
        height: auto !important;
        display: block;
    }
    
    .ckeditor-content table {
        width: 100% !important;
        max-width: 100%;
        overflow-x: auto;
        display: block; /* Make tables scrollable */
    }
    
    .ckeditor-content pre, 
    .ckeditor-content code {
        white-space: pre-wrap;
        word-wrap: break-word;
        max-width: 100%;
    }
</style>

<script>
function openPhotoModal(element) {
    const src = element.getAttribute('data-src');
    document.getElementById('modalImage').src = src;
    document.getElementById('photoModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePhotoModal() {
    document.getElementById('photoModal').classList.add('hidden');
    document.getElementById('modalImage').src = '';
    document.body.style.overflow = 'auto';
}

// Close on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closePhotoModal();
    }
});
</script>
@endsection
