@extends('layouts.admin')

@section('title', 'Edit Banner - Admin Panel')
@section('page_title', 'Edit Banner Slider')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-3">
            <h3 class="font-bold text-gray-800"><i class="fas fa-edit text-yellow-500 mr-2"></i> Edit Banner</h3>
            <a href="{{ route('admin.banners.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-semibold transition flex items-center justify-center gap-1 bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left"></i> Kembali</a>
            
        </div>
        
        <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Judul Banner <span class="text-red-500">*</span></label>
                <input type="text" name="title" required value="{{ old('title', $banner->title) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-brand-blue outline-none transition">
            </div>
            
            
            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-2">Gambar Banner</label>
                
                <div class="mb-3 border border-gray-200 rounded-lg overflow-hidden bg-gray-50 relative aspect-[21/9]">
                    <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Banner Lama" class="w-full h-full object-cover">
                    <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 hover:opacity-100 transition-opacity">
                        <span class="text-white text-xs font-bold px-2 py-1 bg-black/70 rounded">Gambar Saat Ini</span>
                    </div>
                </div>

                <input type="file" name="image" id="imageInput" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-blue-light file:text-brand-blue hover:file:bg-brand-blue-light cursor-pointer">
                <p class="text-xs text-gray-400 mt-2">
                    Format: JPG/PNG/WEBP (Maks 10 MB)<br>
                    Biarkan kosong jika tidak ingin mengubah gambar. Pilih gambar baru untuk melakukan crop ulang.<br>
                    <strong>Rekomendasi ukuran: 1920 x 600 px (Rasio Landscape 21:9 atau 3:1). Gambar akan dipotong (crop) secara otomatis agar memenuhi lebar layar tanpa terdistorsi.</strong>
                </p>
                
                <input type="hidden" name="cropped_image" id="croppedInput">
                
                <div id="imagePreviewArea" class="mt-4 hidden border border-yellow-300 rounded-lg overflow-hidden bg-gray-50 relative aspect-[21/9] shadow-inner">
                    <img id="previewImage" src="" alt="Preview Baru" class="w-full h-full object-cover">
                    <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 hover:opacity-100 transition-opacity">
                        <span class="text-white text-xs font-bold px-2 py-1 bg-yellow-500 rounded">Hasil Potongan Baru</span>
                    </div>
                </div>
            </div>
            
            <div class="mb-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
                <label class="block text-sm font-bold text-gray-700 mb-2">Status Publikasi</label>
                <div class="flex gap-4">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="is_published" value="1" class="w-4 h-4 text-brand-blue" {{ $banner->is_published ? 'checked' : '' }}>
                        <span class="ml-2 text-sm font-semibold text-gray-700">Publik</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="is_published" value="0" class="w-4 h-4 text-brand-blue" {{ !$banner->is_published ? 'checked' : '' }}>
                        <span class="ml-2 text-sm font-semibold text-gray-700">Draft</span>
                    </label>
                </div>
            </div>

            <div class="mb-6 p-4 border border-gray-200 rounded-lg bg-gray-50">
                <label class="block text-sm font-bold text-gray-700 mb-2">Jadwal Tayang (Opsional)</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Waktu Mulai Tayang</label>
                        <input type="datetime-local" name="start_date" value="{{ old('start_date', $banner->start_date ? $banner->start_date->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded text-sm outline-none focus:ring-1 focus:ring-brand-blue">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Waktu Berakhir Tayang</label>
                        <input type="datetime-local" name="end_date" value="{{ old('end_date', $banner->end_date ? $banner->end_date->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded text-sm outline-none focus:ring-1 focus:ring-brand-blue">
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Jika kosong, banner akan terus tayang apabila statusnya Publik.</p>
            </div>
            
            <button type="submit" class="w-full py-3 bg-yellow-500 text-white font-bold rounded hover:bg-yellow-600 transition shadow-md flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Perbarui Banner
            </button>
        </form>
    </div>
</div>

<!-- Modal Cropper -->
<div id="cropModal" class="fixed inset-0 bg-black/80 z-[9999] hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl flex flex-col overflow-hidden max-h-[90vh]">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800"><i class="fas fa-crop text-brand-blue mr-2"></i> Sesuaikan Ukuran Banner</h3>
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
                <i class="fas fa-info-circle mr-1"></i> Geser, zoom, dan sesuaikan kotak crop. Rasio banner sudah dikunci otomatis.
            </div>
            <div class="flex gap-2">
                <button type="button" id="btnResetCrop" class="px-4 py-2 bg-gray-200 text-gray-700 font-bold rounded hover:bg-gray-300 transition text-sm">
                    <i class="fas fa-sync-alt mr-1"></i> Reset
                </button>
                <button type="button" id="btnSaveCrop" class="px-5 py-2 bg-brand-blue text-white font-bold rounded hover:bg-brand-blue-hover transition shadow text-sm">
                    <i class="fas fa-check mr-1"></i> Terapkan & Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
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

    imageInput.addEventListener('change', function(e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const file = files[0];
            if (file.size > 10 * 1024 * 1024) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Ukuran File Terlalu Besar', text: 'Ukuran foto maksimal 10 MB.' });
                } else {
                    alert('Ukuran foto maksimal 10 MB.');
                }
                imageInput.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(event) {
                cropImage.src = event.target.result;
                cropModal.classList.remove('hidden');
                cropModal.classList.add('flex');
                
                if (cropper) {
                    cropper.destroy();
                }
                
                cropper = new Cropper(cropImage, {
                    aspectRatio: 21 / 9,
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
            imageInput.value = ''; // reset if they cancelled before cropping
        }
    });

    btnResetCrop.addEventListener('click', function() {
        if (cropper) {
            cropper.reset();
        }
    });

    btnSaveCrop.addEventListener('click', function() {
        if (!cropper) return;
        
        const canvas = cropper.getCroppedCanvas({
            maxWidth: 2560,
            maxHeight: 2560,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });
        
        const croppedData = canvas.toDataURL('image/jpeg', 1.0);
        croppedInput.value = croppedData;
        
        previewImage.src = croppedData;
        imagePreviewArea.classList.remove('hidden');
        
        cropModal.classList.add('hidden');
        cropModal.classList.remove('flex');
    });
</script>
@endsection
