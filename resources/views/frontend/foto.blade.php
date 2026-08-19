@extends('layouts.public')

@section('title', 'Galeri Foto - Bagian Pemerintahan')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">

    <div class="container mx-auto px-4 lg:px-8">
        
        <!-- Photo Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($photos as $photo)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden group cursor-pointer hover:shadow-lg transition" data-src="{{ asset('storage/' . $photo->file_path) }}" data-title="{{ $photo->title }}" onclick="openPhotoModal(this)">
                    <div class="relative w-full h-52 bg-gray-100 overflow-hidden">
                        @if($photo->file_path)
                            <img src="{{ asset('storage/' . $photo->file_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $photo->title }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <i class="fas fa-image text-4xl"></i>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center text-white">
                            <i class="fas fa-search-plus text-3xl"></i>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-800 text-xs line-clamp-2 leading-snug group-hover:text-blue-600 transition">{{ $photo->title }}</h3>
                        <p class="text-[11px] text-gray-400 mt-2"><i class="far fa-calendar-alt text-blue-500 mr-1"></i>{{ $photo->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <i class="fas fa-images text-5xl text-gray-300 mb-3"></i>
                    <h3 class="font-bold text-gray-700">Belum Ada Galeri Foto</h3>
                    <p class="text-xs text-gray-500">Dokumentasi foto kegiatan belum diunggah.</p>
                </div>
            @endforelse
        </div>

        @if(method_exists($photos, 'links'))
            <div class="mt-8">
                {{ $photos->links() }}
            </div>
        @endif

    </div>

    <!-- Photo Lightbox Modal -->
    <div id="photoModal" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-black/90 p-4">
        <button onclick="closePhotoModal()" class="absolute top-6 right-6 text-white hover:text-gray-300 text-4xl focus:outline-none z-50">
            &times;
        </button>
        <div class="max-w-4xl w-full text-center">
            <img id="modalImage" src="" class="max-h-[80vh] mx-auto rounded shadow-2xl object-contain" alt="Preview Foto">
            <p id="modalCaption" class="text-white font-bold text-sm mt-4 px-4"></p>
        </div>
    </div>
</div>

<script>
function openPhotoModal(element) {
    const src = element.getAttribute('data-src');
    const title = element.getAttribute('data-title');
    document.getElementById('modalImage').src = src;
    document.getElementById('modalCaption').innerText = title;
    document.getElementById('photoModal').classList.remove('hidden');
}

function closePhotoModal() {
    document.getElementById('photoModal').classList.add('hidden');
    document.getElementById('modalImage').src = '';
}
</script>
@endsection
