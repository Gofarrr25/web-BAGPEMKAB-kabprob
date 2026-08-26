@extends('layouts.admin')

@section('title', 'Manajemen Struktur Organisasi')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Struktur Organisasi</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola gambar Struktur Organisasi yang akan ditampilkan di halaman utama website.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center shadow-sm">
            <i class="fas fa-check-circle mr-3 text-lg"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <form action="{{ route('admin.organization-members.upload-photo') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf
            
            <div class="flex flex-col md:flex-row gap-8 items-start">
                <!-- Kolom Upload -->
                <div class="flex-1 w-full space-y-4">
                    <label class="block text-sm font-bold text-gray-700">Upload Foto Struktur Organisasi</label>
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:bg-gray-50 transition relative">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                        <p class="text-gray-600 font-medium mb-1">Pilih Gambar Struktur Organisasi</p>
                        <p class="text-xs text-gray-400 mb-4">Format: JPG, PNG, WEBP (Max 5MB)</p>
                        
                        <input type="file" name="image" id="imageInput" accept="image/jpeg,image/png,image/webp,image/gif" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" @if(!$photo) required @endif>
                        <input type="hidden" name="cropped_image" id="croppedInput">
                        
                        <button type="button" class="px-4 py-2 bg-blue-50 text-blue-600 border border-blue-200 rounded-lg text-sm font-bold">
                            Jelajahi File
                        </button>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                        <div class="text-xs text-blue-800 space-y-1">
                            <p><strong>Rekomendasi:</strong> Gunakan ukuran minimal <strong>1920 &times; 1080 px</strong> agar gambar tidak pecah saat di-zoom oleh pengunjung.</p>
                            <p>Anda dapat memotong (crop) gambar setelah memilih file, atau mencentang opsi "Gunakan Ukuran Asli" jika struktur organisasi Anda memanjang ke bawah (vertikal) atau memiliki rasio bebas.</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex gap-3">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-sm transition">
                            <i class="fas fa-save mr-2"></i> Simpan Gambar
                        </button>
                        
                        @if($photo)
                        <button type="button" onclick="if(confirm('Apakah Anda yakin ingin menghapus struktur organisasi ini?')) document.getElementById('deletePhotoForm').submit();" class="px-6 py-2.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 font-bold border border-red-200 transition">
                            <i class="fas fa-trash-alt mr-2"></i> Hapus
                        </button>
                        @endif
                    </div>
                </div>
                
                <!-- Kolom Preview -->
                <div class="flex-1 w-full bg-gray-50 rounded-xl border border-gray-200 p-4 relative">
                    <p class="text-sm font-bold text-gray-700 mb-3 border-b border-gray-200 pb-2">Preview Gambar Saat Ini</p>
                    <div id="imagePreviewArea" class="bg-white rounded-lg border border-gray-200 flex items-center justify-center min-h-[300px] relative overflow-hidden p-2">
                        @if($photo)
                            <img id="previewImage" src="{{ asset('storage/' . $photo) }}" alt="Preview" class="max-w-full max-h-[400px] object-contain rounded">
                        @else
                            <div id="noImagePlaceholder" class="text-center text-gray-400">
                                <i class="fas fa-image text-5xl mb-2"></i>
                                <p class="text-sm">Belum ada gambar</p>
                            </div>
                            <img id="previewImage" src="" alt="Preview" class="max-w-full max-h-[400px] object-contain rounded hidden">
                        @endif
                    </div>
                </div>
            </div>
        </form>
        
        <!-- Form Delete Hidden -->
        @if($photo)
        <form id="deletePhotoForm" action="{{ route('admin.organization-members.delete-photo') }}" method="POST" class="hidden">
            @csrf
        </form>
        @endif
    </div>
</div>

<!-- Modal Cropper -->
<div id="cropModal" class="fixed inset-0 bg-black/80 z-[9999] hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl flex flex-col overflow-hidden max-h-[90vh]">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800"><i class="fas fa-crop text-blue-600 mr-2"></i> Sesuaikan Gambar Struktur Organisasi</h3>
            <button type="button" id="closeCropModal" class="text-gray-400 hover:text-red-500 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-4 bg-gray-900 flex-1 overflow-hidden relative min-h-[60vh]">
            <div class="w-full h-full flex items-center justify-center">
                <img id="cropImage" src="" alt="Picture to crop" class="max-w-full max-h-full">
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <input type="checkbox" id="bypassCropCheckbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="bypassCropCheckbox" class="text-sm font-bold text-gray-700 cursor-pointer">Gunakan Ukuran Asli Gambar (Lewati Crop)</label>
            </div>
            <div class="flex gap-2">
                <button type="button" id="btnResetCrop" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-bold transition">
                    <i class="fas fa-sync-alt mr-1"></i> Reset
                </button>
                <button type="button" id="btnSaveCrop" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-sm transition">
                    <i class="fas fa-check mr-1"></i> Terapkan
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
    const bypassCropCheckbox = document.getElementById('bypassCropCheckbox');
    
    // Elements for placeholder
    const noImagePlaceholder = document.getElementById('noImagePlaceholder');

    let originalFile = null;
    let originalDataURL = null;

    imageInput.addEventListener('change', function(e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            originalFile = files[0];
            const reader = new FileReader();
            reader.onload = function(event) {
                originalDataURL = event.target.result;
                cropImage.src = originalDataURL;
                cropModal.classList.remove('hidden');
                cropModal.classList.add('flex');
                
                // Uncheck bypass by default
                bypassCropCheckbox.checked = false;
                
                if (cropper) {
                    cropper.destroy();
                }
                
                cropper = new Cropper(cropImage, {
                    // No fixed aspect ratio to allow free cropping
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.9,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                });
            };
            reader.readAsDataURL(originalFile);
        }
    });

    closeCropModal.addEventListener('click', function() {
        cropModal.classList.remove('flex');
        cropModal.classList.add('hidden');
        imageInput.value = '';
    });

    btnResetCrop.addEventListener('click', function() {
        if (cropper) {
            cropper.reset();
        }
    });

    btnSaveCrop.addEventListener('click', function() {
        if (bypassCropCheckbox.checked) {
            // Jika bypass, biarkan input asli (image) terkirim, kosongkan croppedInput
            croppedInput.value = '';
            
            // Set preview ke gambar asli
            previewImage.src = originalDataURL;
            previewImage.classList.remove('hidden');
            if(noImagePlaceholder) noImagePlaceholder.classList.add('hidden');
            
            cropModal.classList.remove('flex');
            cropModal.classList.add('hidden');
        } else {
            if (!cropper) return;
            
            // Dapatkan canvas dengan kualitas tinggi tanpa memaksa ukuran spesifik,
            // tetapi batasi maxWidth agar browser tidak crash jika gambar sangat besar
            const canvas = cropper.getCroppedCanvas({
                maxWidth: 4096,
                maxHeight: 4096,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });
            
            const croppedData = canvas.toDataURL('image/jpeg', 0.9);
            croppedInput.value = croppedData;
            
            previewImage.src = croppedData;
            previewImage.classList.remove('hidden');
            if(noImagePlaceholder) noImagePlaceholder.classList.add('hidden');
            
            // Hapus value file input agar form menggunakan base64 (kecuali required)
            // Sebenarnya controller cek cropped_image dulu, jadi aman
            
            cropModal.classList.remove('flex');
            cropModal.classList.add('hidden');
        }
    });
</script>
@endsection
