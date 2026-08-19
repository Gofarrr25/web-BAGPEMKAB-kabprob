@extends('layouts.admin')

@section('title', 'Edit Galeri - Admin Panel')
@section('page_title', 'Edit Media Galeri')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800"><i class="fas fa-edit text-yellow-500 mr-2"></i> Edit Media</h3>
            <a href="{{ route('admin.galleries.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-semibold transition flex items-center justify-center gap-1 bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
        
        <form action="{{ route('admin.galleries.update', $gallery->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Judul Media <span class="text-red-500">*</span></label>
                <input type="text" name="title" required value="{{ old('title', $gallery->title) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none transition">
                @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Publikasi</label>
                <input type="datetime-local" name="created_at" value="{{ old('created_at', $gallery->created_at->format('Y-m-d\TH:i')) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none transition">
                <p class="text-xs text-gray-500 mt-1">Sesuaikan jika Anda ingin mengubah tanggal media ini.</p>
                @error('created_at') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Tipe Media (Read-only on edit to avoid complex state changes) -->
            <input type="hidden" name="type" value="{{ $gallery->type }}">
            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-2">Tipe Media</label>
                <input type="text" disabled value="{{ $gallery->type === 'image' ? 'Foto / Gambar' : 'Tautan Video YouTube' }}" class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-600">
            </div>

            @if($gallery->type === 'image')
                <div class="mb-5 bg-gray-50 p-4 border border-gray-200 rounded-lg">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Ganti Foto <span class="text-xs font-normal text-gray-500">(Opsional)</span></label>
                    <div class="flex gap-4 items-start">
                        <div class="w-32 h-24 shrink-0 rounded overflow-hidden border border-gray-300 bg-white">
                            <img src="{{ asset('storage/' . $gallery->file_path) }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <input type="file" name="file" id="imageInput" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                            <p class="text-xs text-gray-400 mt-2">Biarkan kosong jika tidak ingin mengubah foto. Rasio potongan otomatis adalah 4:3.</p>
                        </div>
                    </div>
                    <input type="hidden" name="cropped_image" id="croppedInput">
                    <div id="imagePreviewArea" class="mt-4 hidden border border-gray-200 rounded-lg overflow-hidden bg-white relative aspect-[4/3] w-full max-w-sm">
                        <img id="previewImage" src="" alt="Preview" class="w-full h-full object-cover">
                        <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 hover:opacity-100 transition-opacity">
                            <span class="text-white text-xs font-bold px-2 py-1 bg-black/70 rounded">Hasil Potongan</span>
                        </div>
                    </div>
                    @error('file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            @else
                <div class="mb-5 bg-gray-50 p-4 border border-gray-200 rounded-lg">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tautan Video YouTube <span class="text-red-500">*</span></label>
                    <input type="url" name="video_url" required value="{{ old('video_url', $gallery->video_url) }}" placeholder="https://www.youtube.com/watch?v=..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none transition">
                    @error('video_url') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    
                    <div class="mt-4 w-48 rounded overflow-hidden border border-gray-300">
                        <img src="{{ $gallery->thumbnail_url }}" class="w-full object-cover">
                    </div>
                </div>
            @endif

            <div class="border-t border-gray-100 pt-5 flex gap-3 mt-2">
                <button type="submit" class="w-full py-2 bg-yellow-500 text-white font-bold rounded-lg hover:bg-yellow-600 transition shadow-md">
                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@if($gallery->type === 'image')
<!-- Modal Cropper -->
<div id="cropModal" class="fixed inset-0 bg-black/80 z-[9999] hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl flex flex-col overflow-hidden max-h-[90vh]">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800"><i class="fas fa-crop text-blue-600 mr-2"></i> Sesuaikan Ukuran Gambar (Rasio 4:3)</h3>
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
                <i class="fas fa-info-circle mr-1"></i> Geser, zoom, dan sesuaikan kotak crop. Rasio sudah dikunci 4:3.
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

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
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
                        aspectRatio: 4 / 3,
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
                height: 600,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
                fillColor: '#fff'
            });
            
            const croppedData = canvas.toDataURL('image/jpeg', 0.9);
            croppedInput.value = croppedData;
            
            previewImage.src = croppedData;
            imagePreviewArea.classList.remove('hidden');
            
            imageInput.value = '';
            cropModal.classList.add('hidden');
            cropModal.classList.remove('flex');
        });
    }
</script>
@endpush
@endif

@endsection
