@extends('layouts.public')

@section('title', $title . ' - Bagian Pemerintahan')

@push('meta')
@if(isset($page) && $page)
    <meta name="description" content="{{ $page->seo_description }}">
    <link rel="canonical" href="{{ url('/page/' . $page->slug) }}">
    <meta property="og:title" content="{{ $page->seo_title }}">
    <meta property="og:description" content="{{ $page->seo_description }}">
    <meta property="og:url" content="{{ url('/page/' . $page->slug) }}">
    <meta property="og:type" content="article">
    @if($page->image)
        <meta property="og:image" content="{{ asset('storage/' . $page->image) }}">
    @endif
@endif
@endpush

@push('scripts')
<!-- dFlip 3D Flipbook Libraries (Sesuai Referensi Diskominfo) -->
<link href="https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook@latest/dflip/css/dflip.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook@latest/dflip/css/themify-icons.min.css" rel="stylesheet">
<link href="{{ asset('css/dflip-custom.css') }}?v={{ time() }}" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    // WAJIB: Atur lokasi file worker pdf.js untuk dFlip
    var dFlipLocation = "https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook@latest/dflip/";
    var dFlipOptions = {
        backgroundColor: "transparent"
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook@latest/dflip/js/dflip.min.js"></script>
<script>
// Biarkan _df_button auto-initialize bawaan dFlip
</script>
@endpush

@section('content')
<div class="bg-gray-100 min-h-screen pb-12">
    <div class="container mx-auto px-4 lg:px-8 mt-8">
        
        <div class="bg-white p-6 md:p-10 rounded-lg shadow-xl border border-gray-100 @if(!isset($page) || !$page->pdf_file) min-h-[40vh] @endif">
            
            @if(isset($page) && $page->pdf_file)
                @php $pdfUrl = asset('storage/' . $page->pdf_file); @endphp
                <!-- Jika Halaman Memiliki File PDF, Tampilkan PDF Viewer Interaktif -->
                <div class="mb-8 w-full">
                    <!-- Tab Baca (Klik 2x) -->
                    <div class="flex">
                        <div class="_df_button bg-[#729b48] hover:bg-[#5f8439] text-white px-5 py-2.5 flex items-center justify-center font-semibold text-sm cursor-pointer transition shadow-sm" source="{{ $pdfUrl }}" style="min-width: 150px;">
                            <div class="flex items-center gap-2 pointer-events-none">
                                <i class="fas fa-book-reader"></i> Baca (Klik 2x)
                            </div>
                        </div>
                    </div>
                    
                    <!-- Native PDF Viewer Container -->
                    <div class="bg-gray-900 overflow-hidden shadow-sm border border-gray-200" style="height: 75vh;">
                        <iframe src="{{ $pdfUrl }}" class="w-full h-full border-0" style="background-color: #323639;"></iframe>
                    </div>
                </div>
            @endif
            
            @if(isset($page) && $page)
                @if($page->image && !Str::endsWith(strtolower($page->image), '.pdf'))
                    <div class="w-full mb-8 mt-4 text-center flex justify-center">
                        <img src="{{ asset('storage/' . $page->image) }}" class="max-w-full h-auto rounded shadow-sm border border-gray-100" alt="{{ $title }}">
                    </div>
                @endif
                
                @if($page->content)
                    <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                        {!! $page->content !!}
                    </div>
                @endif
                
                @if(!$page->image && !$page->pdf_file && !$page->content)
                    <div class="text-center py-20 text-gray-400">
                        <i class="fas fa-file-alt text-6xl mb-4 text-gray-200"></i>
                        <p>Konten halaman sedang diperbarui oleh Admin.</p>
                    </div>
                @endif
            @else
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded text-yellow-800">
                    <h3 class="font-bold text-lg mb-2"><i class="fas fa-tools mr-2"></i>Status Pengembangan</h3>
                    <p>Halaman <strong>{{ $title }}</strong> ini belum dibuat di sistem CMS Admin.</p>
                    <p class="mt-2 text-sm text-yellow-600">Silakan login sebagai Admin, buka menu <strong>Halaman Statis</strong>, lalu kelola isi konten untuk URL: <code>{{ $slug }}</code></p>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
