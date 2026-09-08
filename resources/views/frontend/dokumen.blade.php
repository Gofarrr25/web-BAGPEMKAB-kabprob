@extends('layouts.public')

@section('title', 'Dokumen ' . $kategori . ' - Bagian Pemerintahan')

@push('scripts')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style>
    /* Custom DataTables Styling to match screenshot */
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 4px;
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 6px 10px;
        margin-left: 8px;
    }
    table.dataTable thead th {
        background-color: #1a365d; /* brand-blue */ /* brand-blue */
        color: white;
        border-bottom: none;
        padding: 12px 18px;
    }
    table.dataTable tbody td {
        padding: 12px 18px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    table.dataTable.no-footer {
        border-bottom: 1px solid #e2e8f0;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #1a365d;
        color: white !important;
        border: 1px solid #1a365d;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #cbd5e1;
        color: black !important;
        border: 1px solid #cbd5e1;
    }
    
    /* PDF Viewer Modal Overlay */
    #pdf-modal {
        backdrop-filter: blur(4px);
    }
</style>
@endpush

@section('content')
<div class="bg-gray-50 py-12 min-h-[70vh]">
    <div class="container mx-auto px-4 lg:px-8">

        <!-- Filter Area (Optional if DataTables handles search) -->
        <div class="bg-white p-4 rounded-t-lg shadow-sm border border-gray-200 flex flex-wrap gap-4 items-center justify-between mb-0 border-b-0">
            <div class="flex gap-4 flex-wrap">
                <div class="w-56">
                    <div class="relative w-full" id="year-picker-container" data-years="{{ json_encode($availableYears) }}">
                        <button type="button" id="year-picker-btn" class="w-full flex justify-between items-center px-4 py-2 border border-gray-300 rounded outline-none text-gray-700 bg-white hover:bg-gray-50 focus:ring-2 focus:ring-brand-blue transition-all">
                            <span id="year-picker-label" class="font-medium text-sm">
                                <i class="far fa-calendar-alt mr-2 text-gray-500"></i>
                                {{ request('year') ? 'Tahun ' . request('year') : 'Semua Tahun' }}
                            </span>
                            <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                        </button>
                        
                        <div id="year-picker-dropdown" class="absolute left-0 top-full mt-2 w-full max-w-[18rem] bg-white rounded-lg shadow-xl border border-gray-200 z-50 hidden opacity-0 transition-opacity duration-200" style="transform-origin: top left;">
                            <div class="flex justify-between items-center p-3 bg-gray-50 border-b border-gray-100 rounded-t-lg">
                                <button type="button" id="yp-prev" class="p-1.5 rounded hover:bg-gray-200 text-gray-600 transition"><i class="fas fa-angle-double-left"></i></button>
                                <span id="yp-range-label" class="font-bold text-sm text-gray-700 tracking-wide"></span>
                                <button type="button" id="yp-next" class="p-1.5 rounded hover:bg-gray-200 text-gray-600 transition"><i class="fas fa-angle-double-right"></i></button>
                            </div>
                            
                            <div class="p-3">
                                <div id="yp-grid" class="grid grid-cols-4 gap-2 text-sm">
                                    <!-- Tahun di render via JS -->
                                </div>
                                <div class="mt-4 pt-3 border-t border-gray-100">
                                    <button type="button" id="yp-all" class="w-full py-2 bg-brand-blue-light hover:bg-brand-blue-light text-brand-blue font-bold rounded transition text-sm">
                                        Tampilkan Semua Tahun
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-64">
                    <select id="category-filter" class="w-full px-4 py-2 border border-gray-300 outline-none text-gray-700 bg-white">
                        <option value="">Semua Kategori</option>
                        <option value="{{ $kategori }}" selected>{{ $kategori }}</option>
                    </select>
                </div>
            </div>
            <div class="flex">
                <input type="text" id="custom-search" placeholder="Search Here..." class="px-4 py-2 border border-gray-300 outline-none w-64">
                <button id="custom-search-btn" class="bg-brand-blue text-white px-4 py-2 hover:bg-brand-blue-hover transition">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Tabel -->
        <div class="bg-white p-4 md:p-6 shadow-sm border border-gray-200 rounded-b-lg overflow-x-auto w-full">
            <table id="dokumenTable" class="w-full text-left border-collapse min-w-[800px]" style="width:100%">
                <thead>
                    <tr class="whitespace-nowrap">
                        <th class="w-12 text-center">No</th>
                        <th>Judul</th>
                        <th class="w-20 text-center">PDF</th>
                        <th class="w-20 text-center">Zip</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $index => $doc)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="text-center whitespace-nowrap">{{ $index + 1 }}</td>
                            <td class="font-medium text-gray-800">{{ $doc->title }}</td>
                            @php
                                $pdfUrl = null;
                                $zipUrl = null;
                                
                                // Cek file_path
                                if (!empty($doc->file_path)) {
                                    $ext = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                                    if ($ext === 'pdf') {
                                        $pdfUrl = asset('storage/' . $doc->file_path);
                                    } elseif (in_array($ext, ['zip', 'rar', '7z'])) {
                                        $zipUrl = asset('storage/' . $doc->file_path);
                                    }
                                }
                                
                                // Cek zip_path (akan menimpa jika zip_path ada isinya)
                                if (!empty($doc->zip_path)) {
                                    $ext = strtolower(pathinfo($doc->zip_path, PATHINFO_EXTENSION));
                                    if (in_array($ext, ['zip', 'rar', '7z'])) {
                                        $zipUrl = asset('storage/' . $doc->zip_path);
                                    } elseif ($ext === 'pdf' && !$pdfUrl) {
                                        $pdfUrl = asset('storage/' . $doc->zip_path);
                                    }
                                }
                            @endphp
                            <td class="text-center whitespace-nowrap">
                                @if($pdfUrl)
                                    <button data-pdf-url="{{ $pdfUrl }}" onclick="openPdfViewer(this.getAttribute('data-pdf-url'))" class="inline-flex items-center justify-center w-8 h-8 bg-brand-blue hover:bg-brand-blue-hover text-white rounded shadow transition" title="Lihat PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="text-center whitespace-nowrap">
                                @if($zipUrl)
                                    <a href="{{ $zipUrl }}" download class="inline-flex items-center justify-center w-8 h-8 bg-amber-500 hover:bg-amber-600 text-white rounded shadow transition" title="Download ZIP">
                                        <i class="fas fa-file-archive"></i>
                                    </a>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="text-gray-600 whitespace-nowrap">{{ $doc->category }}</td>
                            <td class="text-gray-600 whitespace-nowrap" data-sort="{{ $doc->document_date ?? $doc->created_at }}">
                                {{ $doc->document_date ? \Carbon\Carbon::parse($doc->document_date)->format('d-m-Y') : $doc->created_at->format('d-m-Y') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Modal PDF Viewer -->
<div id="pdf-modal" class="fixed inset-0 z-[9999] bg-black/70 hidden flex-col items-center justify-center">
    <!-- PDF Container -->
    <div class="w-full max-w-6xl h-[85vh] bg-gray-900 rounded-t-lg overflow-hidden shadow-2xl relative flex flex-col mt-4">
        <!-- iframe for native PDF viewer -->
        <iframe id="pdf-iframe" src="" class="w-full flex-1 border-none" style="background-color: #323639;"></iframe>
    </div>
    
    <!-- Footer / Action Bar -->
    <div class="w-full max-w-6xl bg-white p-3 flex justify-end gap-2 shadow-2xl rounded-b-lg border-t border-gray-200">
        <button type="button" id="pdf-baca-btn" onclick="triggerFlipbook(this)" data-url="" class="bg-[#eab308] hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded transition shadow flex items-center gap-2">
            <i class="fas fa-book-reader"></i> Baca
        </button>
        <button onclick="closePdfViewer()" class="bg-brand-blue hover:bg-brand-blue-hover text-white font-bold py-2 px-6 rounded transition shadow">
            Close
        </button>
    </div>
</div>

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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        var table = $('#dokumenTable').DataTable({
            "language": {
                "lengthMenu": "_MENU_ entries per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "search": "Search:",
                "paginate": {
                    "previous": "Previous",
                    "next": "Next"
                }
            },
            "pageLength": 10,
            "dom": '<"flex justify-between items-center mb-4"l><"hidden"f>rt<"flex justify-between items-center mt-4 text-sm text-gray-600"ip>',
            "ordering": true,
        });

        // Custom Search Box binding
        $('#custom-search').on('keyup', function() {
            table.search(this.value).draw();
        });
        
        $('#custom-search-btn').on('click', function() {
            table.search($('#custom-search').val()).draw();
        });

        // Year Picker Logic
        const ypBtn = document.getElementById('year-picker-btn');
        const ypContainer = document.getElementById('year-picker-container');
        const ypDropdown = document.getElementById('year-picker-dropdown');
        const ypGrid = document.getElementById('yp-grid');
        const ypRangeLabel = document.getElementById('yp-range-label');
        
        let availableYears = [];
        try {
            availableYears = JSON.parse(ypContainer.getAttribute('data-years') || '[]');
        } catch(e) {
            console.error('Failed to parse available years', e);
        }
        
        const selectedYear = "{{ request('year') }}";
        
        let currentDecadeStart = 2020;
        if (selectedYear) {
            currentDecadeStart = Math.floor(parseInt(selectedYear) / 10) * 10;
        } else if (availableYears.length > 0) {
            currentDecadeStart = Math.floor(Math.max(...availableYears) / 10) * 10;
        } else {
            currentDecadeStart = Math.floor(new Date().getFullYear() / 10) * 10;
        }

        function renderYearGrid() {
            ypRangeLabel.innerText = currentDecadeStart + " - " + (currentDecadeStart + 9);
            ypGrid.innerHTML = '';
            
            const start = currentDecadeStart - 1;
            const end = currentDecadeStart + 10;
            
            for (let y = start; y <= end; y++) {
                const isAvailable = availableYears.includes(y);
                const isSelected = y == selectedYear;
                const isOutOfRange = (y < currentDecadeStart || y > currentDecadeStart + 9);
                
                const btnEl = document.createElement('button');
                btnEl.type = 'button';
                btnEl.innerText = y;
                btnEl.className = 'py-2 rounded transition font-medium text-center ';
                
                if (isSelected) {
                    btnEl.className += 'bg-brand-blue text-white shadow-md ';
                } else if (isAvailable) {
                    btnEl.className += 'bg-gray-50 text-gray-700 hover:bg-brand-blue-light hover:text-brand-blue-hover border border-gray-100 ';
                    if (isOutOfRange) btnEl.className += 'opacity-60 ';
                } else {
                    btnEl.className += 'text-gray-300 cursor-not-allowed bg-transparent ';
                }
                
                if (isAvailable) {
                    btnEl.onclick = function() {
                        applyYearFilter(y);
                    };
                }
                
                ypGrid.appendChild(btnEl);
            }
        }
        
        function applyYearFilter(year) {
            const url = new URL(window.location.href);
            if (year) {
                url.searchParams.set('year', year);
            } else {
                url.searchParams.delete('year');
            }
            window.location.href = url.toString();
        }
        
        ypBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (ypDropdown.classList.contains('hidden')) {
                ypDropdown.classList.remove('hidden');
                setTimeout(() => ypDropdown.classList.remove('opacity-0'), 10);
                renderYearGrid();
            } else {
                ypDropdown.classList.add('opacity-0');
                setTimeout(() => ypDropdown.classList.add('hidden'), 200);
            }
        });
        
        document.getElementById('yp-prev').addEventListener('click', function(e) {
            e.stopPropagation();
            currentDecadeStart -= 10;
            renderYearGrid();
        });
        
        document.getElementById('yp-next').addEventListener('click', function(e) {
            e.stopPropagation();
            currentDecadeStart += 10;
            renderYearGrid();
        });
        
        document.getElementById('yp-all').addEventListener('click', function(e) {
            e.stopPropagation();
            applyYearFilter('');
        });
        
        document.addEventListener('click', function(e) {
            if (!ypDropdown.contains(e.target) && !ypBtn.contains(e.target) && !ypDropdown.classList.contains('hidden')) {
                ypDropdown.classList.add('opacity-0');
                setTimeout(() => ypDropdown.classList.add('hidden'), 200);
            }
        });
    });

    // PDF Viewer Logic (Langsung bypass ke dFlip)
    function openPdfViewer(url) {
        document.getElementById('pdf-iframe').src = url;
        document.getElementById('pdf-baca-btn').setAttribute('data-url', url);
        document.getElementById('pdf-modal').classList.remove('hidden');
        document.getElementById('pdf-modal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closePdfViewer() {
        document.getElementById('pdf-iframe').src = '';
        document.getElementById('pdf-modal').classList.add('hidden');
        document.getElementById('pdf-modal').classList.remove('flex');
        document.body.style.overflow = '';
    }

    function triggerFlipbook(btn) {
        var url = btn.getAttribute('data-url');
        if (!url) return;
        
        // Tutup modal iframe bawaan browser jika sedang terbuka
        closePdfViewer();
        
        // Inject _df_button untuk menginisialisasi lightbox
        $('.dflip-dynamic-btn').remove();
        var dfBtn = $('<div class="_df_button dflip-dynamic-btn" source="' + url + '" style="display:none;"></div>');
        $('body').append(dfBtn);
        
        // Eksekusi klik untuk memicu Lightbox dFlip
        setTimeout(function() {
            dfBtn.trigger('click');
        }, 100);
    }
</script>
@endpush
@endsection
