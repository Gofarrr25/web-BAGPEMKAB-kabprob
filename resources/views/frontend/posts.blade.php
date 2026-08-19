@extends('layouts.public')

@section('title', 'Informasi & Berita Terkini - Bagian Pemerintahan')

@section('content')
<div class="bg-gray-50 min-h-screen pb-16">
    <div class="container mx-auto px-4 lg:px-8 mt-8">
        
        <!-- Search & Filter Bar -->
        <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-100 mb-8 flex flex-col md:flex-row gap-4 justify-between items-center">
            <form action="{{ url('/informasi') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari berita atau kata kunci..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#7a8b3d] outline-none">
                </div>

                <div class="w-full md:w-56">
                    <select name="category" onchange="this.form.submit()" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white font-semibold text-gray-700 outline-none">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="px-6 py-2.5 bg-[#7a8b3d] hover:bg-[#687733] text-white font-bold rounded-lg text-sm shadow transition flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
        </div>

        <!-- News Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($posts as $post)
            <article class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition group flex flex-col">
                <div class="relative h-48 overflow-hidden bg-gray-100">
                    @if($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-200">
                            <i class="fas fa-newspaper text-5xl"></i>
                        </div>
                    @endif
                    <span class="absolute top-3 left-3 bg-[#7a8b3d] text-white text-[11px] font-bold px-3 py-1 rounded-full shadow">
                        {{ $post->category->name ?? 'Informasi' }}
                    </span>
                </div>

                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 text-xs text-gray-500 mb-3">
                            <span><i class="far fa-clock text-[#7a8b3d] mr-1"></i>{{ $post->created_at->format('d M Y') }}</span>
                            <span>•</span>
                            <span><i class="far fa-user text-[#7a8b3d] mr-1"></i>{{ $post->user->name ?? 'admin' }}</span>
                        </div>

                        <h2 class="text-base font-bold text-[#1a365d] group-hover:text-[#7a8b3d] transition leading-snug mb-3">
                            <a href="{{ url('/informasi/' . $post->slug) }}">{{ Str::limit($post->title, 70) }}</a>
                        </h2>

                        <p class="text-gray-600 text-xs leading-relaxed line-clamp-3 mb-4">
                            {{ Str::limit(strip_tags($post->content), 120) }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between text-xs">
                        <a href="{{ url('/informasi/' . $post->slug) }}" class="text-[#7a8b3d] font-bold hover:underline flex items-center gap-1">
                            Baca Selengkapnya <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </article>
            @empty
            <div class="col-span-full py-16 text-center bg-white rounded-xl border border-gray-100 shadow-sm">
                <i class="fas fa-newspaper text-5xl text-gray-300 mb-3"></i>
                <h3 class="font-bold text-gray-700 text-lg mb-1">Belum Ada Informasi</h3>
                <p class="text-gray-500 text-xs">Tidak ditemukan artikel berita atau informasi sesuai kata kunci pencarian Anda.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-10">
            {{ $posts->links() }}
        </div>

    </div>
</div>
@endsection
