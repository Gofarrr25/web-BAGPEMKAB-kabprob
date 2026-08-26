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
        
        @php
            // Fungsi sentral untuk menghasilkan UI block berdasarkan ekstensi dan URL
            // Memastikan 100% konsistensi antara file dari pdf_file dan dari CKEditor
            $generateAttachmentUI = function($src, $filename, $ext) {
                if ($ext === 'pdf') {
                    // IMPLEMENTASI REFERENCE DARI STANDAR PELAYANAN PUBLIK
                    return '
                    <div class="mb-8 w-full">
                        <div class="flex">
                            <div class="_df_button bg-[#729b48] hover:bg-[#5f8439] text-white px-5 py-2.5 flex items-center justify-center font-semibold text-sm cursor-pointer transition shadow-sm" source="'.$src.'" style="min-width: 150px;">
                                <div class="flex items-center gap-2 pointer-events-none">
                                    <i class="fas fa-book-reader"></i> Baca (Klik)
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-900 overflow-hidden shadow-sm border border-gray-200" style="height: 75vh;">
                            <iframe src="'.$src.'" class="w-full h-full border-0" style="background-color: #323639;"></iframe>
                        </div>
                    </div>';
                } elseif (in_array($ext, ['doc', 'docx'])) {
                    return '
                    <div class="mb-8 p-4 border border-blue-200 bg-blue-50/50 rounded-xl flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-blue-100 rounded-lg text-blue-600">
                                <i class="fas fa-file-word text-2xl"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-800 text-sm">Dokumen Microsoft Word</div>
                                <div class="text-xs text-gray-500 mt-0.5">'.$filename.'</div>
                            </div>
                        </div>
                        <a href="'.$src.'" download class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-all flex items-center gap-2">
                            <i class="fas fa-download"></i> <span class="hidden sm:inline">Unduh</span>
                        </a>
                    </div>';
                } elseif (in_array($ext, ['xls', 'xlsx'])) {
                    return '
                    <div class="mb-8 p-4 border border-green-200 bg-green-50/50 rounded-xl flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-green-100 rounded-lg text-green-600">
                                <i class="fas fa-file-excel text-2xl"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-800 text-sm">Dokumen Spreadsheet</div>
                                <div class="text-xs text-gray-500 mt-0.5">'.$filename.'</div>
                            </div>
                        </div>
                        <a href="'.$src.'" download class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-all flex items-center gap-2">
                            <i class="fas fa-download"></i> <span class="hidden sm:inline">Unduh</span>
                        </a>
                    </div>';
                } elseif (in_array($ext, ['zip', 'rar', '7z'])) {
                    return '
                    <div class="mb-8 p-4 border border-yellow-200 bg-yellow-50/50 rounded-xl flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-yellow-100 rounded-lg text-yellow-600">
                                <i class="fas fa-file-archive text-2xl"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-800 text-sm">File Arsip Terkompresi</div>
                                <div class="text-xs text-gray-500 mt-0.5">'.$filename.'</div>
                            </div>
                        </div>
                        <a href="'.$src.'" download class="bg-yellow-600 hover:bg-yellow-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-all flex items-center gap-2">
                            <i class="fas fa-download"></i> <span class="hidden sm:inline">Unduh</span>
                        </a>
                    </div>';
                }
                return null;
            };
        @endphp

        <div class="bg-white p-6 md:p-10 rounded-lg shadow-xl border border-gray-100 @if(!isset($page) || !$page->pdf_file) min-h-[40vh] @endif">
            
            @if(isset($page) && $page->pdf_file)
                @php 
                    $pdfUrl = asset('storage/' . $page->pdf_file); 
                    $pdfExt = strtolower(pathinfo($page->pdf_file, PATHINFO_EXTENSION));
                    $pdfFilename = basename($page->pdf_file);
                    $pdfUI = $generateAttachmentUI($pdfUrl, $pdfFilename, $pdfExt);
                @endphp
                
                @if($pdfUI)
                    {!! $pdfUI !!}
                @else
                    <!-- Fallback untuk format lain yang diunggah ke pdf_file agar tidak masuk ke iframe (mencegah auto-download) -->
                    <div class="mb-8 p-4 border border-gray-200 bg-gray-50/50 rounded-xl flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-gray-200 rounded-lg text-gray-600">
                                <i class="fas fa-paperclip text-2xl"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-800 text-sm">Lampiran Berkas</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $pdfFilename }}</div>
                            </div>
                        </div>
                        <a href="{{ $pdfUrl }}" download class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-all flex items-center gap-2">
                            <i class="fas fa-download"></i> <span class="hidden sm:inline">Unduh</span>
                        </a>
                    </div>
                @endif
            @endif
            
            @if(isset($page) && $page)
                @if($page->image && !Str::endsWith(strtolower($page->image), '.pdf'))
                    <div class="w-full mb-8 mt-4 text-center flex justify-center">
                        <img src="{{ asset('storage/' . $page->image) }}" class="max-w-full h-auto rounded shadow-sm border border-gray-100" alt="{{ $title }}">
                    </div>
                @endif
                
                @if($page->content)
                    @php
                        $processedContent = $page->content;

                        // Fungsi untuk menghasilkan UI block berdasarkan ekstensi dan URL
                        $generateAttachmentUI = function($src, $filename, $ext) {
                            if ($ext === 'pdf') {
                                return '
                                <div class="mb-8 w-full">
                                    <div class="flex">
                                        <div class="_df_button bg-[#729b48] hover:bg-[#5f8439] text-white px-5 py-2.5 flex items-center justify-center font-semibold text-sm cursor-pointer transition shadow-sm" source="'.$src.'" style="min-width: 150px;">
                                            <div class="flex items-center gap-2 pointer-events-none">
                                                <i class="fas fa-book-reader"></i> Baca (Klik)
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-gray-900 overflow-hidden shadow-sm border border-gray-200" style="height: 75vh;">
                                        <iframe src="'.$src.'" class="w-full h-full border-0" style="background-color: #323639;"></iframe>
                                    </div>
                                </div>';
                            } elseif (in_array($ext, ['doc', 'docx'])) {
                                return '
                                <div class="mb-8 p-4 border border-blue-200 bg-blue-50/50 rounded-xl flex items-center justify-between shadow-sm">
                                    <div class="flex items-center gap-4">
                                        <div class="p-3 bg-blue-100 rounded-lg text-blue-600">
                                            <i class="fas fa-file-word text-2xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800 text-sm">Dokumen Microsoft Word</div>
                                            <div class="text-xs text-gray-500 mt-0.5">'.$filename.'</div>
                                        </div>
                                    </div>
                                    <a href="'.$src.'" download class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-all flex items-center gap-2">
                                        <i class="fas fa-download"></i> <span class="hidden sm:inline">Unduh</span>
                                    </a>
                                </div>';
                            } elseif (in_array($ext, ['xls', 'xlsx'])) {
                                return '
                                <div class="mb-8 p-4 border border-green-200 bg-green-50/50 rounded-xl flex items-center justify-between shadow-sm">
                                    <div class="flex items-center gap-4">
                                        <div class="p-3 bg-green-100 rounded-lg text-green-600">
                                            <i class="fas fa-file-excel text-2xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800 text-sm">Dokumen Spreadsheet</div>
                                            <div class="text-xs text-gray-500 mt-0.5">'.$filename.'</div>
                                        </div>
                                    </div>
                                    <a href="'.$src.'" download class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-all flex items-center gap-2">
                                        <i class="fas fa-download"></i> <span class="hidden sm:inline">Unduh</span>
                                    </a>
                                </div>';
                            } elseif (in_array($ext, ['zip', 'rar', '7z'])) {
                                return '
                                <div class="mb-8 p-4 border border-yellow-200 bg-yellow-50/50 rounded-xl flex items-center justify-between shadow-sm">
                                    <div class="flex items-center gap-4">
                                        <div class="p-3 bg-yellow-100 rounded-lg text-yellow-600">
                                            <i class="fas fa-file-archive text-2xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800 text-sm">File Arsip Terkompresi</div>
                                            <div class="text-xs text-gray-500 mt-0.5">'.$filename.'</div>
                                        </div>
                                    </div>
                                    <a href="'.$src.'" download class="bg-yellow-600 hover:bg-yellow-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-all flex items-center gap-2">
                                        <i class="fas fa-download"></i> <span class="hidden sm:inline">Unduh</span>
                                    </a>
                                </div>';
                            }
                            return null;
                        };

                        // Pass 1: Backward compatibility untuk data existing dimana file non-gambar (pdf, docx, dll) telanjur tersimpan sebagai tag <img> oleh CKEditor
                        $processedContent = preg_replace_callback('/(?:<figure[^>]*>)?\s*<img[^>]*src=["\']([^"\']+)["\'][^>]*>\s*(?:<\/figure>)?/i', function($matches) use ($generateAttachmentUI) {
                            $src = $matches[1];
                            $ext = strtolower(pathinfo(parse_url($src, PHP_URL_PATH), PATHINFO_EXTENSION));
                            $filename = basename(parse_url($src, PHP_URL_PATH));
                            
                            // Jika format yang valid untuk image, biarkan sebagai image
                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                                return $matches[0];
                            }
                            
                            $ui = $generateAttachmentUI($src, $filename, $ext);
                            return $ui ? $ui : $matches[0];
                        }, $processedContent);

                        // Pass 2: Parser untuk mekanisme baru dimana dokumen disisipkan sebagai tag <a class="document-attachment">
                        $processedContent = preg_replace_callback('/<a[^>]*href=["\']([^"\']+)["\'][^>]*class=["\'][^"\']*document-attachment[^"\']*["\'][^>]*>(.*?)<\/a>/i', function($matches) use ($generateAttachmentUI) {
                            $src = $matches[1];
                            $filename = strip_tags($matches[2]);
                            $ext = strtolower(pathinfo(parse_url($src, PHP_URL_PATH), PATHINFO_EXTENSION));
                            
                            $ui = $generateAttachmentUI($src, $filename, $ext);
                            
                            // Fallback jika ekstensi tidak dikenali
                            if (!$ui) {
                                return '
                                <div class="mb-8 p-4 border border-gray-200 bg-gray-50/50 rounded-xl flex items-center justify-between shadow-sm">
                                    <div class="flex items-center gap-4">
                                        <div class="p-3 bg-gray-200 rounded-lg text-gray-600">
                                            <i class="fas fa-paperclip text-2xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800 text-sm">Lampiran Berkas</div>
                                            <div class="text-xs text-gray-500 mt-0.5">'.$filename.'</div>
                                        </div>
                                    </div>
                                    <a href="'.$src.'" download class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-all flex items-center gap-2">
                                        <i class="fas fa-download"></i> <span class="hidden sm:inline">Unduh</span>
                                    </a>
                                </div>';
                            }
                            return $ui;
                        }, $processedContent);
                    @endphp
                    <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                        {!! $processedContent !!}
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
