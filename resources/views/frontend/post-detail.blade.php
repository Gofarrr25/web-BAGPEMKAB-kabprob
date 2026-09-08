@extends('layouts.public')

@section('title', $post->title . ' - Bagian Pemerintahan')

@section('content')
<div class="bg-white min-h-screen py-8">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-10">
            
            <!-- Main Content Area (Left Column - 2 cols) -->
            <main class="lg:col-span-2">
                
                <!-- Main Featured Image (Top of Left Column) -->
                @if($post->image)
                    <div class="mb-4 rounded-lg overflow-hidden shadow-sm border border-gray-100 bg-gray-100 cursor-pointer flex justify-center" onclick="openPhotoModal(this)" data-src="{{ asset('storage/' . $post->image) }}">
                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-auto max-h-[600px] object-contain hover:opacity-90 transition">
                    </div>
                @elseif(!$post->image && (!isset($post->postImages) || $post->postImages->count() == 0))
                    <div class="mb-4 rounded-lg overflow-hidden shadow-sm border border-gray-100 bg-gray-900 text-white py-20 text-center">
                        <i class="fas fa-newspaper text-6xl text-gray-500 mb-3"></i>
                        <p class="text-sm text-gray-400">Bagian Pemerintahan Kabupaten Probolinggo</p>
                    </div>
                @endif

                <!-- Album Foto Berita (Di Atas Judul/Konten) -->
                @if($post->postImages && $post->postImages->count() > 0)
                <div class="mb-6 flex flex-col gap-4">
                    @foreach($post->postImages as $img)
                        <div class="rounded-lg overflow-hidden shadow-sm border border-gray-100 bg-gray-100 cursor-pointer flex justify-center" onclick="openPhotoModal(this)" data-src="{{ asset('storage/' . $img->image_path) }}">
                            <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-auto max-h-[600px] object-contain hover:opacity-90 transition" alt="Foto Album Berita">
                        </div>
                    @endforeach
                </div>
                @endif

                <!-- Meta Info Row (Clock Icon + Date - Category) -->
                <div class="text-xs text-gray-500 flex items-center gap-1.5 font-semibold mb-3 mt-4">
                    <i class="far fa-clock text-brand-blue text-sm"></i>
                    <span>{{ $post->created_at->format('d F Y') }}</span>
                    <span>-</span>
                    <span class="text-gray-700 font-bold">{{ $post->category->name ?? 'Pemerintahan' }}</span>
                </div>

                <!-- Main News Title -->
                <h1 class="text-2xl md:text-[28px] font-medium text-brand-blue leading-snug mb-8">
                    {{ $post->title }}
                </h1>

                <!-- Content Body -->
                <div class="prose prose-lg max-w-none text-gray-800 leading-relaxed font-sans border-b border-gray-100 pb-10">
                    {!! $post->content !!}
                </div>



            </main>

            <!-- Sidebar (Right Column - 1 col) -->
            <aside class="lg:col-span-1 space-y-8">
                
                <!-- Search Box (Persis Seperti Gambar User) -->
                <div>
                    <form action="{{ url('/informasi') }}" method="GET" class="flex shadow-xs">
                        <input type="text" name="q" placeholder="Search Here..." class="w-full border border-gray-300 px-4 py-2.5 text-sm text-gray-700 outline-none rounded-l border-r-0 focus:ring-1 focus:ring-brand-blue">
                        <button type="submit" class="bg-brand-blue hover:bg-brand-blue-hover text-white px-5 py-2.5 rounded-r transition flex items-center justify-center">
                            <i class="fas fa-search text-base"></i>
                        </button>
                    </form>
                </div>

                <!-- Informasi Lainnya List (Persis Seperti Gambar User) -->
                <div>
                    <h2 class="text-xl font-bold text-brand-blue mb-6 border-b border-gray-100 pb-2">Informasi Lainnya</h2>

                    <div class="space-y-6">
                        @foreach($latestPosts as $lPost)
                        <div class="flex gap-4 items-start group border-b border-gray-100 pb-5 last:border-0">
                            <a href="{{ url('/informasi/' . $lPost->slug) }}" class="w-20 h-16 rounded overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-100 block">
                                @if($lPost->image)
                                    <img src="{{ asset('storage/' . $lPost->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $lPost->title }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-200 text-xs">
                                        <i class="fas fa-newspaper"></i>
                                    </div>
                                @endif
                            </a>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-[15px] font-medium text-brand-blue group-hover:text-brand-blue transition line-clamp-2 leading-relaxed">
                                    <a href="{{ url('/informasi/' . $lPost->slug) }}">{{ $lPost->title }}</a>
                                </h3>
                                <div class="text-xs text-gray-400 mt-1 flex items-center gap-1 font-sans">
                                    <i class="far fa-clock text-xs"></i>
                                    <span>{{ $lPost->created_at->format('d F Y') }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Back to All News Button -->
                <div>
                    <a href="{{ url('/informasi') }}" class="w-full py-3 bg-brand-blue text-white font-bold rounded hover:bg-brand-blue-hover transition shadow flex items-center justify-center gap-2 text-sm uppercase tracking-wider">
                        <i class="fas fa-list"></i> Indeks Informasi & Berita
                    </a>
                </div>

            </aside>

        </div>
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
