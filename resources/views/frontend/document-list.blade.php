@extends('layouts.public')

@section('title', $categoryName . ' - Bagian Pemerintahan')

@section('content')
@push('scripts')
<!-- dFlip 3D Flipbook Libraries -->
<link href="https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook@latest/dflip/css/dflip.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook@latest/dflip/css/themify-icons.min.css" rel="stylesheet">
<link href="{{ asset('css/dflip-custom.css') }}?v={{ time() }}" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    var dFlipLocation = "https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook@latest/dflip/";
    var dFlipOptions = {
        backgroundColor: "transparent"
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook@latest/dflip/js/dflip.min.js"></script>
@endpush

<div class="container mx-auto px-4 lg:px-8 py-12 min-h-screen">
    <!-- Filter & Search Bar -->
    <form method="GET" action="" class="flex flex-col md:flex-row gap-4 mb-8">
        <div class="w-full md:w-1/4">
            <select name="tahun" onchange="this.form.submit()" class="w-full border border-gray-300 px-4 py-2 text-gray-700 outline-none rounded bg-white text-sm">
                <option value="">Semua Tahun</option>
                <option value="2026" {{ request('tahun') == '2026' ? 'selected' : '' }}>2026</option>
                <option value="2025" {{ request('tahun') == '2025' ? 'selected' : '' }}>2025</option>
                <option value="2024" {{ request('tahun') == '2024' ? 'selected' : '' }}>2024</option>
            </select>
        </div>
        <div class="w-full md:w-1/4">
            <input type="text" readonly value="{{ $categoryName }}" class="w-full border border-gray-300 px-4 py-2 text-gray-700 outline-none rounded bg-gray-100 text-sm font-semibold">
        </div>
        <div class="w-full md:w-1/2 flex">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Judul Dokumen..." class="w-full border border-gray-300 px-4 py-2 text-gray-700 outline-none rounded-l text-sm">
            <button type="submit" class="bg-brand-blue text-white px-6 py-2 hover:bg-brand-blue-hover transition rounded-r">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>

    <!-- Data Table Header & Entries Info -->
    <div class="flex justify-between items-center mb-4 text-sm text-gray-600">
        <div class="text-xs text-gray-500">
            Menampilkan dokumen publik terdaftar
        </div>
        <div class="text-xs font-mono text-gray-500">
            Total: {{ $documents->total() }} Dokumen
        </div>
    </div>

    <!-- The Document Table -->
    <div class="overflow-x-auto bg-white border border-gray-200 shadow-sm rounded-lg w-full">
        <table class="w-full text-left border-collapse text-sm min-w-[800px]">
            <thead>
                <tr class="bg-brand-blue text-white whitespace-nowrap">
                    <th class="px-4 py-3 font-bold border-r border-white/20 w-16 text-center">No</th>
                    <th class="px-4 py-3 font-bold border-r border-white/20">Judul Dokumen</th>
                    <th class="px-4 py-3 font-bold border-r border-white/20 w-28 text-center">Lihat PDF</th>
                    <th class="px-4 py-3 font-bold border-r border-white/20 w-24 text-center">Zip</th>
                    <th class="px-4 py-3 font-bold border-r border-white/20">Kategori</th>
                    <th class="px-4 py-3 font-bold text-center">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $index => $doc)
                @php
                    $isPdf = false;
                    $isZip = false;
                    $pdfUrl = null;
                    $zipUrl = null;
                    
                    if (!empty($doc->file_path)) {
                        $ext = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                        if ($ext === 'pdf') {
                            $isPdf = true;
                            $pdfUrl = asset('storage/' . $doc->file_path);
                        } elseif (in_array($ext, ['zip', 'rar', '7z'])) {
                            $isZip = true;
                            $zipUrl = asset('storage/' . $doc->file_path);
                        }
                    }
                    
                    if (!empty($doc->zip_path)) {
                        $ext = strtolower(pathinfo($doc->zip_path, PATHINFO_EXTENSION));
                        if (in_array($ext, ['zip', 'rar', '7z'])) {
                            $isZip = true;
                            $zipUrl = asset('storage/' . $doc->zip_path);
                        } elseif ($ext === 'pdf' && !$isPdf) {
                            $isPdf = true;
                            $pdfUrl = asset('storage/' . $doc->zip_path);
                        }
                    }
                @endphp
                <tr class="{{ $index % 2 == 0 ? 'bg-gray-50/60' : 'bg-white' }} border-b border-gray-100 hover:bg-brand-blue-light/50 transition">
                    <td class="px-4 py-4 text-center text-gray-600 border-r border-gray-100 font-mono text-xs whitespace-nowrap">{{ $documents->firstItem() + $index }}</td>
                    
                    <!-- Judul Dokumen (Klik langsung buka PDF Modal) -->
                    <td class="px-4 py-4 text-gray-800 font-medium border-r border-gray-100 min-w-[250px]">
                        <div class="flex items-center gap-2.5">
                            @if($isZip)
                                <i class="fas fa-file-archive text-amber-500 text-base"></i>
                                <span>{{ $doc->title }}</span>
                            @elseif($isPdf)
                                <i class="fas fa-file-pdf text-red-500 text-base"></i>
                                <span data-pdf-url="{{ $pdfUrl }}" data-pdf-title="{{ $doc->title }}" onclick="openPdfModalFromEl(this)" class="cursor-pointer hover:text-brand-blue-hover hover:underline font-semibold text-gray-900 transition">
                                    {{ $doc->title }}
                                </span>
                            @else
                                <i class="fas fa-file-alt text-gray-400 text-base"></i>
                                <span>{{ $doc->title }}</span>
                            @endif
                        </div>
                    </td>

                    <!-- Tombol Lihat PDF (Modal Pop-Up tanpa langsung download) -->
                    <td class="px-4 py-4 text-center border-r border-gray-100 whitespace-nowrap">
                        @if($isPdf)
                            <button type="button" 
                                    data-pdf-url="{{ $pdfUrl }}" 
                                    data-pdf-title="{{ $doc->title }}" 
                                    onclick="openPdfModalFromEl(this)" 
                                    class="inline-flex bg-red-600 text-white w-9 h-9 items-center justify-center rounded-lg hover:bg-red-700 transition shadow-sm mx-auto cursor-pointer" 
                                    title="Klik untuk Baca/Lihat PDF">
                                <i class="fas fa-file-pdf text-base"></i>
                            </button>
                        @else
                            <span class="text-gray-300">-</span>
                        @endif
                    </td>

                    <!-- Tombol Zip -->
                    <td class="px-4 py-4 text-center border-r border-gray-100 whitespace-nowrap">
                        @if($isZip)
                            <a href="{{ $zipUrl }}" download class="inline-flex bg-amber-600 text-white w-9 h-9 items-center justify-center rounded-lg hover:bg-amber-700 transition shadow-sm mx-auto" title="Unduh Zip">
                                <i class="fas fa-file-archive text-base"></i>
                            </a>
                        @else
                            <span class="text-gray-300">-</span>
                        @endif
                    </td>

                    <td class="px-4 py-4 text-gray-600 border-r border-gray-100 text-xs whitespace-nowrap">{{ $doc->category }}</td>
                    <td class="px-4 py-4 text-gray-600 text-center text-xs font-mono whitespace-nowrap">{{ $doc->created_at->format('d-m-Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-500 bg-white">
                        <i class="fas fa-folder-open text-4xl text-gray-300 mb-2"></i>
                        <p class="font-bold text-gray-700">Belum Ada Dokumen</p>
                        <p class="text-xs text-gray-400">Tidak ada dokumen yang ditemukan untuk kategori ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer Pagination -->
    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 text-sm text-gray-600 gap-4">
        <div class="text-xs text-gray-500">
            Showing {{ $documents->firstItem() ?? 0 }} to {{ $documents->lastItem() ?? 0 }} of {{ $documents->total() }} entries
        </div>
        <div>
            {{ $documents->links('pagination::tailwind') }}
        </div>
    </div>
</div>

<!-- MODAL PDF VIEWER POP-UP (Persis Sesuai Tampilan Gambar Reference) -->
<div id="pdfViewerModal" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-black/80 p-2 md:p-4 md:p-6 backdrop-blur-xs transition-all duration-300">
    <div class="bg-white w-full max-w-6xl rounded-lg shadow-2xl overflow-hidden flex flex-col h-[92vh] relative border border-gray-700">
        
        <!-- Modal Header Bar -->
        <div class="bg-gray-900 text-white px-4 py-3 flex items-center justify-between border-b border-gray-800 flex-shrink-0">
            <div class="flex items-center gap-3 min-w-0 pr-4">
                <div class="w-8 h-8 rounded bg-red-600 flex items-center justify-center text-white flex-shrink-0">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <h3 id="pdfModalTitle" class="font-bold text-sm md:text-base text-gray-100 truncate">
                    Pratinjau Dokumen PDF
                </h3>
            </div>

            <button type="button" onclick="closePdfModal()" class="text-gray-400 hover:text-white text-3xl font-bold focus:outline-none transition px-2 leading-none">
                &times;
            </button>
        </div>

        <!-- Embedded PDF Viewer Container -->
        <div class="flex-1 bg-gray-800 relative w-full h-full">
            <iframe id="pdfModalIframe" src="" class="w-full h-full border-0 bg-white" title="Pratinjau Dokumen PDF">
                <p class="text-white p-4 text-center">Browser Anda tidak mendukung iframe PDF. Silakan klik tombol Baca untuk melihat dokumen.</p>
            </iframe>
        </div>

        <!-- Modal Footer Bar (Sesuai Tombol "Baca" Kuning & "Close" Biru pada Gambar) -->
        <div class="bg-gray-100 px-6 py-3 border-t border-gray-200 flex items-center justify-between flex-shrink-0">
            <div class="text-xs text-gray-500 hidden sm:block">
                <i class="fas fa-info-circle text-brand-blue mr-1"></i> Klik <strong>Baca</strong> untuk membuka layar penuh di tab baru, atau <strong>Close</strong> untuk menutup modal.
            </div>

            <div class="flex items-center gap-3 ml-auto">
                <!-- Tombol "Baca" Kuning (Sesuai Gambar User) -->
                <button type="button" id="pdfModalReadBtn" onclick="triggerFlipbook(this)" data-url="" class="bg-amber-400 hover:bg-amber-500 text-gray-900 font-bold px-6 py-2 rounded shadow flex items-center gap-2 text-sm transition">
                    <i class="fas fa-book-reader"></i> Baca
                </button>

                <!-- Tombol "Close" Biru (Sesuai Gambar User) -->
                <button type="button" onclick="closePdfModal()" class="bg-brand-blue hover:bg-brand-blue-hover text-white font-bold px-6 py-2 rounded shadow text-sm transition">
                    Close
                </button>
            </div>
        </div>

    </div>
</div>

<script>
function openPdfModal(url, title) {
    const modal = document.getElementById('pdfViewerModal');
    const iframe = document.getElementById('pdfModalIframe');
    const titleEl = document.getElementById('pdfModalTitle');
    const readBtn = document.getElementById('pdfModalReadBtn');

    titleEl.textContent = title;
    iframe.src = url;
    readBtn.setAttribute('data-url', url);

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function openPdfModalFromEl(el) {
    const url = el.getAttribute('data-pdf-url');
    const title = el.getAttribute('data-pdf-title');
    if (url) {
        openPdfModal(url, title);
    }
}

function closePdfModal() {
    const modal = document.getElementById('pdfViewerModal');
    const iframe = document.getElementById('pdfModalIframe');

    iframe.src = '';
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePdfModal();
    }
});

function triggerFlipbook(btn) {
    var url = btn.getAttribute('data-url');
    if (!url) return;
    
    // Tutup modal iframe bawaan browser jika sedang terbuka
    closePdfModal();
    
    // Gunakan fungsi API dFlip untuk membuat lightbox (jika didukung) atau inject _df_button
    $('.dflip-dynamic-btn').remove();
    var dfBtn = $('<div class="_df_button dflip-dynamic-btn" source="' + url + '" style="display:none;"></div>');
    $('body').append(dfBtn);
    
    // Eksekusi secara otomatis jika dFlip sudah mendeteksi, atau klik manual
    setTimeout(function() {
        dfBtn.trigger('click');
    }, 100);
}
</script>
@endsection

