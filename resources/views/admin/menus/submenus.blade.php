@extends('layouts.admin')

@section('title', 'Kelola Submenu ' . $menu->title . ' - Admin Panel')
@section('page_title', 'Kelola Submenu Navigasi: ' . $menu->title)

@section('content')
<div class="space-y-6">

    <!-- Breadcrumb & Header -->
    <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-100 space-y-4">
        <div class="flex items-center gap-2 text-xs font-bold text-gray-500">
            <a href="{{ route('admin.menus.index') }}" class="hover:text-brand-blue-hover">Menu Utama Navigasi</a>
            <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
            <span class="text-brand-blue font-extrabold uppercase">{{ $menu->title }}</span>
            <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
            <span class="text-gray-700">Daftar Submenu</span>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-t pt-4">
            <div>
                <h3 class="font-extrabold text-gray-900 text-lg uppercase tracking-tight flex items-center gap-2">
                    <i class="{{ $menu->icon ?? 'fas fa-sitemap' }} text-brand-blue"></i> Kelola Submenu: <span class="text-brand-blue">{{ $menu->title }}</span>
                </h3>
                <p class="text-xs text-gray-500 mt-1">Gunakan tombol <strong>Kelola Konten</strong> untuk mengedit teks dokumen, gambar, PDF, dan YouTube di WYSIWYG Editor SuperBuild.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.menus.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i> Kembali ke Menu Utama
                </a>
                <button onclick="openCreateSubmenuModal()" class="px-4 py-2 bg-brand-blue hover:bg-brand-blue-hover text-white rounded-lg text-xs font-bold shadow-md transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Tambah Submenu Baru
                </button>
            </div>
        </div>
    </div>

    <!-- Submenu Table List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h4 class="font-bold text-gray-800 text-sm">Struktur Anak Menu Dari "{{ $menu->title }}"</h4>
            <span class="text-xs font-bold text-gray-500">Total: {{ $submenus->count() }} Submenu</span>
        </div>

        <div class="overflow-x-auto w-full">
        <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-white border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        <th class="px-6 py-4 text-center w-16 whitespace-nowrap">Urutan</th>
                        <th class="px-6 py-4 whitespace-nowrap">Nama Submenu</th>
                        <th class="px-6 py-4 whitespace-nowrap">Halaman / Link Tujuan</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Status</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Aksi Submenu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm font-semibold text-gray-800">
                    @forelse($submenus as $sub)
                    <tr class="hover:bg-brand-blue-light/30 transition">
                        <!-- Urutan -->
                        <td class="px-6 py-4 text-center">
                            <span class="bg-gray-100 text-gray-700 px-2.5 py-1 rounded font-mono text-xs font-bold">
                                {{ $sub->order_index }}
                            </span>
                        </td>

                        <!-- Nama Submenu -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2.5">
                                <i class="{{ $sub->icon ?? 'fas fa-angle-right' }} text-brand-blue"></i>
                                <div>
                                    <span class="font-bold text-gray-900">{{ $sub->title }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Halaman / Link Tujuan -->
                        <td class="px-6 py-4 text-xs">
                            @if($sub->page)
                                <span class="text-brand-blue font-bold flex items-center gap-1">
                                    <i class="fas fa-file-alt text-brand-blue"></i> {{ $sub->page->title }} (/page/{{ $sub->page->slug }})
                                </span>
                            @elseif($sub->url)
                                <span class="text-gray-600 font-mono truncate max-w-xs block"><i class="fas fa-link text-indigo-500"></i> {{ $sub->url }}</span>
                            @else
                                <span class="text-gray-400 italic">Belum terhubung</span>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.menus.toggle', $sub->id) }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); confirmToggle(this);">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded-full text-xs font-bold border transition {{ $sub->is_active ? 'bg-green-100 text-green-800 border-green-200 hover:bg-green-200' : 'bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200' }}">
                                    <i class="fas fa-{{ $sub->is_active ? 'check-circle' : 'minus-circle' }} mr-1"></i>
                                    {{ $sub->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>

                        <!-- Aksi Submenu -->
                        <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                            <!-- Edit Button -->
                            <button type="button"
                                    data-id="{{ $sub->id }}"
                                    data-title="{{ $sub->title }}"
                                    data-icon="{{ $sub->icon }}"
                                    data-order="{{ $sub->order_index }}"
                                    data-parent="{{ $sub->parent_id }}"
                                    data-module="{{ $sub->module_type ?? 'page' }}"
                                    data-url="{{ $sub->url }}"
                                    data-active="{{ $sub->is_active ? '1' : '0' }}"
                                    onclick="editSubmenuBtn(this)"
                                    class="px-2.5 py-1.5 bg-brand-blue-light text-brand-blue hover:bg-brand-blue-light rounded-lg text-xs font-bold border border-brand-blue-light transition" title="Edit Submenu">
                                <i class="fas fa-edit"></i> Edit
                            </button>



                            <!-- Hapus Button -->
                            <form action="{{ route('admin.menus.destroy', $sub->id) }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); confirmDelete(this, 'Menu', 'Data Terpilih', false);">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-bold border border-red-200 transition" title="Hapus Submenu">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-sitemap text-4xl mb-2 block"></i>
                            Belum ada Submenu untuk Menu Utama <strong>"{{ $menu->title }}"</strong>.<br>
                            Silakan klik <strong>+ Tambah Submenu Baru</strong> untuk membuat anak menu.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Submenu -->
<div id="createSubmenuModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-4 md:p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <i class="fas fa-plus-circle text-brand-blue"></i> Tambah Submenu Untuk "{{ $menu->title }}"
            </h3>
            <button onclick="closeCreateSubmenuModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
        </div>

        <form action="{{ route('admin.menus.store') }}" method="POST" class="space-y-4">
            @csrf
            <!-- Parent ID Otomatis Terhubung dengan Menu Utama saat ini -->
            <input type="hidden" name="parent_id" value="{{ $menu->id }}">
            <input type="hidden" name="position" value="{{ $menu->position ?? 'navbar' }}">

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Submenu <span class="text-red-500">*</span></label>
                <input type="text" name="title" required placeholder="Contoh: Visi Misi / Tugas & Fungsi / Sejarah" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-semibold">
            </div>

            <input type="hidden" name="module_type" value="page">
            <p id="create_page_help" class="text-[11px] text-emerald-700 font-medium mt-1">*Halaman Statis baru akan <strong>otomatis dibuat</strong> dan terhubung langsung ke Submenu ini.</p>



            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Urutan (Angka)</label>
                <input type="number" name="order_index" value="0" min="0" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-semibold">
            </div>


            <div class="flex items-center justify-between pt-2">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-brand-blue rounded">
                    <span class="ml-2 text-xs font-bold text-gray-700">Tampilkan di Navigasi</span>
                </label>

                <div class="flex gap-2">
                    <button type="button" onclick="closeCreateSubmenuModal()" class="px-4 py-2 bg-gray-200 text-gray-700 font-bold rounded-lg text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-blue hover:bg-brand-blue-hover text-white font-bold rounded-lg text-xs shadow-md">Simpan Submenu</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Submenu -->
<div id="editSubmenuModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-4 md:p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <i class="fas fa-edit text-brand-blue"></i> Edit Submenu
            </h3>
            <button onclick="closeEditSubmenuModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
        </div>

        <form id="editSubmenuForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="position" value="{{ $menu->position ?? 'navbar' }}">

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Submenu <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="edit_sub_title" required class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-semibold">
            </div>

            <input type="hidden" name="module_type" value="page">

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Menu Induk / Menu Utama</label>
                <select name="parent_id" id="edit_sub_parent_id" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-semibold bg-white uppercase">
                    @foreach($parentMenus as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                    @endforeach
                </select>
            </div>



            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Urutan (Angka)</label>
                <input type="number" name="order_index" id="edit_sub_order_index" min="0" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-semibold">
            </div>


            <div class="flex items-center justify-between pt-2">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" id="edit_sub_is_active" value="1" class="w-4 h-4 text-brand-blue rounded">
                    <span class="ml-2 text-xs font-bold text-gray-700">Tampilkan di Navigasi</span>
                </label>

                <div class="flex gap-2">
                    <button type="button" onclick="closeEditSubmenuModal()" class="px-4 py-2 bg-gray-200 text-gray-700 font-bold rounded-lg text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-blue hover:bg-brand-blue-hover text-white font-bold rounded-lg text-xs shadow-md">Perbarui Submenu</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openCreateSubmenuModal() {
        document.getElementById('createSubmenuModal').classList.remove('hidden');
        document.getElementById('createSubmenuModal').classList.add('flex');
    }

    function closeCreateSubmenuModal() {
        document.getElementById('createSubmenuModal').classList.remove('flex');
        document.getElementById('createSubmenuModal').classList.add('hidden');
    }



    function editSubmenuBtn(btn) {
        const id = btn.getAttribute('data-id');
        document.getElementById('editSubmenuForm').action = '/admin/menus/' + id;
        document.getElementById('edit_sub_title').value = btn.getAttribute('data-title') || '';

        document.getElementById('edit_sub_order_index').value = btn.getAttribute('data-order') || 0;
        document.getElementById('edit_sub_parent_id').value = btn.getAttribute('data-parent') || '';


        document.getElementById('editSubmenuModal').classList.remove('hidden');
        document.getElementById('editSubmenuModal').classList.add('flex');
    }

    function closeEditSubmenuModal() {
        document.getElementById('editSubmenuModal').classList.remove('flex');
        document.getElementById('editSubmenuModal').classList.add('hidden');
    }
</script>
@endpush
@endsection



