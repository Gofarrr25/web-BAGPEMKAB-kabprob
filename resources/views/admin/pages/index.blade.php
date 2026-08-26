@extends('layouts.admin')

@section('title', 'Manajemen Halaman Statis & Structuring Menu - Admin Panel')
@section('page_title', 'Manajemen Halaman Statis & Menu Navigasi')

@section('content')
<div class="space-y-6">

    <!-- Main Container Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">
        
        <!-- Header Controls & Search Bar -->
        <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <!-- Quick Search Input -->
            <div class="relative w-full md:w-80">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" id="searchInput" placeholder="Cari halaman atau URL..." onkeyup="filterPagesTable()"
                       class="w-full pl-9 pr-4 py-2 bg-white border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 focus:ring-2 focus:ring-blue-500 outline-none transition">
            </div>

            @role('Superadmin')
            <div class="flex-shrink-0">
                <a href="{{ route('admin.menus.index') }}" class="px-4 py-2 bg-yellow-400 text-gray-900 font-bold rounded-xl text-xs hover:bg-yellow-300 transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-network-wired"></i> Kelola di Menu Navigasi
                </a>
            </div>
            @endrole
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto w-full">
        <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-gray-100/70 border-b border-gray-200 text-[11px] font-extrabold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        <th class="px-4 py-3.5 w-10 text-center align-middle">No</th>
                        <th class="px-4 py-3.5 align-middle">Judul Halaman</th>
                        <th class="px-4 py-3.5 align-middle">Kelompok Menu</th>
                        <th class="px-4 py-3.5 align-middle">Submenu Dari</th>
                        <th class="px-4 py-3.5 align-middle">Dokumen & Lampiran</th>
                        <th class="px-4 py-3.5 align-middle">Tautan URL Publik</th>
                        <th class="px-4 py-3.5 text-center align-middle">Navigasi</th>
                        <th class="px-4 py-3.5 text-center align-middle whitespace-nowrap min-w-[200px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-gray-100" id="pagesTableBody">
                    @forelse($mainMenus as $mainMenu)
                        <!-- Group Header -->
                        <tr class="bg-indigo-50/80 border-y border-indigo-100 group-header">
                            <td colspan="8" class="px-5 py-3 font-extrabold text-indigo-900 uppercase tracking-wider text-xs">
                                <i class="{{ $mainMenu->icon ?? 'fas fa-folder' }} text-indigo-500 mr-2 text-sm"></i> KELOMPOK MENU UTAMA: {{ $mainMenu->title }}
                            </td>
                        </tr>

                        <!-- Main Menu's Own Page (if any) -->
                        @if($mainMenu->page)
                            @include('admin.pages.partials.row', ['page' => $mainMenu->page, 'menu' => $mainMenu, 'isSubmenu' => false])
                        @endif

                        <!-- Submenus -->
                        @foreach($mainMenu->children as $child)
                            @if($child->page)
                                @include('admin.pages.partials.row', ['page' => $child->page, 'menu' => $child, 'isSubmenu' => true, 'parentTitle' => $mainMenu->title])
                            @else
                                <!-- Submenu without page -->
                                @include('admin.pages.partials.empty_row', ['menu' => $child, 'parentTitle' => $mainMenu->title])
                            @endif
                        @endforeach
                    @empty
                        <tr class="searchable-row" data-search="">
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i>
                                <p class="font-bold text-gray-600">Belum ada struktur menu utama.</p>
                                <p class="text-xs text-gray-400 mt-1">Gunakan <strong>Menu Navigasi</strong> untuk membuat menu.</p>
                            </td>
                        </tr>
                    @endforelse


                </tbody>
            </table>
        </div>

        <!-- Footer Info -->
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500 font-semibold">
            <span id="resultCountText">Memuat data...</span>
            <span class="text-gray-400">BAGPEMKAB Probolinggo CMS</span>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function filterPagesTable() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.searchable-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.getAttribute('data-search') || '';
            if (text.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('resultCountText').innerText = 'Menampilkan ' + visibleCount + ' Halaman / Submenu';
    }

    document.addEventListener('DOMContentLoaded', function() {
        filterPagesTable();
    });
</script>
@endpush



