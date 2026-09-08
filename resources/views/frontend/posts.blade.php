@extends('layouts.public')

@section('title', 'Informasi & Berita Terkini - Bagian Pemerintahan')

@section('content')
<div class="bg-gray-50 min-h-screen pb-16">
    <div class="container mx-auto px-4 lg:px-8 mt-8">
        
        <!-- Search & Filter Bar -->
        <div class="bg-white p-4 md:p-4 md:p-6 rounded-xl shadow-sm border border-gray-100 mb-8 flex flex-col md:flex-row gap-4 justify-between items-center">
            <form action="{{ url('/informasi') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari berita atau kata kunci..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-blue outline-none">
                </div>

                <div class="w-full md:w-56">
                    <select name="category" onchange="this.form.submit()" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white font-semibold text-gray-700 outline-none">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="px-6 py-2.5 bg-brand-blue hover:bg-brand-blue-hover text-white font-bold rounded-lg text-sm shadow transition flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
        </div>

        <!-- News Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-8">
            @forelse($posts as $post)
            <article class="bg-white shadow-sm hover:shadow-md transition group flex flex-col h-full">
                <a href="{{ url('/informasi/' . $post->slug) }}" class="block flex-grow flex flex-col">
                    <div class="relative h-60 md:h-64 w-full bg-gray-100">
                        @if($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-200">
                                <i class="fas fa-newspaper text-5xl"></i>
                            </div>
                        @endif
                        
                        @php
                            $monthsId = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                            $day = $post->created_at->format('d');
                            $monthInt = (int)$post->created_at->format('m');
                            $year = $post->created_at->format('Y');
                            $monthName = $monthsId[$monthInt];
                            $formattedDate = strtoupper($day . ' ' . $monthName . ' ' . $year);
                        @endphp
                        
                        <div class="absolute -bottom-5 left-6 md:left-7 bg-brand-blue text-white text-[13px] md:text-sm font-bold px-5 py-2.5 shadow-sm whitespace-nowrap">
                            {{ $formattedDate }}
                        </div>
                    </div>

                    <div class="p-6 md:p-7 pt-10 md:pt-10 flex-1 flex flex-col">
                        <p class="text-slate-500 text-[13px] md:text-sm mb-1 font-normal tracking-wide">{{ $post->category->name ?? 'Pemerintahan' }}</p>
                        <h2 class="text-base md:text-[17px] font-medium text-brand-blue leading-snug group-hover:text-brand-blue-hover transition">
                            {{ $post->title }}
                        </h2>
                    </div>
                </a>
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
