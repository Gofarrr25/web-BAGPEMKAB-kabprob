@extends('layouts.admin')

@section('title', 'Kategori Berita - Admin Panel')
@section('page_title', 'Manajemen Kategori')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    
    <!-- Kolom Kiri: Form Tambah Kategori -->
    <div class="md:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-24">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Tambah Kategori Baru</h3>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Misal: Pemerintahan"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none transition">
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="w-full py-2 bg-blue-600 text-white font-bold rounded hover:bg-blue-700 transition shadow-md">
                    <i class="fas fa-save mr-1"></i> Simpan Kategori
                </button>
            </form>
        </div>
    </div>

    <!-- Kolom Kanan: Tabel Kategori -->
    <div class="md:col-span-2">
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
                            <th class="px-6 py-3 font-semibold text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($categories as $index => $cat)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                            <td class="px-6 py-3 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-3 font-bold text-gray-800 whitespace-nowrap">{{ $cat->name }}</td>
                            <td class="px-6 py-3 text-gray-500 whitespace-nowrap"><code>{{ $cat->slug }}</code></td>
                            <td class="px-6 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.categories.edit', $cat->id) }}" class="text-yellow-500 hover:text-yellow-700 font-bold px-2 py-1 rounded border border-yellow-200 hover:bg-yellow-50 transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-bold px-2 py-1 rounded border border-red-200 hover:bg-red-50 transition" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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
</div>
@endsection
