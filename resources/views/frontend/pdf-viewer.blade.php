@extends('layouts.public')

@section('title', $title . ' - Bagian Pemerintahan')

@push('scripts')
<!-- dFlip 3D Flipbook Libraries (Sesuai Referensi Diskominfo) -->
<link href="https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook@latest/dflip/css/dflip.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook@latest/dflip/css/themify-icons.min.css" rel="stylesheet">
<link href="{{ asset('css/dflip-custom.css') }}?v={{ time() }}" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    // WAJIB: Atur lokasi file worker pdf.js untuk dFlip agar tidak 404
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
        @php
            $pdfUrl = null;
            if (isset($page) && $page->pdf_file) {
                $pdfUrl = asset('storage/' . $page->pdf_file);
            } elseif (isset($page) && $page->image && Str::endsWith(strtolower($page->image), '.pdf')) {
                $pdfUrl = asset('storage/' . $page->image);
            }
        @endphp
        
        <div class="bg-white p-6 md:p-10 rounded-lg shadow-xl border border-gray-100 min-h-[40vh]">
            
            @if($pdfUrl)
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
            @else
                <!-- Fallback jika PDF belum diunggah -->
                <div class="flex-1 bg-gray-800 p-8 flex flex-col items-center justify-center text-center rounded-lg mb-8">
                    <div class="w-16 h-16 rounded-full bg-yellow-500/20 text-yellow-400 flex items-center justify-center text-3xl mb-4">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="font-bold text-lg text-white mb-2">Dokumen PDF Belum Diunggah</h3>
                    <p class="text-gray-400 text-sm max-w-md mb-4">File PDF Standar Pelayanan untuk <strong>{{ $title }}</strong> belum diunggah di Admin CMS.</p>
                    <p class="text-xs text-gray-500">Silakan login sebagai Admin -> buka <strong>Halaman Statis</strong> -> Edit <code>{{ $slug }}</code> -> Unggah File PDF.</p>
                </div>
            @endif

            <!-- Menampilkan Foto (Jika Ada) -->
            @if(isset($page) && $page->image && !Str::endsWith(strtolower($page->image), '.pdf'))
                <div class="w-full mb-8 mt-4 text-center flex justify-center">
                    <img src="{{ asset('storage/' . $page->image) }}" class="max-w-full h-auto rounded shadow-sm border border-gray-100" alt="{{ $title }}">
                </div>
            @endif
            
            <!-- Teks Keterangan Konten Halaman (jika ada) -->
            @if(isset($page) && $page->content)
                <div class="text-gray-700 text-sm leading-relaxed prose max-w-none">
                    {!! $page->content !!}
                </div>
            @else
                <div class="text-gray-700 text-sm leading-relaxed">
                    <p><strong>{{ $title }}</strong> adalah tolak ukur yang menjadi pedoman dan acuan penilaian dalam penyelenggaraan pelayanan publik, bertujuan memberikan kepastian, transparansi, dan akuntabilitas penyelenggaraan layanan di lingkungan Bagian Pemerintahan Sekretariat Daerah Kabupaten Probolinggo.</p>
                </div>
            @endif
            
        </div>

    </div>
</div>
@endsection
