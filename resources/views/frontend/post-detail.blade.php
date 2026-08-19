@extends('layouts.public')

@section('title', $post->title . ' - Bagian Pemerintahan')

@section('content')
<div class="bg-white min-h-screen py-8">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <!-- Main Content Area (Left Column - 2 cols) -->
            <main class="lg:col-span-2">
                
                <!-- Main Featured Image (Top of Left Column) -->
                @if($post->image)
                    <div class="mb-6 rounded-lg overflow-hidden shadow-sm border border-gray-100 bg-gray-100">
                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-auto max-h-[500px] object-cover">
                    </div>
                @else
                    <div class="mb-6 rounded-lg overflow-hidden shadow-sm border border-gray-100 bg-gray-900 text-white py-20 text-center">
                        <i class="fas fa-newspaper text-6xl text-gray-500 mb-3"></i>
                        <p class="text-sm text-gray-400">Bagian Pemerintahan Kabupaten Probolinggo</p>
                    </div>
                @endif

                <!-- Meta Info Row (Clock Icon + Date - Category Oleh Author) -->
                <div class="text-xs text-gray-500 flex items-center gap-1.5 font-semibold mb-3">
                    <i class="far fa-clock text-[#7a8b3d] text-sm"></i>
                    <span>{{ $post->created_at->format('d F Y') }}</span>
                    <span>-</span>
                    <span class="text-gray-700 font-bold">{{ $post->category->name ?? 'Pemerintahan' }}</span>
                    <span>Oleh</span>
                    <span class="text-gray-700 font-bold">{{ $post->user->name ?? 'admin' }}</span>
                </div>

                <!-- Main News Title -->
                <h1 class="text-2xl lg:text-3xl font-extrabold text-[#1a365d] leading-tight mb-8">
                    {{ $post->title }}
                </h1>

                <!-- Content Body -->
                <div class="prose prose-lg max-w-none text-gray-800 leading-relaxed font-sans border-b border-gray-100 pb-10">
                    {!! $post->content !!}
                </div>

                <!-- Share Buttons -->
                <div class="mt-8 flex items-center justify-between bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wide">Bagikan Berita Ini:</span>
                    <div class="flex gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition text-xs shadow-sm">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' - ' . url()->current()) }}" target="_blank" class="w-9 h-9 rounded-full bg-green-500 text-white flex items-center justify-center hover:bg-green-600 transition text-xs shadow-sm">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(url()->current()) }}" target="_blank" class="w-9 h-9 rounded-full bg-sky-500 text-white flex items-center justify-center hover:bg-sky-600 transition text-xs shadow-sm">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>

            </main>

            <!-- Sidebar (Right Column - 1 col) -->
            <aside class="lg:col-span-1 space-y-8">
                
                <!-- Search Box (Persis Seperti Gambar User) -->
                <div>
                    <form action="{{ url('/informasi') }}" method="GET" class="flex shadow-xs">
                        <input type="text" name="q" placeholder="Search Here..." class="w-full border border-gray-300 px-4 py-2.5 text-sm text-gray-700 outline-none rounded-l border-r-0 focus:ring-1 focus:ring-[#7a8b3d]">
                        <button type="submit" class="bg-[#7a8b3d] hover:bg-[#687733] text-white px-5 py-2.5 rounded-r transition flex items-center justify-center">
                            <i class="fas fa-search text-base"></i>
                        </button>
                    </form>
                </div>

                <!-- Informasi Lainnya List (Persis Seperti Gambar User) -->
                <div>
                    <h2 class="text-xl font-bold text-[#7a8b3d] mb-6 border-b border-gray-100 pb-2">Informasi Lainnya</h2>

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
                                <h3 class="text-sm font-bold text-[#1a365d] group-hover:text-[#7a8b3d] transition line-clamp-2 leading-snug">
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
                    <a href="{{ url('/informasi') }}" class="w-full py-3 bg-[#7a8b3d] text-white font-bold rounded hover:bg-[#687733] transition shadow flex items-center justify-center gap-2 text-sm uppercase tracking-wider">
                        <i class="fas fa-list"></i> Indeks Informasi & Berita
                    </a>
                </div>

            </aside>

        </div>
    </div>
</div>
@endsection
