@extends('layouts.admin')

@section('title', 'Galeri & Video - Admin Panel')
@section('page_title', 'Manajemen Galeri Foto & Video')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Kolom Kiri: Form Upload -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-24">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Tambah Media Baru</h3>
            <form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Judul Media <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="Kegiatan Apel Pagi..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Publikasi <span class="text-xs font-normal text-gray-500">(Opsional)</span></label>
                    <input type="datetime-local" name="created_at" value="{{ old('created_at', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tipe Media <span class="text-red-500">*</span></label>
                    <select name="type" id="mediaType" onchange="toggleMediaInput()" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 bg-white outline-none transition">
                        <option value="image">Foto / Gambar</option>
                        <option value="video">Video</option>
                    </select>
                </div>
                
                <div id="imageInputGroup" class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Unggah Foto <span class="text-red-500">*</span></label>
                    <input type="file" name="file" id="imageInput" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-gray-400 mt-2">
                        Otomatis konversi ke format WebP ringan.<br>
                        <strong>Rekomendasi ukuran: 800 x 600 px (Rasio 4:3) atau 1080 x 1080 px (Rasio 1:1). Gambar akan dipotong sesuai rasio 4:3.</strong>
                    </p>
                    <input type="hidden" name="cropped_image" id="croppedInput">
                    
                    <div id="imagePreviewArea" class="mt-4 hidden border border-gray-200 rounded-lg overflow-hidden bg-gray-50 relative aspect-[4/3]">
                        <img id="previewImage" src="" alt="Preview" class="w-full h-full object-cover">
                        <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 hover:opacity-100 transition-opacity">
                            <span class="text-white text-xs font-bold px-2 py-1 bg-black/70 rounded">Hasil Potongan</span>
                        </div>
                    </div>
                </div>
                
                <div id="videoInputGroup" class="mb-5" style="display: none;">
                    <div class="mb-3">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Sumber Video</label>
                        <select id="videoSourceType" onchange="toggleVideoSource()" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none transition">
                            <option value="youtube">Tautan YouTube</option>
                            <option value="local">Unggah Video Lokal</option>
                        </select>
                    </div>

                    <div id="youtubeInputWrapper">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tautan Video YouTube <span class="text-red-500">*</span></label>
                        <input type="url" name="video_url" id="videoUrlInput" placeholder="https://www.youtube.com/watch?v=..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>

                    <div id="localVideoWrapper" style="display: none;">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Unggah Video Lokal (MP4/WebM/Ogg) <span class="text-red-500">*</span></label>
                        <input type="file" name="file" id="localVideoInput" disabled accept="video/mp4,video/webm,video/ogg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">
                    </div>
                </div>
                
                <button type="submit" class="w-full py-2 bg-blue-600 text-white font-bold rounded hover:bg-blue-700 transition shadow-md">
                    <i class="fas fa-upload mr-1"></i> Simpan Media
                </button>
            </form>
        </div>
    </div>

    <!-- Kolom Kanan: Grid Galeri -->
    <div class="lg:col-span-2">
        
        <!-- Bagian Foto -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800"><i class="fas fa-image text-blue-500 mr-2"></i>Album Foto</h3>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @forelse($photos as $photo)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden group relative">
                        <img src="{{ asset('storage/' . $photo->file_path) }}" class="w-full h-40 object-cover" alt="{{ $photo->title }}">
                        <div class="p-3">
                            <h4 class="font-bold text-gray-800 text-sm truncate" title="{{ $photo->title }}">{{ $photo->title }}</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ $photo->created_at->format('d M Y') }}</p>
                        </div>
                        
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition flex gap-1">
                            <a href="{{ route('admin.galleries.edit', $photo->id) }}" class="bg-yellow-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow hover:bg-yellow-600" title="Edit">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <form action="{{ route('admin.galleries.destroy', $photo->id) }}" method="POST" onsubmit="return confirm('Hapus foto ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow hover:bg-red-600" title="Hapus">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full border border-dashed border-gray-200 rounded-xl p-8 text-center text-gray-500">
                        Belum ada foto yang diunggah.
                    </div>
                    @endforelse
                </div>
                
                <div class="mt-6">{{ $photos->appends(request()->except('photo_page'))->links() }}</div>
            </div>
        </div>

        <!-- Bagian Video -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800"><i class="fas fa-video text-red-500 mr-2"></i>Album Video</h3>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @forelse($videos as $video)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden group relative">
                        <div class="w-full h-40 bg-gray-900 flex items-center justify-center relative">
                            <img src="{{ $video->thumbnail_url }}" class="w-full h-full object-cover opacity-60" alt="Video">
                            <i class="fab fa-youtube text-red-500 text-5xl absolute z-10"></i>
                        </div>
                        <div class="p-3">
                            <h4 class="font-bold text-gray-800 text-sm truncate" title="{{ $video->title }}">{{ $video->title }}</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ $video->created_at->format('d M Y') }}</p>
                        </div>
                        
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition flex gap-1">
                            <a href="{{ route('admin.galleries.edit', $video->id) }}" class="bg-yellow-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow hover:bg-yellow-600" title="Edit">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <form action="{{ route('admin.galleries.destroy', $video->id) }}" method="POST" onsubmit="return confirm('Hapus video ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow hover:bg-red-600" title="Hapus">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full border border-dashed border-gray-200 rounded-xl p-8 text-center text-gray-500">
                        Belum ada video yang diunggah.
                    </div>
                    @endforelse
                </div>
                
                <div class="mt-6">{{ $videos->appends(request()->except('video_page'))->links() }}</div>
            </div>
        </div>

    </div>
</div>

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
    function toggleMediaInput() {
        const type = document.getElementById('mediaType').value;
        const imageInput = document.getElementById('imageInput');
        const localVideoInput = document.getElementById('localVideoInput');
        
        if(type === 'image') {
            document.getElementById('imageInputGroup').style.display = 'block';
            document.getElementById('videoInputGroup').style.display = 'none';
            if (imageInput) imageInput.disabled = false;
            if (localVideoInput) localVideoInput.disabled = true;
        } else {
            document.getElementById('imageInputGroup').style.display = 'none';
            document.getElementById('videoInputGroup').style.display = 'block';
            if (imageInput) imageInput.disabled = true;
            toggleVideoSource();
        }
    }

    function toggleVideoSource() {
        const source = document.getElementById('videoSourceType').value;
        const localVideoInput = document.getElementById('localVideoInput');
        const videoUrlInput = document.getElementById('videoUrlInput');
        
        if(source === 'youtube') {
            document.getElementById('youtubeInputWrapper').style.display = 'block';
            document.getElementById('localVideoWrapper').style.display = 'none';
            if(localVideoInput) localVideoInput.disabled = true;
            if(videoUrlInput) videoUrlInput.disabled = false;
        } else {
            document.getElementById('youtubeInputWrapper').style.display = 'none';
            document.getElementById('localVideoWrapper').style.display = 'block';
            if(localVideoInput) localVideoInput.disabled = false;
            if(videoUrlInput) videoUrlInput.disabled = true;
        }
    }

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
@endsection
