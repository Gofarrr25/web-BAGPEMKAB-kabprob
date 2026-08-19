@extends('layouts.admin')

@section('title', 'Banner Slider - Admin Panel')
@section('page_title', 'Manajemen Banner Slider Depan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Kolom Kiri: Form -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-24">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Tambah Banner Utama</h3>
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Judul Banner <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="Selamat Datang di Portal..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tautan URL (Opsional)</label>
                    <input type="url" name="link_url" value="{{ old('link_url') }}" placeholder="https://..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none transition">
                    <p class="text-xs text-gray-400 mt-1">URL yang akan dibuka saat banner diklik.</p>
                </div>
                
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Gambar Banner <span class="text-red-500">*</span></label>
                    <input type="file" name="image" id="imageInput" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-gray-400 mt-2">
                        Pilih gambar, lalu Anda dapat menyesuaikan area crop (potong) secara interaktif.<br>
                        <strong>Rekomendasi ukuran: 1920 x 600 px (Rasio Landscape 21:9 atau 3:1). Gambar akan dipotong (crop) secara otomatis agar memenuhi lebar layar tanpa terdistorsi.</strong>
                    </p>
                    <input type="hidden" name="cropped_image" id="croppedInput">
                    
                    <div id="imagePreviewArea" class="mt-4 hidden border border-gray-200 rounded-lg overflow-hidden bg-gray-50 relative aspect-[21/9]">
                        <img id="previewImage" src="" alt="Preview" class="w-full h-full object-cover">
                        <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 hover:opacity-100 transition-opacity">
                            <span class="text-white text-xs font-bold px-2 py-1 bg-black/70 rounded">Hasil Potongan</span>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="w-full py-2 bg-blue-600 text-white font-bold rounded hover:bg-blue-700 transition shadow-md">
                    <i class="fas fa-upload mr-1"></i> Unggah Banner
                </button>
            </form>
        </div>
    </div>

    <!-- Kolom Kanan: Tabel -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-gray-800">Daftar Banner Aktif</h3>
            </div>
            <div class="p-4 space-y-4">
                @forelse($banners as $banner)
                <div class="border border-gray-100 rounded-lg overflow-hidden flex flex-col sm:flex-row shadow-sm hover:shadow-md transition">
                    <div class="w-full sm:w-1/3 bg-gray-100 h-32 relative">
                        <img src="{{ asset('storage/' . $banner->image_path) }}" class="w-full h-full object-cover" alt="Banner">
                    </div>
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h4 class="font-bold text-gray-800 text-lg mb-1">{{ $banner->title }}</h4>
                            @if($banner->link_url)
                                <a href="{{ $banner->link_url }}" target="_blank" class="text-xs text-blue-500 hover:underline"><i class="fas fa-link"></i> {{ $banner->link_url }}</a>
                            @endif
                        </div>
                        <div class="flex justify-between items-center mt-4">
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold border border-green-200">Aktif</span>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.banners.edit', $banner->id) }}" class="text-yellow-600 hover:text-yellow-700 font-bold px-3 py-1 rounded border border-yellow-200 hover:bg-yellow-50 transition text-sm">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Hapus banner ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold px-3 py-1 rounded border border-red-200 hover:bg-red-50 transition text-sm">
                                        <i class="fas fa-trash mr-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center p-10 border-2 border-dashed border-gray-200 rounded-lg text-gray-500">
                    <i class="fas fa-image text-4xl mb-3 text-gray-300"></i>
                    <p>Belum ada banner yang diunggah.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal Cropper -->
<div id="cropModal" class="fixed inset-0 bg-black/80 z-[9999] hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl flex flex-col overflow-hidden max-h-[90vh]">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800"><i class="fas fa-crop text-blue-600 mr-2"></i> Sesuaikan Ukuran Banner</h3>
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
                <button type="button" id="btnSaveCrop" class="px-5 py-2 bg-blue-600 text-white font-bold rounded hover:bg-blue-700 transition shadow text-sm">
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
            width: 1920,
            height: Math.round(1920 / (21/9)),
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });
        
        const croppedData = canvas.toDataURL('image/jpeg', 0.9);
        croppedInput.value = croppedData;
        
        previewImage.src = croppedData;
        imagePreviewArea.classList.remove('hidden');
        
        cropModal.classList.add('hidden');
        cropModal.classList.remove('flex');
    });
</script>
@endsection
