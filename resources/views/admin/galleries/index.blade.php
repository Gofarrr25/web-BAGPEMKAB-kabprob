@extends('layouts.admin')

@section('title', 'Galeri & Video - Admin Panel')
@section('page_title', 'Manajemen Galeri Foto & Video')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-8">
    
    <!-- Kolom Kiri: Form Upload -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 sticky top-24">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Tambah Media Baru</h3>
            <form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Judul Galeri / Album <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="Kegiatan Apel Pagi..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-brand-blue outline-none transition">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Teks / Keterangan Album <span class="text-xs font-normal text-gray-500">(Opsional)</span></label>
                    <textarea name="description" id="ck-editor-description" class="w-full border border-gray-300 rounded-lg">{{ old('description') }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Publikasi <span class="text-xs font-normal text-gray-500">(Opsional)</span></label>
                    <input type="datetime-local" name="created_at" value="{{ old('created_at', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-brand-blue outline-none transition">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tipe Media <span class="text-red-500">*</span></label>
                    <select name="type" id="mediaType" onchange="toggleMediaInput()" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-brand-blue bg-white outline-none transition">
                        <option value="image">Foto / Gambar</option>
                        <option value="video">Video</option>
                    </select>
                </div>
                
                <div id="imageInputGroup" class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Unggah Foto <span class="text-red-500">*</span></label>
                    <input type="file" name="file" id="imageInput" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-blue-light file:text-brand-blue hover:file:bg-brand-blue-light cursor-pointer">
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
                    
                    <div class="mt-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Unggah Foto Tambahan untuk Album (Opsional)</label>
                        <!-- Custom Drag & Drop Zone -->
                        <div id="imageAlbumDropzone" class="w-full border-2 border-dashed border-gray-300 rounded-xl p-4 md:p-6 flex flex-col items-center justify-center text-center hover:bg-gray-50 hover:border-brand-blue-pale transition cursor-pointer bg-white relative">
                            <input type="file" id="imageAlbumInput" multiple accept="image/jpeg,image/png,image/webp,image/gif" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <div class="bg-brand-blue-light text-brand-blue rounded-full w-12 h-12 flex items-center justify-center mb-3">
                                <i class="fas fa-cloud-upload-alt text-xl"></i>
                            </div>
                            <h4 class="font-bold text-gray-700 text-sm mb-1">Tarik & Lepas Foto di Sini</h4>
                            <p class="text-xs text-gray-500 mb-3">Atau klik untuk membuka File Explorer</p>
                            <span class="px-3 py-1 bg-gray-200 text-gray-600 text-[10px] font-bold rounded-full">JPG, PNG, WEBP, GIF</span>
                        </div>
                        <input type="file" name="album_files[]" id="imageAlbumHidden" multiple class="hidden">
                        
                        <div class="mt-4">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-bold text-gray-700">Preview Foto Terpilih</label>
                                <span id="imageAlbumCount" class="text-xs font-bold text-brand-blue bg-brand-blue-light px-2 py-1 rounded">0 foto dipilih</span>
                            </div>
                            <div id="imageAlbumPreviewContainer" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 min-h-[100px] empty:flex empty:items-center empty:justify-center">
                                <!-- Previews will be injected here -->
                                <p class="text-xs text-gray-400 empty-state col-span-full text-center">Belum ada foto yang dipilih</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                                <div id="videoInputGroup" class="mb-5" style="display: none;">
                    <div id="youtubeInputWrapper">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Album Video (Tautan YouTube) <span class="text-red-500">*</span></label>
                        <div id="youtubeUrlContainer" class="space-y-4">
                            <div class="flex flex-col gap-2 group bg-gray-50 p-3 rounded-xl border border-gray-200 shadow-sm relative youtube-input-wrapper">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-grip-vertical text-gray-400 cursor-move px-1 hover:text-gray-600"></i>
                                    <input type="url" name="youtube_urls[]" oninput="previewYoutube(this)" required placeholder="https://www.youtube.com/watch?v=..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-brand-blue outline-none transition text-sm font-medium">
                                    <button type="button" onclick="this.closest('.youtube-input-wrapper').remove()" class="bg-red-50 hover:bg-red-100 text-red-500 w-9 h-9 flex items-center justify-center rounded transition shrink-0">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="youtube-preview-container hidden w-full h-48 bg-gray-900 rounded-lg overflow-hidden relative">
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="addYoutubeUrl()" class="mt-4 text-sm bg-brand-blue-light hover:bg-brand-blue-light text-brand-blue font-bold py-2 px-4 rounded-lg transition border border-brand-blue-light w-full flex justify-center items-center gap-2">
                            <i class="fas fa-plus"></i> Tambah Video YouTube
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="w-full py-2 bg-brand-blue text-white font-bold rounded hover:bg-brand-blue-hover transition shadow-md">
                    <i class="fas fa-upload mr-1"></i> Simpan Media
                </button>
            </form>
        </div>
    </div>

        <div class="lg:col-span-1 space-y-6">
            <!-- Bagian Foto -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800"><i class="fas fa-images text-brand-blue mr-2"></i>Galeri Foto</h3>
                </div>
                
                <div class="p-4 md:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($photos as $photo)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden group relative flex flex-col">
                            <div class="w-full h-40 bg-gray-100">
                                <img src="{{ $photo->thumbnail_url }}" class="w-full h-full object-cover" alt="Foto">
                            </div>
                            @php $fotoCount = $photo->galleryItems->count() + ($photo->file_path ? 1 : 0); @endphp
                            <div class="p-3 flex flex-col">
                                @if($fotoCount > 1)
                                    <div class="text-[10px] font-bold text-brand-blue mb-1 flex items-center gap-1.5">
                                        <i class="fas fa-images"></i> Album Foto &bull; {{ $fotoCount }} Foto
                                    </div>
                                @endif
                                <h4 class="font-bold text-gray-800 text-sm truncate" title="{{ $photo->title }}">{{ $photo->title }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $photo->created_at->format('d M Y') }}</p>
                            </div>
                            
                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition flex gap-1">
                                @php
                                    $canEditDelete = !auth()->user()->hasRole('Staf') || $photo->user_id === auth()->id();
                                @endphp
                                @if($canEditDelete)
                                <a href="{{ route('admin.galleries.edit', $photo->id) }}" class="bg-yellow-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow hover:bg-yellow-600" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <form action="{{ route('admin.galleries.destroy', $photo->id) }}" method="POST" onsubmit="event.preventDefault(); confirmDelete(this, 'Galeri', 'Data Terpilih', true);">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow hover:bg-red-600" title="Hapus">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                                @else
                                <span class="bg-black/50 text-white text-xs px-2 py-1 rounded"><i class="fas fa-lock mr-1"></i>Akses Terbatas</span>
                                @endif
                            </div>
                            
                            @if($canEditDelete)
                            <div class="px-3 pb-3 border-t border-gray-100 pt-3 flex items-center justify-between bg-gray-50/50 mt-auto">
                                <span class="text-xs font-bold text-gray-600">Status Publikasi:</span>
                                <label class="relative inline-flex items-center cursor-pointer" title="Aktif/Nonaktifkan Publikasi">
                                    <input type="checkbox" onchange="toggleGalleryStatus('{{ $photo->id }}', this)" class="sr-only peer" {{ $photo->is_active ? 'checked' : '' }}>
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                                </label>
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="col-span-full border border-dashed border-gray-200 rounded-xl p-4 md:p-8 text-center text-gray-500">
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
                
                <div class="p-4 md:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($videos as $video)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden group relative flex flex-col">
                            <div class="w-full h-40 bg-gray-900 flex items-center justify-center relative">
                                <img src="{{ $video->thumbnail_url }}" class="w-full h-full object-cover opacity-60" alt="Video">
                                <i class="fab fa-youtube text-red-500 text-5xl absolute z-10"></i>
                            </div>
                            @php 
                                $vidCount = $video->galleryItems->count() + ($video->video_url || $video->file_path ? 1 : 0); 
                            @endphp
                            <div class="p-3 flex flex-col">
                                @if($vidCount > 1)
                                    <div class="text-[10px] font-bold text-red-600 mb-1 flex items-center gap-1.5">
                                        <i class="fas fa-video"></i> Album Video &bull; {{ $vidCount }} Video
                                    </div>
                                @endif
                                <h4 class="font-bold text-gray-800 text-sm truncate" title="{{ $video->title }}">{{ $video->title }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $video->created_at->format('d M Y') }}</p>
                            </div>
                            
                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition flex gap-1">
                                @php
                                    $canEditDeleteVid = !auth()->user()->hasRole('Staf') || $video->user_id === auth()->id();
                                @endphp
                                @if($canEditDeleteVid)
                                <a href="{{ route('admin.galleries.edit', $video->id) }}" class="bg-yellow-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow hover:bg-yellow-600" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <form action="{{ route('admin.galleries.destroy', $video->id) }}" method="POST" onsubmit="event.preventDefault(); confirmDelete(this, 'Galeri', 'Data Terpilih', true);">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow hover:bg-red-600" title="Hapus">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                                @else
                                <span class="bg-black/50 text-white text-xs px-2 py-1 rounded"><i class="fas fa-lock mr-1"></i>Akses Terbatas</span>
                                @endif
                            </div>
                            
                            @if($canEditDeleteVid)
                            <div class="px-3 pb-3 border-t border-gray-100 pt-3 flex items-center justify-between bg-gray-50/50 mt-auto">
                                <span class="text-xs font-bold text-gray-600">Status Publikasi:</span>
                                <label class="relative inline-flex items-center cursor-pointer" title="Aktif/Nonaktifkan Publikasi">
                                    <input type="checkbox" onchange="toggleGalleryStatus('{{ $video->id }}', this)" class="sr-only peer" {{ $video->is_active ? 'checked' : '' }}>
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                                </label>
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="col-span-full border border-dashed border-gray-200 rounded-xl p-4 md:p-8 text-center text-gray-500">
                            Belum ada video yang diunggah.
                        </div>
                        @endforelse
                    </div>
                    
                    <div class="mt-6">{{ $videos->appends(request()->except('video_page'))->links() }}</div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Cropper -->
