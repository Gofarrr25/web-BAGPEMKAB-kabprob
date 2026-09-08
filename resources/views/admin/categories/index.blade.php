@extends('layouts.admin')

@section('title', 'Kategori Berita - Admin Panel')
@section('page_title', 'Manajemen Kategori')

@section('content')
    <!-- Form Tambah Kategori -->
    <div class="mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Tambah Kategori Baru</h3>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Misal: Pemerintahan"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-brand-blue outline-none transition">
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="w-full md:w-auto px-6 py-2 bg-brand-blue text-white font-bold rounded hover:bg-brand-blue-hover transition shadow-md">
                    <i class="fas fa-save mr-1"></i> Simpan Kategori
                </button>
            </form>
        </div>
    </div>

    <!-- Tabel Kategori -->
    <div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-gray-800">Daftar Kategori Tersedia</h3>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse min-w-[500px]">
                    <thead>
                        <tr class="bg-white border-b border-gray-100 text-sm text-gray-500 uppercase tracking-wider whitespace-nowrap">
                            <th class="px-6 py-3 font-semibold w-16 whitespace-nowrap">No</th>
                            <th class="px-6 py-3 font-semibold whitespace-nowrap">Nama Kategori</th>
                            <th class="px-6 py-3 font-semibold whitespace-nowrap">Slug (URL)</th>
                            <th class="px-6 py-3 font-semibold whitespace-nowrap text-center">Status</th>
                            <th class="px-6 py-3 font-semibold whitespace-nowrap">Penggunaan</th>
                            <th class="px-6 py-3 font-semibold text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($categories as $index => $cat)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                            <td class="px-6 py-3 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-3 font-bold text-gray-800 whitespace-nowrap">{{ $cat->name }}</td>
                            <td class="px-6 py-3 text-gray-500 whitespace-nowrap"><code>{{ $cat->slug }}</code></td>
                            <td class="px-6 py-3 whitespace-nowrap text-center">
                                @if($cat->is_active)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full"><i class="fas fa-check-circle mr-1"></i> Aktif</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full"><i class="fas fa-times-circle mr-1"></i> Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap">
                                @if($cat->posts_count > 0)
                                    <span class="text-sm font-semibold text-brand-blue"><i class="fas fa-link mr-1"></i> Digunakan oleh {{ $cat->posts_count }} Berita/Artikel</span>
                                @else
                                    <span class="text-sm text-gray-400 italic">Belum digunakan</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.categories.toggle', $cat->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="{{ $cat->is_active ? 'text-red-500 hover:text-red-700 border-red-200 hover:bg-red-50' : 'text-green-500 hover:text-green-700 border-green-200 hover:bg-green-50' }} font-bold px-2 py-1 rounded border transition" title="{{ $cat->is_active ? 'Nonaktifkan Kategori' : 'Aktifkan Kategori' }}">
                                            <i class="fas {{ $cat->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                        </button>
                                    </form>
                                    
                                    <a href="{{ route('admin.categories.edit', $cat->id) }}" class="text-yellow-500 hover:text-yellow-700 font-bold px-2 py-1 rounded border border-yellow-200 hover:bg-yellow-50 transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if($cat->posts_count > 0)
                                        <button type="button" class="text-gray-400 font-bold px-2 py-1 rounded border border-gray-200 cursor-not-allowed bg-gray-50" title="Kategori sedang digunakan dan tidak dapat dihapus.">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="event.preventDefault(); confirmDelete(this, 'Kategori Berita', 'Data Terpilih', false);" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold px-2 py-1 rounded border border-red-200 hover:bg-red-50 transition" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @if($categories->isEmpty())
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada kategori.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
