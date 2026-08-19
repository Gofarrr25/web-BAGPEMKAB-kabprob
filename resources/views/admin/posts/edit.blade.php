@extends('layouts.admin')

@section('title', 'Edit Berita - Admin Panel')
@section('page_title', 'Edit Berita & Artikel')

@push('styles')
<style>
    .ck-editor__editable_inline {
        min-height: 400px;
        font-size: 15px;
        font-family: 'Nunito', sans-serif;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
@endpush

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 max-w-5xl">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Form Edit Berita</h3>
            <a href="{{ route('admin.posts.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-semibold transition flex items-center justify-center gap-1 bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left"></i> Kembali</a>
        <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">Mode Edit</span>
    </div>
    
    <div class="p-6">
        <form action="{{ route('admin.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data" id="editPostForm">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Kolom Kiri: Judul & Konten -->
                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Judul Berita <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required value="{{ old('title', $post->title) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-base font-bold text-gray-800">
                        @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Isi Konten Berita <span class="text-red-500">*</span></label>
                        
                        <!-- CKEditor 5 Textarea -->
                        <textarea name="content" id="ck-editor" class="w-full border border-gray-300 rounded-lg">{{ old('content', $post->content) }}</textarea>

                        @error('content') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Kolom Kanan: Pengaturan -->
                <div class="space-y-6">
                    <!-- Kategori -->
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white text-sm font-semibold">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $post->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tanggal Publikasi -->
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Publikasi</label>
                        <input type="datetime-local" name="created_at" value="{{ old('created_at', $post->created_at->format('Y-m-d\TH:i')) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                        <p class="text-xs text-gray-500 mt-1">Sesuaikan jika Anda ingin mengubah tanggal berita ini.</p>
                        @error('created_at') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Thumbnail -->
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Gambar Utama</label>
                        @if($post->image)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $post->image) }}" class="w-full h-32 object-cover rounded-lg border border-gray-200" alt="Current Image">
                                <p class="text-xs text-gray-500 mt-1 font-semibold">Gambar aktif</p>
                            </div>
                        @endif
                        <input type="file" name="image" id="imageInput" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                        <p class="text-xs text-gray-400 mt-2">
                            Biarkan kosong jika tidak ingin mengubah gambar.<br>
                            <strong>Rekomendasi ukuran: 800 x 533 px (Rasio 3:2) atau 800 x 450 px (Rasio 16:9). Tampilan depan akan memotong (crop) gambar agar seragam di dalam kartu berita.</strong>
                        </p>
                        <input type="hidden" name="cropped_image" id="croppedInput">
                        
                        <div id="imagePreviewArea" class="mt-4 hidden border border-gray-200 rounded-lg overflow-hidden bg-gray-50 relative aspect-[16/9]">
                            <img id="previewImage" src="" alt="Preview" class="w-full h-full object-cover">
                            <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 hover:opacity-100 transition-opacity">
                                <span class="text-white text-xs font-bold px-2 py-1 bg-black/70 rounded">Hasil Potongan Baru</span>
                            </div>
                        </div>
                        @error('image') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status -->
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Status Publikasi</label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_published" value="1" {{ $post->is_published ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm font-bold text-gray-700">Tampilkan ke Publik</span>
                        </label>
                    </div>

                    <!-- Tombol Simpan -->
                    <div class="pt-4 flex flex-col gap-3">
                        <button type="submit" class="w-full py-3 bg-yellow-500 text-white font-bold rounded-lg hover:bg-yellow-600 transition shadow-md text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> Perbarui Berita
                        </button>
                        
                    </div>
                </div>
            </div>
            
        </form>
    </div>
</div>

<!-- Modal Cropper -->
<div id="cropModal" class="fixed inset-0 bg-black/80 z-[9999] hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl flex flex-col overflow-hidden max-h-[90vh]">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800"><i class="fas fa-crop text-blue-600 mr-2"></i> Sesuaikan Ukuran Gambar (Rasio 16:9)</h3>
            <button type="button" id="closeCropModal" class="text-gray-400 hover:text-red-500 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-4 bg-gray-900 flex-1 overflow-hidden relative min-h-[50vh]">
            <div class="w-full h-full flex items-center justify-center">
                <img id="cropImage" src="" alt="Picture to crop" class="max-w-full max-h-full">
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-wrap justify-between items-center gap-4">
            <div class="text-xs text-gray-500">
                <i class="fas fa-info-circle mr-1"></i> Geser, zoom, dan sesuaikan kotak crop. Rasio sudah dikunci 16:9.
            </div>
            <div class="flex gap-2">
                <button type="button" id="btnResetCrop" class="px-4 py-2 bg-gray-200 text-gray-700 font-bold rounded hover:bg-gray-300 transition text-sm">
                    <i class="fas fa-sync-alt mr-1"></i> Reset
                </button>
                <button type="button" id="btnSaveCrop" class="px-5 py-2 bg-blue-600 text-white font-bold rounded hover:bg-blue-700 transition shadow text-sm">
                    <i class="fas fa-check mr-1"></i> Terapkan & Simpan
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@include('admin.partials.ckeditor-script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        createCkEditor('#ck-editor', 'Tuliskan atau sunting berita di sini...');
    });

    let cropper;
    const imageInput = document.getElementById('imageInput');
    const cropModal = document.getElementById('cropModal');
    const cropImage = document.getElementById('cropImage');
    const croppedInput = document.getElementById('croppedInput');
    const imagePreviewArea = document.getElementById('imagePreviewArea');
    const previewImage = document.getElementById('previewImage');
    const closeCropModal = document.getElementById('closeCropModal');
    const btnSaveCrop = document.getElementById('btnSaveCrop');
    const btnResetCrop = document.getElementById('btnResetCrop');

    if(imageInput) {
        imageInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const file = files[0];
                const reader = new FileReader();
                reader.onload = function(event) {
                    cropImage.src = event.target.result;
                    cropModal.classList.remove('hidden');
                    cropModal.classList.add('flex');
                    
                    if (cropper) {
                        cropper.destroy();
                    }
                    
                    cropper = new Cropper(cropImage, {
                        aspectRatio: 16 / 9,
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 1,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                    });
                };
                reader.readAsDataURL(file);
            }
        });

        closeCropModal.addEventListener('click', function() {
            cropModal.classList.add('hidden');
            cropModal.classList.remove('flex');
            if (!croppedInput.value) {
                imageInput.value = '';
            }
        });

        btnResetCrop.addEventListener('click', function() {
            if (cropper) cropper.reset();
        });

        btnSaveCrop.addEventListener('click', function() {
            if (!cropper) return;
            
            const canvas = cropper.getCroppedCanvas({
                width: 800,
                height: 450,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
                fillColor: '#fff'
            });
            
            const croppedData = canvas.toDataURL('image/jpeg', 0.9);
            croppedInput.value = croppedData;
            
            previewImage.src = croppedData;
            imagePreviewArea.classList.remove('hidden');
            
            // Clear file input to prevent uploading both
            imageInput.value = '';
            
            cropModal.classList.add('hidden');
            cropModal.classList.remove('flex');
        });
    }
</script>
@endpush
@endsection
