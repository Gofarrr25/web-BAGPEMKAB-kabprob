@extends('layouts.admin')

@section('title', 'Manajemen Menu Utama - Admin Panel')
@section('page_title', 'Manajemen Menu Utama Navigasi Website')

@section('content')
<div class="space-y-6">

    <!-- Header Stats & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                <i class="fas fa-sitemap text-blue-600"></i> Kelola Menu Utama Website
            </h3>
            <p class="text-xs text-gray-500 mt-1">Daftar kelompok Menu Utama tingkat teratas. Klik <strong>Kelola Submenu</strong> untuk menata turunan anak menu.</p>
        </div>
        <div class="flex items-center gap-3">
            @if(request('trashed') == '1')
                <a href="{{ route('admin.menus.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i> Kembali ke Menu Aktif
                </a>
            @else
                <a href="{{ route('admin.menus.index', ['trashed' => '1']) }}" class="px-3.5 py-2 bg-amber-50 text-amber-700 hover:bg-amber-100 rounded-lg text-xs font-bold transition flex items-center gap-1.5 border border-amber-200">
                    <i class="fas fa-trash-alt"></i> Tempat Sampah
                </a>
                <button onclick="openCreateModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-md transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Tambah Menu Utama Baru
                </button>
            @endif
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-wrap items-center justify-between gap-4 text-xs">
        <form action="{{ route('admin.menus.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            @if(request('trashed'))
                <input type="hidden" name="trashed" value="{{ request('trashed') }}">
            @endif

            <select name="position" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg outline-none font-semibold text-gray-700">
                <option value="">-- Semua Posisi (Navbar & Footer) --</option>
                <option value="navbar" {{ request('position') == 'navbar' ? 'selected' : '' }}>Posisi Navbar (Header Utama)</option>
                <option value="footer" {{ request('position') == 'footer' ? 'selected' : '' }}>Posisi Footer (Bawah)</option>
            </select>

            <select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg outline-none font-semibold text-gray-700">
                <option value="">-- Semua Status --</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif Sahaja</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama menu utama..." class="px-3 py-2 border border-gray-300 rounded-lg outline-none w-48 font-semibold">
            <button type="submit" class="px-3 py-2 bg-gray-800 text-white rounded-lg font-bold">Cari</button>
        </form>

        <span class="text-gray-500 font-bold">Total: {{ $menus->total() }} Menu Utama</span>
    </div>

    <!-- Main Menu Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto w-full">
        <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-600 uppercase tracking-wider whitespace-nowrap">
                        <th class="px-6 py-4 text-center w-16">Urutan</th>
                        <th class="px-6 py-4">Menu Utama & Ikon</th>
                        <th class="px-6 py-4 text-center">Posisi</th>
                        <th class="px-6 py-4 text-center">Jumlah Submenu</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi Management</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm font-semibold text-gray-800">
                    @forelse($menus as $menu)
                    <tr class="hover:bg-blue-50/30 transition">
                        <!-- Urutan -->
                        <td class="px-6 py-4 text-center">
                            <span class="bg-gray-100 text-gray-700 px-2.5 py-1 rounded font-mono text-xs font-bold">
                                {{ $menu->order_index }}
                            </span>
                        </td>

                        <!-- Menu Utama & Ikon -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 border border-blue-200 text-blue-600 flex items-center justify-center font-bold text-base shadow-sm">
                                    <i class="{{ $menu->icon ?? 'fas fa-bars' }}"></i>
                                </div>
                                <div>
                                    <span class="font-extrabold text-gray-900 uppercase tracking-wide">{{ $menu->title }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Posisi -->
                        <td class="px-6 py-4 text-center">
                            @if($menu->position == 'footer')
                                <span class="bg-purple-100 text-purple-800 px-2.5 py-1 rounded-full text-xs font-bold">Footer Bawah</span>
                            @else
                                <span class="bg-blue-100 text-blue-800 px-2.5 py-1 rounded-full text-xs font-bold">Navbar Atas</span>
                            @endif
                        </td>


                        <!-- Jumlah Submenu -->
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.menus.submenus', $menu->id) }}" class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 px-3 py-1 rounded-lg text-xs font-bold border border-blue-200 transition">
                                <i class="fas fa-sitemap text-blue-500"></i> {{ $menu->children_count }} Submenu
                            </a>
                        </td>

                        <!-- Status Aktif / Nonaktif -->
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.menus.toggle', $menu->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin {{ $menu->is_active ? 'menonaktifkan' : 'mengaktifkan' }} Menu Utama ini?');">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded-full text-xs font-bold border transition {{ $menu->is_active ? 'bg-green-100 text-green-800 border-green-200 hover:bg-green-200' : 'bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200' }}">
                                    <i class="fas fa-{{ $menu->is_active ? 'check-circle' : 'minus-circle' }} mr-1"></i>
                                    {{ $menu->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>

                        <!-- Aksi Management -->
                        <td class="px-6 py-4 text-right space-x-1.5">
                            @if($menu->trashed())
                                <form action="{{ route('admin.menus.restore', $menu->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition">
                                        <i class="fas fa-undo mr-1"></i> Restore
                                    </button>
                                </form>
                            @else
                                <!-- Edit Button -->
                                <button type="button"
                                        data-id="{{ $menu->id }}"
                                        data-title="{{ $menu->title }}"
                                        data-icon="{{ $menu->icon }}"
                                        data-position="{{ $menu->position }}"
                                        data-order="{{ $menu->order_index }}"
                                        data-active="{{ $menu->is_active ? '1' : '0' }}"
                                        onclick="editMenuBtn(this)"
                                        class="px-2.5 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-bold border border-blue-200 transition" title="Edit Menu Utama">
                                    <i class="fas fa-edit"></i> Edit
                                </button>

                                <!-- Kelola Submenu Button -->
                                <a href="{{ route('admin.menus.submenus', $menu->id) }}" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-bold shadow-sm transition inline-flex items-center gap-1" title="Kelola Submenu">
                                    <i class="fas fa-list-ul"></i> Kelola Submenu
                                </a>

                                <!-- Hapus Button -->
                                <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Menu Utama \'{{ $menu->title }}\'? Seluruh submenunya akan ikut terhapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-bold border border-red-200 transition" title="Hapus Menu Utama">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-folder-open text-4xl mb-2 block"></i>
                            Belum ada Menu Utama yang terdaftar. Silakan klik <strong>Tambah Menu Utama Baru</strong>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($menus->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $menus->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Create Menu Utama -->
<div id="createModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <i class="fas fa-plus-circle text-blue-600"></i> Tambah Menu Utama Baru
            </h3>
            <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
        </div>

        <form action="{{ route('admin.menus.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Menu Utama <span class="text-red-500">*</span></label>
                <input type="text" name="title" required placeholder="Contoh: PROFIL / LAYANAN / INFORMASI" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-semibold uppercase">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Ikon (FontAwesome)</label>
                    <input type="text" name="icon" placeholder="fas fa-building" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Posisi Menu</label>
                    <select name="position" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-semibold bg-white">
                        <option value="navbar">Navbar Atas (Header)</option>
                        <option value="footer">Footer Bawah</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Urutan (Angka)</label>
                    <input type="number" name="order_index" value="0" min="0" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-semibold">
                </div>
            </div>


            <div class="flex items-center justify-between pt-2">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-blue-600 rounded">
                    <span class="ml-2 text-xs font-bold text-gray-700">Tampilkan di Navigation Bar</span>
                </label>

                <div class="flex gap-2">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-gray-200 text-gray-700 font-bold rounded-lg text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs shadow-md">Simpan Menu Utama</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Menu Utama -->
<div id="editModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <i class="fas fa-edit text-blue-600"></i> Edit Menu Utama
            </h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
        </div>

        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Menu Utama <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="edit_title" required class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-semibold uppercase">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Ikon (FontAwesome)</label>
                    <input type="text" name="icon" id="edit_icon" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Posisi Menu</label>
                    <select name="position" id="edit_position" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-semibold bg-white">
                        <option value="navbar">Navbar Atas (Header)</option>
                        <option value="footer">Footer Bawah</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Urutan (Angka)</label>
                    <input type="number" name="order_index" id="edit_order_index" min="0" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg outline-none text-sm font-semibold">
                </div>
            </div>


            <div class="flex items-center justify-between pt-2">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="w-4 h-4 text-blue-600 rounded">
                    <span class="ml-2 text-xs font-bold text-gray-700">Tampilkan di Navigasi</span>
                </label>

                <div class="flex gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-200 text-gray-700 font-bold rounded-lg text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs shadow-md">Perbarui Menu Utama</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
        document.getElementById('createModal').classList.add('flex');
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.remove('flex');
        document.getElementById('createModal').classList.add('hidden');
    }

    function editMenuBtn(btn) {
        const id = btn.getAttribute('data-id');
        document.getElementById('editForm').action = '/admin/menus/' + id;
        document.getElementById('edit_title').value = btn.getAttribute('data-title') || '';
        document.getElementById('edit_icon').value = btn.getAttribute('data-icon') || '';
        document.getElementById('edit_position').value = btn.getAttribute('data-position') || 'navbar';
        document.getElementById('edit_order_index').value = btn.getAttribute('data-order') || 0;
        document.getElementById('edit_is_active').checked = btn.getAttribute('data-active') === '1';

        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').classList.add('flex');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.remove('flex');
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endpush
@endsection



