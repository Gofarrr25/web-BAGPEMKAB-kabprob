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
        
        <form action="{{ route('admin.galleries.update', $gallery->id) }}" method="POST" enctype="multipart/form-data" class="p-4 md:p-6">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Judul Galeri / Album <span class="text-red-500">*</span></label>
                <input type="text" name="title" required value="{{ old('title', $gallery->title) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-brand-blue outline-none transition">
                @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Teks / Keterangan Album <span class="text-xs font-normal text-gray-500">(Opsional)</span></label>
                <textarea name="description" id="ck-editor-description" class="w-full border border-gray-300 rounded-lg">{{ old('description', $gallery->description) }}</textarea>
                @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Publikasi</label>
                <input type="datetime-local" name="created_at" value="{{ old('created_at', $gallery->created_at->format('Y-m-d\TH:i')) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-brand-blue outline-none transition">
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
                            <input type="file" name="file" id="imageInput" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-blue-light file:text-brand-blue hover:file:bg-brand-blue-light cursor-pointer">
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

                <div class="mb-5 bg-gray-50 p-4 border border-gray-200 rounded-lg">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tambah Foto ke Album <span class="text-xs font-normal text-gray-500">(Opsional)</span></label>
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
                            <label class="block text-sm font-bold text-gray-700">Preview Foto Baru Terpilih</label>
                            <span id="imageAlbumCount" class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded">0 foto dipilih</span>
                        </div>
                        <div id="imageAlbumPreviewContainer" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 min-h-[100px] empty:flex empty:items-center empty:justify-center">
                            <!-- Previews will be injected here -->
                            <p class="text-xs text-gray-400 empty-state col-span-full text-center">Belum ada foto baru yang dipilih</p>
                        </div>
                    </div>
                    
                    @if($gallery->galleryItems->count() > 0)
                    <div class="mt-4">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Foto Album Saat Ini:</label>
                        <ul class="space-y-2" id="albumList">
                            @foreach($gallery->galleryItems as $img)
                            <li class="flex items-center justify-between p-2 border border-gray-200 rounded-lg bg-white" data-id="{{ $img->id }}">
                                <div class="flex items-center gap-3">
                                    <div class="cursor-move text-gray-400 hover:text-gray-600"><i class="fas fa-grip-vertical"></i></div>
                                    <img src="{{ asset('storage/' . $img->file_path) }}" class="w-12 h-12 object-cover rounded shadow-sm">
                                </div>
                                <button type="button" onclick="deleteAlbumItem('{{ $img->id }}')" class="text-red-500 hover:bg-red-50 p-1.5 rounded-lg transition"><i class="fas fa-trash"></i></button>
                            </li>
                            @endforeach
                        </ul>
                        <button type="button" onclick="saveAlbumOrder()" class="mt-2 text-xs bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1.5 px-3 rounded w-full transition">Simpan Urutan Album</button>
                    </div>
                    @endif
                </div>
            @else
                <div class="mb-5 bg-gray-50 p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-bold text-gray-700">Album Video (Tautan YouTube) <span class="text-red-500">*</span></label>
                    </div>
                    
                    <div id="youtubeUrlContainer" class="space-y-4">
                        @forelse($gallery->galleryItems->whereNotNull('video_url')->sortBy('order_index') as $vid)
                        <div class="flex flex-col gap-2 group bg-white p-3 rounded-xl border border-gray-200 shadow-sm relative youtube-input-wrapper">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-grip-vertical text-gray-400 cursor-move px-1 hover:text-gray-600"></i>
                                <input type="url" name="youtube_urls[]" value="{{ $vid->video_url }}" oninput="previewYoutube(this)" required placeholder="https://www.youtube.com/watch?v=..."
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-brand-blue outline-none transition text-sm font-medium">
                                <button type="button" onclick="this.closest('.youtube-input-wrapper').remove()" class="bg-red-50 hover:bg-red-100 text-red-500 w-9 h-9 flex items-center justify-center rounded transition shrink-0">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="youtube-preview-container w-full h-48 bg-gray-900 rounded-lg overflow-hidden relative">
                                @php
                                    preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $vid->video_url, $matches);
                                    $ytId = $matches[2] ?? null;
                                @endphp
                                @if($ytId)
                                    <iframe src="https://www.youtube.com/embed/{{ $ytId }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-red-500 bg-red-50"><i class="fas fa-exclamation-circle text-2xl mb-1"></i><span class="text-xs font-bold">URL YouTube tidak valid</span></div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="flex flex-col gap-2 group bg-white p-3 rounded-xl border border-gray-200 shadow-sm relative youtube-input-wrapper">
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
                        @endforelse
                    </div>
                    <button type="button" onclick="addYoutubeUrl()" class="mt-4 text-sm bg-white hover:bg-brand-blue-light text-brand-blue font-bold py-2 px-4 rounded-lg transition border border-brand-blue-light w-full flex justify-center items-center gap-2 shadow-sm">
                        <i class="fas fa-plus"></i> Tambah Tautan YouTube
                    </button>
                    
                    <p class="text-xs text-gray-500 mt-4 border-t border-gray-200 pt-3">
                        <i class="fas fa-info-circle mr-1 text-brand-blue"></i> Jika Anda menambahkan lebih dari satu tautan YouTube, media ini otomatis akan menjadi <strong>Album Video</strong>. Geser ikon <i class="fas fa-grip-vertical text-gray-400 mx-1"></i> untuk mengubah urutan. Urutan saat disimpan akan menjadi urutan tayang di halaman website.
                    </p>
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

@push('scripts')
@include('admin.partials.ckeditor-script')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof createCkEditor === 'function') {
            createCkEditor('#ck-editor-description', 'Deskripsi atau keterangan tentang album ini...');
        }
    });

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
        wrapper.className = 'flex flex-col gap-2 group bg-white p-3 rounded-xl border border-gray-200 shadow-sm relative youtube-input-wrapper';
        
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

    if(document.getElementById('albumList')) {
        var sortable = Sortable.create(document.getElementById('albumList'), {
            animation: 150,
            handle: '.cursor-move'
        });
    }

    if(document.getElementById('youtubeUrlContainer')) {
        var sortableYoutube = Sortable.create(document.getElementById('youtubeUrlContainer'), {
            animation: 150,
            handle: '.cursor-move'
        });
    }

    function deleteAlbumItem(id) {
        confirmAjaxDelete(() => {
            fetch(`/admin/galleries/{{ $gallery->id }}/delete-item/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(r => window.location.reload());
        }, 'Item Galeri');
    }

    function saveAlbumOrder() {
        const listItems = document.querySelectorAll('#albumList li');
        let orders = [];
        listItems.forEach((li, index) => {
            orders.push({ id: li.dataset.id, order_index: index });
        });
        
        fetch(`/admin/galleries/{{ $gallery->id }}/reorder-items`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ orders: orders })
        }).then(res => res.json()).then(data => {
            if(data.success) {
                Swal.fire({icon: 'success', title: 'Berhasil', text: 'Urutan berhasil disimpan!', timer: 1500, showConfirmButton: false});
            }
        });
    }

    // MULTIPLE FILE UPLOAD MANAGER
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
