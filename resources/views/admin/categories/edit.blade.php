@extends('layouts.admin')

@section('title', 'Edit Kategori - Admin Panel')
@section('page_title', 'Edit Kategori Berita')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800"><i class="fas fa-edit text-yellow-500 mr-2"></i> Edit Kategori</h3>
            <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-semibold transition flex items-center justify-center gap-1 bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
        
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="p-4 md:p-6">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="name" required value="{{ old('name', $category->name) }}" placeholder="Misal: Pemerintahan"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-brand-blue outline-none transition">
                <p class="text-xs text-gray-500 mt-1">Mengubah nama kategori tidak akan memutus tautannya dengan berita yang sudah ada.</p>
                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="border-t border-gray-100 pt-5 mt-2">
                <button type="submit" class="w-full py-2 bg-yellow-500 text-white font-bold rounded hover:bg-yellow-600 transition shadow-md">
                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
