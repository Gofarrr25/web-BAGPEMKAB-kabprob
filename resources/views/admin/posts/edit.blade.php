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
    <div class="px-4 md:px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-wrap gap-3 justify-between items-center">
        <h3 class="font-bold text-gray-800">Form Edit Berita</h3>
            <a href="{{ route('admin.posts.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-semibold transition flex items-center justify-center gap-1 bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left"></i> Kembali</a>
        <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">Mode Edit</span>
    </div>
    
    <div class="p-4 md:p-6">
        <form action="{{ route('admin.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data" id="editPostForm">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-8">
                <!-- Kolom Kiri: Judul & Konten -->
                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Judul Berita <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required value="{{ old('title', $post->title) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue outline-none transition text-base font-bold text-gray-800">
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
                        <select name="category_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue bg-white text-sm font-semibold">
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
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue outline-none transition text-sm">
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
                        <input type="file" name="image" id="imageInput" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-blue file:text-white hover:file:bg-brand-blue cursor-pointer">
                        <p class="text-xs text-gray-400 mt-2">
                            Format: JPG/PNG/WEBP (Maks 1 MB)<br>
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

                        <!-- Custom Drag & Drop Zone -->
                        <div id="imageAlbumDropzone" class="w-full border-2 border-dashed border-gray-300 rounded-xl p-4 md:p-6 flex flex-col items-center justify-center text-center hover:bg-gray-50 hover:border-brand-blue-pale transition cursor-pointer bg-white relative">
                            <input type="file" id="album_images_input" multiple accept="image/jpeg,image/png,image/webp,image/gif" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <div class="bg-brand-blue-light text-brand-blue rounded-full w-12 h-12 flex items-center justify-center mb-3">
                                <i class="fas fa-cloud-upload-alt text-xl"></i>
                            </div>
                            <h4 class="font-bold text-gray-700 text-sm mb-1">Tarik & Lepas Foto di Sini</h4>
                            <p class="text-xs text-gray-500 mb-3">Atau klik untuk membuka File Explorer</p>
                            <span class="px-3 py-1 bg-gray-200 text-gray-600 text-[10px] font-bold rounded-full">JPG, PNG, WEBP (Maks 1 MB)</span>
                        </div>
                        <input type="file" name="album_images[]" id="album_images_hidden" multiple class="hidden">
                        
                        <div class="mt-4">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-bold text-gray-700">Preview Foto Terpilih</label>
                                <span id="album_images_Count" class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded">0 foto dipilih</span>
                            </div>
                            <div id="albumImagesPreviewContainer" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 min-h-[100px] empty:flex empty:items-center empty:justify-center">
                                <!-- Previews will be injected here -->
                                <p class="text-xs text-gray-400 empty-state col-span-full text-center">Belum ada foto yang dipilih</p>
                            </div>
                        </div>
                        @error('album_images.*') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        
                        @if($post->postImages->count() > 0)
                        <div class="mt-4">
                            <label class="block text-xs font-bold text-gray-700 mb-2">Foto Album Saat Ini:</label>
                            <ul class="space-y-2" id="albumList">
                                @foreach($post->postImages as $img)
                                <li class="flex items-center justify-between p-2 border border-gray-200 rounded-lg bg-white" data-id="{{ $img->id }}">
                                    <div class="flex items-center gap-3">
                                        <div class="cursor-move text-gray-400 hover:text-gray-600"><i class="fas fa-grip-vertical"></i></div>
                                        <img src="{{ asset('storage/' . $img->image_path) }}" class="w-12 h-12 object-cover rounded shadow-sm">
                                    </div>
                                    <button type="button" onclick="deleteAlbumImage('{{ $img->id }}')" class="text-red-500 hover:bg-red-50 p-1.5 rounded-lg transition"><i class="fas fa-trash"></i></button>
                                </li>
                                @endforeach
                            </ul>
                            <button type="button" onclick="saveAlbumOrder()" class="mt-2 text-xs bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1.5 px-3 rounded w-full transition">Simpan Urutan Album</button>
                        </div>
                        @endif
                    </div>

                    <!-- Status -->
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Status Publikasi</label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_published" value="1" {{ $post->is_published ? 'checked' : '' }} class="w-4 h-4 text-brand-blue rounded focus:ring-brand-blue">
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
            <h3 class="font-bold text-gray-800"><i class="fas fa-crop text-brand-blue mr-2"></i> Sesuaikan Ukuran Gambar (Rasio 16:9)</h3>
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
                <button type="button" id="btnSaveCrop" class="px-5 py-2 bg-brand-blue text-white font-bold rounded hover:bg-brand-blue-hover transition shadow text-sm">
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
                if (file.size > 1024 * 1024) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: 'Ukuran File Terlalu Besar', text: 'Ukuran foto utama tidak boleh lebih dari 1 MB.' });
                    } else {
                        alert('Ukuran foto utama tidak boleh lebih dari 1 MB.');
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
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    if(document.getElementById('albumList')) {
        var sortable = Sortable.create(document.getElementById('albumList'), {
            animation: 150,
            handle: '.cursor-move'
        });
    }

    function deleteAlbumImage(id) {
        confirmAjaxDelete(() => {
            fetch(`/admin/posts/{{ $post->id }}/delete-image/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(r => window.location.reload());
        }, 'Foto Album');
    }

    function saveAlbumOrder() {
        const listItems = document.querySelectorAll('#albumList li');
        let orders = [];
        listItems.forEach((li, index) => {
            orders.push({ id: li.dataset.id, order_index: index });
        });
        
        fetch(`/admin/posts/{{ $post->id }}/reorder-images`, {
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

    // MULTIPLE FILE UPLOAD MANAGER (ALBUM POSTS)
    function setupPostAlbumManager(visibleInputId, hiddenInputId, previewContainerId) {
        const visibleInput = document.getElementById(visibleInputId);
        const hiddenInput = document.getElementById(hiddenInputId);
        const previewContainer = document.getElementById(previewContainerId);
        let dt = new DataTransfer();
        let dropzone = null;
        let countDisplay = null;

        if (!visibleInput || !hiddenInput || !previewContainer) return;

        if (visibleInput.parentElement.id && visibleInput.parentElement.id.includes('Dropzone')) {
            dropzone = visibleInput.parentElement;
        }

        countDisplay = document.getElementById(visibleInputId.replace('input', 'Count'));

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
            visibleInput.value = ''; 
        });

        function handleFiles(files) {
            for(let i = 0; i < files.length; i++) {
                const file = files[i];
                const validImageTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                
                if (!validImageTypes.includes(file.type)) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: 'Format Tidak Sesuai', text: 'Hanya JPG, PNG, WEBP yang diizinkan.' });
                    } else {
                        alert('Format file tidak sesuai. Hanya JPG, PNG, WEBP yang diizinkan.');
                    }
                    continue;
                }

                if (file.size > 1024 * 1024) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: 'Ukuran File Terlalu Besar', text: 'Ukuran setiap foto album tidak boleh lebih dari 1 MB.' });
                    } else {
                        alert('Ukuran setiap foto album tidak boleh lebih dari 1 MB.');
                    }
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
            const emptyState = previewContainer.querySelector('.empty-state');
            if(emptyState) emptyState.style.display = 'none';

            const wrapper = document.createElement('div');
            wrapper.className = 'relative border border-gray-200 rounded-lg overflow-hidden shadow-sm aspect-square bg-gray-100 group transition hover:shadow-md';
            wrapper.dataset.index = index;
            wrapper.dataset.name = file.name;

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

    setupPostAlbumManager('album_images_input', 'album_images_hidden', 'albumImagesPreviewContainer');
</script>
@endpush
@endsection
