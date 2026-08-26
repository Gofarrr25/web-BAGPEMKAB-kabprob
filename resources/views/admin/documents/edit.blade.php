@extends('layouts.admin')

@section('title', 'Edit Dokumen PPID / Zip - Admin Panel')
@section('page_title', 'Edit Dokumen / Berkas Zip')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 max-w-3xl">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Form Edit Dokumen & Arsip Berkas (.PDF / .ZIP)</h3>
            <a href="{{ route('admin.documents.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-semibold transition flex items-center justify-center gap-1 bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left"></i> Kembali</a>
        <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-bold">Maks. 50MB</span>
    </div>
    
    <div class="p-6">
        <form action="{{ route('admin.documents.update', $document->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-2">Judul Dokumen / Nama Paket Zip <span class="text-red-500">*</span></label>
                <input type="text" name="title" required value="{{ old('title', $document->title) }}" placeholder="Misal: LAKIP Tahun 2025 ATAU Paket Dokumen Lampiran (ZIP)"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none transition">
                @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-2">Klasifikasi Dokumen / Submenu <span class="text-red-500">*</span></label>
                <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none bg-white font-semibold">
                    <option value="">-- Pilih Klasifikasi --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->title }}" {{ old('category', $document->category) == $cat->title ? 'selected' : '' }}>
                            {{ $cat->title }}
                        </option>
                    @endforeach
                </select>
                @error('category') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Dokumen</label>
                <input type="date" name="document_date" value="{{ old('document_date', $document->document_date ? \Carbon\Carbon::parse($document->document_date)->format('Y-m-d') : date('Y-m-d')) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none transition">
                @error('document_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 text-center">
                    <div class="flex justify-center gap-3 text-3xl mb-3">
                        <i class="fas fa-file-pdf text-red-500"></i>
                    </div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">File Dokumen (.PDF)</label>
                    @if($document->file_path)
                        <div class="mb-3">
                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="text-blue-600 hover:underline text-xs font-semibold"><i class="fas fa-external-link-alt"></i> Lihat File Saat Ini</a>
                        </div>
                    @endif
                    <input type="file" name="file" accept="application/pdf" class="w-full max-w-sm mx-auto block text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">
                    <p class="text-xs text-gray-500 mt-2 font-medium">Opsional. Biarkan kosong jika tidak ingin mengganti file.</p>
                    @error('file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 text-center">
                    <div class="flex justify-center gap-3 text-3xl mb-3">
                        <i class="fas fa-file-archive text-amber-500"></i>
                    </div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Arsip Lampiran (.ZIP / .RAR)</label>
                    @if($document->zip_path)
                        <div class="mb-3">
                            <a href="{{ asset('storage/' . $document->zip_path) }}" target="_blank" class="text-blue-600 hover:underline text-xs font-semibold"><i class="fas fa-external-link-alt"></i> Lihat File Saat Ini</a>
                        </div>
                    @endif
                    <input type="file" name="zip_file" accept="application/zip,.zip" class="w-full max-w-sm mx-auto block text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 cursor-pointer">
                    <p class="text-xs text-gray-500 mt-2 font-medium">Opsional. Biarkan kosong jika tidak ingin mengganti file.</p>
                    @error('zip_file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="border-t border-gray-100 pt-6 flex gap-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-bold rounded hover:bg-blue-700 transition shadow-md">
                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