<div id="cropModal" class="fixed inset-0 bg-black/80 z-[9999] hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl flex flex-col overflow-hidden max-h-[90vh]">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800"><i class="fas fa-crop text-brand-blue mr-2"></i> Sesuaikan Ukuran Gambar (Rasio 4:3)</h3>
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
                <button type="button" id="btnSaveCrop" class="px-5 py-2 bg-brand-blue text-white font-bold rounded hover:bg-brand-blue-hover transition shadow text-sm">
                    <i class="fas fa-check mr-1"></i> Terapkan & Simpan
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .ck-editor__editable[role="textbox"] {
        min-height: 300px;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
@endpush

@push('scripts')
@include('admin.partials.ckeditor-script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    function toggleGalleryStatus(galleryId, checkbox) {
        const isActive = checkbox.checked;
        fetch(`/admin/galleries/${galleryId}/toggle`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ is_active: isActive })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                checkbox.checked = !isActive;
                alert('Gagal mengubah status galeri');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            checkbox.checked = !isActive;
            alert('Terjadi kesalahan saat mengubah status');
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        if(typeof createCkEditor === 'function') {
            createCkEditor('#ck-editor-description', 'Deskripsi atau keterangan tentang album ini...');
        }
        
        if(document.getElementById('youtubeUrlContainer')) {
            var sortableYoutube = Sortable.create(document.getElementById('youtubeUrlContainer'), {
                animation: 150,
                handle: '.cursor-move'
            });
        }
        
        toggleMediaInput();
    });

    function toggleMediaInput() {
        const type = document.getElementById('mediaType').value;
        const imageInput = document.getElementById('imageInput');
        const ytInputs = document.querySelectorAll('input[name="youtube_urls[]"]');
        
        if(type === 'image') {
            document.getElementById('imageInputGroup').style.display = 'block';
            document.getElementById('videoInputGroup').style.display = 'none';
            if (imageInput) imageInput.disabled = false;
            ytInputs.forEach(input => {
                input.required = false;
                input.disabled = true;
            });
        } else {
            document.getElementById('imageInputGroup').style.display = 'none';
            document.getElementById('videoInputGroup').style.display = 'block';
            if (imageInput) imageInput.disabled = true;
            ytInputs.forEach(input => {
                input.required = true;
                input.disabled = false;
            });
        }
    }

    function extractYoutubeId(url) {
        const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
        const match = url.match(regExp);
        return (match && match[7].length === 11) ? match[7] : false;
    }

    function previewYoutube(input) {
        const wrapper = input.closest('.youtube-input-wrapper');
        const container = wrapper.querySelector('.youtube-preview-container');
        const url = input.value.trim();
        
        container.innerHTML = '';
        
        if (url === '') {
            container.classList.add('hidden');
            input.classList.remove('border-red-500', 'border-green-500');
            return;
        }
        
        const ytId = extractYoutubeId(url);
        
        if (ytId) {
            input.classList.remove('border-red-500');
            input.classList.add('border-green-500');
            container.classList.remove('hidden');
            container.innerHTML = `<iframe src="https://www.youtube.com/embed/${ytId}?rel=0" class="w-full h-full" frameborder="0" allowfullscreen></iframe>`;
        } else {
            input.classList.add('border-red-500');
            input.classList.remove('border-green-500');
            container.classList.remove('hidden');
            container.innerHTML = `<div class="w-full h-full flex flex-col items-center justify-center text-red-500 bg-red-50"><i class="fas fa-exclamation-circle text-2xl mb-1"></i><span class="text-xs font-bold">URL YouTube tidak valid</span></div>`;
        }
    }

    function addYoutubeUrl() {
        const container = document.getElementById('youtubeUrlContainer');
        const wrapper = document.createElement('div');
        wrapper.className = 'flex flex-col gap-2 group bg-gray-50 p-3 rounded-xl border border-gray-200 shadow-sm relative youtube-input-wrapper';
        
        wrapper.innerHTML = `
            <div class="flex items-center gap-2">
                <i class="fas fa-grip-vertical text-gray-400 cursor-move px-1 hover:text-gray-600"></i>
                <input type="url" name="youtube_urls[]" oninput="previewYoutube(this)" required placeholder="https://www.youtube.com/watch?v=..."
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-brand-blue outline-none transition text-sm font-medium">
                <button type="button" onclick="this.closest('.youtube-input-wrapper').remove()" class="bg-red-50 hover:bg-red-100 text-red-500 w-9 h-9 flex items-center justify-center rounded transition shrink-0">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="youtube-preview-container hidden w-full h-48 bg-gray-900 rounded-lg overflow-hidden relative">
            </div>
        `;
        container.appendChild(wrapper);
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

    // MULTIPLE FILE UPLOAD MANAGER (IMAGE ALBUM)
    function setupAlbumManager(visibleInputId, hiddenInputId, previewContainerId, isVideo = false) {
        const visibleInput = document.getElementById(visibleInputId);
        const hiddenInput = document.getElementById(hiddenInputId);
        const previewContainer = document.getElementById(previewContainerId);
        let dt = new DataTransfer();
        let dropzone = null;
        let countDisplay = null;

        if (!visibleInput || !hiddenInput || !previewContainer) return;

        // Cari dropzone container jika ada (berdasarkan struktur HTML baru)
        if (visibleInput.parentElement.id && visibleInput.parentElement.id.includes('Dropzone')) {
            dropzone = visibleInput.parentElement;
        }

        // Cari indikator jumlah foto jika ada
        if (!isVideo) {
            countDisplay = document.getElementById(visibleInputId.replace('Input', 'Count'));
        }

        // Event Drag & Drop
        if (dropzone) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => dropzone.classList.add('border-brand-blue', 'bg-brand-blue-light'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => dropzone.classList.remove('border-brand-blue', 'bg-brand-blue-light'), false);
            });

            dropzone.addEventListener('drop', function(e) {
                const files = e.dataTransfer.files;
                handleFiles(files);
            }, false);
        }

        visibleInput.addEventListener('change', function(e) {
            handleFiles(e.target.files);
            visibleInput.value = ''; // Reset
        });

        function handleFiles(files) {
            for(let i = 0; i < files.length; i++) {
                const file = files[i];
                const validImageTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                const validVideoTypes = ['video/mp4', 'video/webm', 'video/ogg'];
                
                if (isVideo && !validVideoTypes.includes(file.type)) {
                    CustomUploadAlert.showError(visibleInput, 'Format file tidak sesuai.<br>Hanya MP4, WEBM, OGG yang diizinkan untuk video.');
                    continue;
                } else if (!isVideo && !validImageTypes.includes(file.type)) {
                    CustomUploadAlert.showError(visibleInput, 'Format file tidak sesuai.<br>Hanya JPG, PNG, WEBP, GIF yang diizinkan untuk foto.');
                    continue;
                }

                // Cek duplikasi
                let isDuplicate = false;
                for(let j=0; j<dt.items.length; j++) {
                    if(dt.items[j].getAsFile().name === file.name && dt.items[j].getAsFile().size === file.size) {
                        isDuplicate = true; break;
                    }
                }
                if(isDuplicate) continue;

                dt.items.add(file);
                createPreview(file, dt.items.length - 1);
            }
            
            updateHiddenInput();
        }

        function createPreview(file, index) {
            // Hilangkan pesan kosong jika ada
            const emptyState = previewContainer.querySelector('.empty-state');
            if(emptyState) emptyState.style.display = 'none';

            const wrapper = document.createElement('div');
            wrapper.className = 'relative border border-gray-200 rounded-lg overflow-hidden shadow-sm aspect-square bg-gray-100 group transition hover:shadow-md';
            wrapper.dataset.index = index;
            wrapper.dataset.name = file.name;

            if (isVideo) {
                wrapper.innerHTML = `
                    <div class="w-full h-full flex flex-col items-center justify-center p-2 text-center bg-white">
                        <i class="fas fa-file-video text-3xl text-red-400 mb-1"></i>
                        <span class="text-[10px] font-bold text-gray-700 truncate w-full" title="${file.name}">${file.name}</span>
                        <span class="text-[9px] text-gray-400">${(file.size / 1024 / 1024).toFixed(1)} MB</span>
                    </div>
                `;
            } else {
                wrapper.innerHTML = `
                    <div class="absolute inset-0 border-2 border-transparent group-hover:border-brand-blue-pale rounded-lg pointer-events-none z-10 transition"></div>
                    <div class="absolute top-1 left-1 bg-green-500 text-white w-5 h-5 rounded-full flex items-center justify-center z-10 shadow-sm border border-white">
                        <i class="fas fa-check text-[10px]"></i>
                    </div>
                `;
                const img = document.createElement('img');
                img.className = 'w-full h-full object-cover';
                img.src = URL.createObjectURL(file);
                wrapper.appendChild(img);
            }

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.title = 'Hapus dari daftar';
            removeBtn.className = 'absolute top-1 right-1 bg-white text-red-500 hover:text-white border border-gray-200 hover:border-red-500 rounded-full w-6 h-6 flex items-center justify-center text-[10px] hover:bg-red-500 focus:outline-none opacity-0 group-hover:opacity-100 transition z-20 shadow-sm';
            removeBtn.innerHTML = '<i class="fas fa-trash-alt"></i>';
            removeBtn.onclick = function() {
                removeFile(file.name);
            };

            wrapper.appendChild(removeBtn);
            previewContainer.appendChild(wrapper);
        }

        function removeFile(fileName) {
            for(let i = 0; i < dt.items.length; i++) {
                if (dt.items[i].getAsFile().name === fileName) {
                    dt.items.remove(i);
                    break;
                }
            }
            updateHiddenInput();
            
            const previews = previewContainer.querySelectorAll('div[data-name]');
            previews.forEach(p => {
                if(p.dataset.name === fileName) {
                    p.remove();
                }
            });

            // Tampilkan kembali pesan kosong jika tidak ada file
            if(dt.items.length === 0) {
                const emptyState = previewContainer.querySelector('.empty-state');
                if(emptyState) emptyState.style.display = 'block';
            }
        }

        function updateHiddenInput() {
            hiddenInput.files = dt.files;
            if (countDisplay) {
                countDisplay.innerHTML = `<i class="fas fa-images mr-1"></i> ${dt.files.length} foto dipilih`;
                if(dt.files.length > 0) {
                    countDisplay.classList.replace('text-gray-500', 'text-brand-blue');
                    countDisplay.classList.replace('bg-gray-100', 'bg-brand-blue-light');
                } else {
                    countDisplay.classList.replace('text-brand-blue', 'text-gray-500');
                    countDisplay.classList.replace('bg-brand-blue-light', 'bg-gray-100');
                }
            }
        }
    }

    setupAlbumManager('imageAlbumInput', 'imageAlbumHidden', 'imageAlbumPreviewContainer', false);
    setupAlbumManager('localVideoAlbumInput', 'localVideoAlbumHidden', 'localVideoAlbumPreviewContainer', true);

</script>
@endpush
@endsection

