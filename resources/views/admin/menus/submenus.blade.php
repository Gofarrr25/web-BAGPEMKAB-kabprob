@extends('layouts.admin')

@section('title', 'Kelola Submenu ' . $menu->title . ' - Admin Panel')
@section('page_title', 'Kelola Submenu Navigasi: ' . $menu->title)

@section('content')
<div class="space-y-6">

    <!-- Breadcrumb & Header -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-4">
        <div class="flex items-center gap-2 text-xs font-bold text-gray-500">
            <a href="{{ route('admin.menus.index') }}" class="hover:text-blue-600">Menu Utama Navigasi</a>
            <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
            <span class="text-blue-600 font-extrabold uppercase">{{ $menu->title }}</span>
            <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
            <span class="text-gray-700">Daftar Submenu</span>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-t pt-4">
            <div>
                <h3 class="font-extrabold text-gray-900 text-lg uppercase tracking-tight flex items-center gap-2">
                    <i class="{{ $menu->icon ?? 'fas fa-sitemap' }} text-blue-600"></i> Kelola Submenu: <span class="text-blue-700">{{ $menu->title }}</span>
                </h3>
                <p class="text-xs text-gray-500 mt-1">Gunakan tombol <strong>Kelola Konten</strong> untuk mengedit teks dokumen, gambar, PDF, dan YouTube di WYSIWYG Editor SuperBuild.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.menus.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i> Kembali ke Menu Utama
                </a>
                <button onclick="openCreateSubmenuModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-md transition flex items-center gap-2">
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
                        <th class="px-6 py-4 text-center w-16">Urutan</th>
                        <th class="px-6 py-4">Nama Submenu</th>
                        <th class="px-6 py-4">Halaman / Link Tujuan</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi Submenu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm font-semibold text-gray-800">
                    @forelse($submenus as $sub)
                    <tr class="hover:bg-blue-50/30 transition">
                        <!-- Urutan -->
                        <td class="px-6 py-4 text-center">
                            <span class="bg-gray-100 text-gray-700 px-2.5 py-1 rounded font-mono text-xs font-bold">
                                {{ $sub->order_index }}
                            </span>
                        </td>

                        <!-- Nama Submenu -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2.5">
                                <i class="{{ $sub->icon ?? 'fas fa-angle-right' }} text-blue-500"></i>
                                <div>
                                    <span class="font-bold text-gray-900">{{ $sub->title }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Halaman / Link Tujuan -->
                        <td class="px-6 py-4 text-xs">
                            @if($sub->page)
                                <span class="text-blue-600 font-bold flex items-center gap-1">
                                    <i class="fas fa-file-alt text-blue-500"></i> {{ $sub->page->title }} (/page/{{ $sub->page->slug }})
                                </span>
                            @elseif($sub->url)
                                <span class="text-gray-600 font-mono truncate max-w-xs block"><i class="fas fa-link text-indigo-500"></i> {{ $sub->url }}</span>
                            @else
                                <span class="text-gray-400 italic">Belum terhubung</span>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.menus.toggle', $sub->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin {{ $sub->is_active ? 'menonaktifkan' : 'mengaktifkan' }} Submenu ini?');">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded-full text-xs font-bold border transition {{ $sub->is_active ? 'bg-green-100 text-green-800 border-green-200 hover:bg-green-200' : 'bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200' }}">
                                    <i class="fas fa-{{ $sub->is_active ? 'check-circle' : 'minus-circle' }} mr-1"></i>
                                    {{ $sub->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>

                        <!-- Aksi Submenu -->
                        <td class="px-6 py-4 text-right space-x-1.5">
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
                                    class="px-2.5 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-bold border border-blue-200 transition" title="Edit Submenu">
                                <i class="fas fa-edit"></i> Edit
                            </button>



                            <!-- Hapus Button -->
                            <form action="{{ route('admin.menus.destroy', $sub->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Submenu \'{{ $sub->title }}\'?');">
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
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <i class="fas fa-plus-circle text-blue-600"></i> Tambah Submenu Untuk "{{ $menu->title }}"
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

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Tipe Konten / Modul <span class="text-red-500">*</span></label>
                <select name="module_type" id="create_module_type" required class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-semibold bg-white" onchange="toggleUrlField('create')">
                    <option value="page">Halaman Statis (Otomatis Dibuat)</option>
                    <option value="posts">Modul Berita & Artikel</option>
                    <option value="documents">Modul Dokumen PPID</option>

                    <option value="galleries">Modul Galeri & Video</option>
                    <option value="members">Modul Struktur Organisasi</option>
                    <option value="contact">Modul Kontak & Pesan</option>
                    <option value="custom">Tautan Eksternal / Kustom URL</option>
                </select>
                <p id="create_page_help" class="text-[11px] text-emerald-700 font-medium mt-1">*Halaman Statis baru akan <strong>otomatis dibuat</strong> dan terhubung langsung ke Submenu ini.</p>
            </div>

            <div id="create_url_group" class="hidden">
                <label class="block text-xs font-bold text-gray-700 mb-1">URL / Tautan Khusus <span class="text-red-500">*</span></label>
                <input type="text" name="url" id="create_url" placeholder="https://..." class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-semibold">
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Ikon (FontAwesome - Opsional)</label>
                    <input type="text" name="icon" placeholder="fas fa-file-alt" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Urutan (Angka)</label>
                <input type="number" name="order_index" value="0" min="0" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-semibold">
            </div>


            <div class="flex items-center justify-between pt-2">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-blue-600 rounded">
                    <span class="ml-2 text-xs font-bold text-gray-700">Tampilkan di Navigasi</span>
                </label>

                <div class="flex gap-2">
                    <button type="button" onclick="closeCreateSubmenuModal()" class="px-4 py-2 bg-gray-200 text-gray-700 font-bold rounded-lg text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs shadow-md">Simpan Submenu</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Submenu -->
<div id="editSubmenuModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <i class="fas fa-edit text-blue-600"></i> Edit Submenu
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

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Tipe Konten / Modul <span class="text-red-500">*</span></label>
                <select name="module_type" id="edit_sub_module_type" required class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-semibold bg-white" onchange="toggleUrlField('edit')">
                    <option value="page">Halaman Statis (Otomatis Dibuat)</option>
                    <option value="posts">Modul Berita & Artikel</option>
                    <option value="documents">Modul Dokumen PPID</option>

                    <option value="galleries">Modul Galeri & Video</option>
                    <option value="members">Modul Struktur Organisasi</option>
                    <option value="contact">Modul Kontak & Pesan</option>
                    <option value="custom">Tautan Eksternal / Kustom URL</option>
                </select>
                <p class="text-[10px] text-red-500 mt-1 italic">*Jika Anda mengubah dari Halaman Statis ke Modul lain, Halaman Statis yang lama akan tetap ada di Daftar Halaman secara Standalone.</p>
            </div>

            <div id="edit_url_group" class="hidden">
                <label class="block text-xs font-bold text-gray-700 mb-1">URL / Tautan Khusus <span class="text-red-500">*</span></label>
                <input type="text" name="url" id="edit_sub_url" placeholder="https://..." class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-semibold">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Menu Induk / Menu Utama</label>
                <select name="parent_id" id="edit_sub_parent_id" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-semibold bg-white uppercase">
                    @foreach($parentMenus as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Ikon (FontAwesome)</label>
                    <input type="text" name="icon" id="edit_sub_icon" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Urutan (Angka)</label>
                <input type="number" name="order_index" id="edit_sub_order_index" min="0" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-semibold">
            </div>


            <div class="flex items-center justify-between pt-2">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" id="edit_sub_is_active" value="1" class="w-4 h-4 text-blue-600 rounded">
                    <span class="ml-2 text-xs font-bold text-gray-700">Tampilkan di Navigasi</span>
                </label>

                <div class="flex gap-2">
                    <button type="button" onclick="closeEditSubmenuModal()" class="px-4 py-2 bg-gray-200 text-gray-700 font-bold rounded-lg text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs shadow-md">Perbarui Submenu</button>
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

    function toggleUrlField(prefix) {
        const type = document.getElementById(prefix === 'create' ? 'create_module_type' : 'edit_sub_module_type').value;
        const urlGroup = document.getElementById(prefix === 'create' ? 'create_url_group' : 'edit_url_group');
        const urlInput = document.getElementById(prefix === 'create' ? 'create_url' : 'edit_sub_url');
        const helpText = document.getElementById('create_page_help');
        
        if (type === 'custom') {
            urlGroup.classList.remove('hidden');
            urlInput.setAttribute('required', 'required');
        } else {
            urlGroup.classList.add('hidden');
            urlInput.removeAttribute('required');
        }

        if (prefix === 'create' && helpText) {
            helpText.style.display = type === 'page' ? 'block' : 'none';
        }
    }

    function editSubmenuBtn(btn) {
        const id = btn.getAttribute('data-id');
        document.getElementById('editSubmenuForm').action = '/admin/menus/' + id;
        document.getElementById('edit_sub_title').value = btn.getAttribute('data-title') || '';
        document.getElementById('edit_sub_icon').value = btn.getAttribute('data-icon') || '';
        document.getElementById('edit_sub_order_index').value = btn.getAttribute('data-order') || 0;
        document.getElementById('edit_sub_parent_id').value = btn.getAttribute('data-parent') || '';
        document.getElementById('edit_sub_module_type').value = btn.getAttribute('data-module') || 'page';
        document.getElementById('edit_sub_url').value = btn.getAttribute('data-url') || '';
        document.getElementById('edit_sub_is_active').checked = btn.getAttribute('data-active') === '1';

        toggleUrlField('edit');

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



