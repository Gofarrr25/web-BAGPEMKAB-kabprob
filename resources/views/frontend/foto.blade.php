@extends('layouts.public')

@section('title', 'Galeri Foto - Bagian Pemerintahan')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">

    <div class="container mx-auto px-4 lg:px-8">
        
        <!-- Search Bar -->
        <div class="bg-white p-4 md:p-4 md:p-6 rounded-xl shadow-sm border border-gray-100 mb-8 flex justify-between items-center">
            <form action="{{ url('/galeri-foto') }}" method="GET" class="w-full flex flex-col md:flex-row gap-4">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Galeri Foto..." class="w-full flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-blue outline-none">
                <button type="submit" class="px-6 py-2.5 bg-brand-blue hover:bg-[#122543] text-white font-bold rounded-lg text-sm shadow transition flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
        </div>

        <!-- Photo Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($photos as $photo)
                @php $itemCount = ($photo->file_path ? 1 : 0) + ($photo->galleryItems ? $photo->galleryItems->count() : 0); @endphp
                
                <a href="{{ route('frontend.foto.detail', $photo->id) }}" class="relative w-full aspect-[4/3] rounded-lg overflow-hidden group cursor-pointer block shadow-lg bg-gray-200">
                    <!-- Image -->
                    @if($photo->file_path)
                        <img src="{{ asset('storage/' . $photo->file_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" alt="{{ $photo->title }}">
                    @elseif($photo->galleryItems && $photo->galleryItems->first())
                        <img src="{{ asset('storage/' . $photo->galleryItems->first()->file_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" alt="{{ $photo->title }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <i class="fas fa-image text-4xl"></i>
                        </div>
                    @endif
                    
                    <!-- Dark Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-80 group-hover:opacity-100 transition duration-300"></div>

                    @if($itemCount > 1)
                        <!-- Top Right Badge (Count) - Hanya untuk Album -->
                        <div class="absolute top-4 right-4 bg-black/40 backdrop-blur-sm text-white text-sm font-semibold px-4 py-1.5 rounded-full flex items-center gap-2">
                            <i class="far fa-image"></i> {{ $itemCount }}
                        </div>
                    @endif

                    <!-- Bottom Left Content -->
                    <div class="absolute bottom-6 left-6 right-6 flex flex-col items-start">
                        @if($itemCount > 1)
                            <span class="text-brand-blue-pale font-semibold text-sm mb-1 block">Album</span>
                        @endif
                        
                        <h3 class="text-white font-extrabold text-xl md:text-2xl lg:text-3xl line-clamp-2 leading-tight mb-4 group-hover:text-brand-blue-pale transition">{{ $photo->title }}</h3>
                        
                        @if($itemCount > 1)
                            <!-- Arrow Button - Hanya untuk Album -->
                            <div class="w-12 h-12 bg-brand-blue hover:bg-brand-blue-hover rounded-full flex items-center justify-center text-white transition-colors duration-300 shadow-md group-hover:scale-105">
                                <i class="fas fa-arrow-right text-xl"></i>
                            </div>
                        @else
                            <!-- Tanggal untuk single photo -->
                            <p class="text-xs text-gray-300 flex items-center gap-1 opacity-80 group-hover:opacity-100 transition">
                                <i class="far fa-calendar-alt"></i> {{ $photo->created_at->format('d M Y') }}
                            </p>
                        @endif
                    </div>
                </a>
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

@endsection
