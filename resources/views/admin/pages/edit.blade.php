@extends('layouts.admin')

@section('title', 'Edit Konten Menu: ' . $page->title . ' - Admin Panel')
@section('page_title', 'CMS Editor Konten Menu: ' . $page->title)

@push('styles')
<style>
    /* Styling Editor CKEditor 5 SuperBuild */
    .ck-editor__editable_inline {
        min-height: 620px !important;
        max-height: 900px !important;
        font-size: 16px !important;
        font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        line-height: 1.15;
        padding: 2rem !important;
        color: #1e293b !important;
        background-color: #ffffff !important;
    }
    .ck-toolbar {
        background-color: #f8fafc !important;
        border-color: #e2e8f0 !important;
        border-radius: 0.75rem 0.75rem 0 0 !important;
        padding: 0.75rem !important;
        box-shadow: inset 0 -1px 0 #e2e8f0;
    }
    .ck.ck-editor__main>.ck-editor__editable:not(.ck-focused) {
        border-color: #cbd5e1 !important;
        border-radius: 0 0 0.75rem 0.75rem !important;
    }
    .ck.ck-editor__main>.ck-editor__editable.ck-focused {
        border-color: #2563eb !important;
        border-radius: 0 0 0.75rem 0.75rem !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
    }
    .ck-toolbar__items {
        flex-wrap: wrap !important;
    }
    
    /* Tab Styling */
    .nav-tab-btn {
        transition: all 0.2s ease-in-out;
        border-bottom: 3px solid transparent;
    }
    .nav-tab-btn.active {
        color: #2563eb;
        border-bottom-color: #2563eb;
        background-color: rgba(37, 99, 235, 0.04);
        font-weight: 800;
    }
    
    /* Drag and Drop Zone */
    .drag-drop-box {
        font-family: 'Nunito', sans-serif;
    }
    .drag-drop-box.dragover {
        border-color: #10b981;
        background-color: #ecfdf5;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
@endpush

@section('content')
<div class="space-y-6">

    <form action="{{ route('admin.pages.update', $page->id) }}" method="POST" enctype="multipart/form-data" id="editPageForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="status" id="status_input" value="{{ old('status', $page->status ?? 'publish') }}">

        <!-- Top Header & Action Bar -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 sticky top-4 z-30 backdrop-blur-md bg-white/95">
            <div class="flex items-center gap-3">
                
                <div>
                    <div class="flex items-center gap-2">
                        <span id="status_badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide {{ $page->status == 'publish' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                            {{ $page->status == 'publish' ? '🟢 Diterbitkan' : '🟠 Draf' }}
                        </span>
                        <span class="text-xs text-gray-400 font-mono">ID: #{{ $page->id }}</span>
                    </div>
                    <h2 class="text-lg font-extrabold text-gray-900 leading-tight truncate max-w-md mt-0.5">
                        {{ $page->title }}
                    </h2>
                </div>
            </div>

            <!-- Header Quick Action Buttons -->
            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.pages.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-semibold transition flex items-center justify-center gap-1 bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left"></i> Kembali</a>
                <a href="{{ url('/page/' . $page->slug) }}" target="_blank" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition flex items-center gap-2">
                    <i class="fas fa-external-link-alt text-gray-500"></i> Pratinjau Web
                </a>

                <!-- Save Draft Button -->
                <button type="button" onclick="submitFormWithStatus('draft')" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl text-xs transition shadow-sm flex items-center gap-1.5">
                    <i class="fas fa-save"></i> Simpan Draf
                </button>

                <!-- Update / Publish Button -->
                <button type="button" onclick="submitFormWithStatus('publish')" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition shadow-md flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> Update & Publikasikan
                </button>
            </div>
        </div>

        <!-- Tab Navigasi CMS Editor -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="border-b border-gray-100 bg-gray-50/50 px-4 flex overflow-x-auto gap-2">
                <button type="button" onclick="switchTab('tab-konten', this)" class="nav-tab-btn active px-5 py-4 text-xs font-bold text-gray-600 flex items-center gap-2 outline-none">
                    <i class="fas fa-file-alt text-base"></i> 1. Konten Utama & Editor
                </button>
                <button type="button" onclick="switchTab('tab-media', this)" class="nav-tab-btn px-5 py-4 text-xs font-bold text-gray-600 flex items-center gap-2 outline-none">
                    <i class="fas fa-photo-video text-base"></i> 2. Berkas & Media
                </button>
                <button type="button" onclick="switchTab('tab-seo', this)" class="nav-tab-btn px-5 py-4 text-xs font-bold text-gray-600 flex items-center gap-2 outline-none">
                    <i class="fas fa-search text-base"></i> 3. Optimasi SEO
                </button>
            </div>

            <div class="p-6 md:p-8">

                <!-- TAB 1: KONTEN UTAMA & EDITOR -->
                <div id="tab-konten" class="tab-pane space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Judul Halaman / Nama Artikel <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="page_title_input" value="{{ old('title', $page->title) }}" required oninput="updatePermalinkPreview(this.value)" placeholder="Masukkan Judul Halaman Lengkap di Sini..." class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-base font-extrabold text-gray-900 bg-white">
                        
                        <div class="mt-2 flex items-center gap-2 text-xs font-mono text-gray-500 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                            <i class="fas fa-link text-blue-500"></i>
                            <span>Permalink:</span>
                            <span id="permalink_preview" class="text-blue-700 font-bold">{{ url('/page/' . $page->slug) }}</span>
                        </div>
                    </div>

                    <!-- Area WYSIWYG Editor Superbuild -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider">
                                Editor Konten Profesional (CKEditor 5 Full Word Features)
                            </label>
                            <span class="text-[11px] font-bold text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">
                                <i class="fas fa-info-circle mr-1"></i> Supports Fullscreen, Tables, Images, PDF, YouTube & Code
                            </span>
                        </div>

                        <textarea name="content" id="ck-page-editor" class="w-full border border-gray-300 rounded-xl">{{ old('content', $page->content) }}</textarea>
                    </div>
                </div>

                <!-- TAB 2: BERKAS & MEDIA (DRAG & DROP) -->
                <div id="tab-media" class="tab-pane hidden space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Upload Foto Cover / Banner (Drag & Drop) -->
                        <div class="border border-gray-200 rounded-2xl p-5 bg-gray-50/50 space-y-4">
                            <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-image text-emerald-600"></i> Foto Cover / Banner Halaman
                            </h4>

                            <div class="drag-drop-box border-2 border-dashed border-gray-300 hover:border-emerald-500 rounded-xl p-6 text-center bg-white cursor-pointer" onclick="document.getElementById('image_file_input').click()" id="image_drop_zone">
                                <input type="file" name="image" id="image_file_input" accept="image/*" class="hidden" onchange="previewImageFile(this)">
                                <input type="hidden" name="cropped_image" id="croppedInput">
                                
                                <div id="image_preview_container" class="{{ $page->image ? '' : 'hidden' }} space-y-3 relative mx-auto w-full max-w-sm aspect-[16/9] overflow-hidden rounded-lg border border-gray-200">
                                    <img id="image_preview_img" src="{{ $page->image ? asset('storage/' . $page->image) : '' }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 hover:opacity-100 transition-opacity">
                                        <span class="text-white text-xs font-bold px-2 py-1 bg-black/70 rounded">Hasil Potongan Baru</span>
                                    </div>
                                </div>

                                <div id="image_placeholder" class="{{ $page->image ? 'hidden' : '' }} space-y-2">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-emerald-500 block"></i>
                                    <span class="text-xs font-bold text-gray-700 block">Klik atau Seret & Lepas Gambar ke Sini</span>
                                    <span class="text-[11px] text-gray-400 block">
                                        Format: JPG, PNG, WEBP (Maksimal 10MB)<br>
                                        <strong class="mt-1 block">Rekomendasi ukuran: 1200 x 675 px (Rasio 16:9). Gambar akan dipotong sesuai rasio 16:9.</strong>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Dokumen PDF / Word / Excel / Zip (Drag & Drop) -->
                        <div class="border border-gray-200 rounded-2xl p-5 bg-gray-50/50 space-y-4">
                            <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-file-pdf text-red-600"></i> Dokumen PDF / Berkas Lampiran
                            </h4>

                            <div class="drag-drop-box border-2 border-dashed border-gray-300 hover:border-red-500 rounded-xl p-6 text-center bg-white cursor-pointer" onclick="document.getElementById('pdf_file_input').click()" id="pdf_drop_zone">
                                <input type="file" name="pdf_file" id="pdf_file_input" accept=".pdf,.zip,.rar,.7z,.doc,.docx,.xls,.xlsx" class="hidden" onchange="previewPdfFile(this)">

                                <div id="pdf_preview_container" class="{{ $page->pdf_file ? '' : 'hidden' }} space-y-2">
                                    <i class="fas fa-file-pdf text-4xl text-red-600 block"></i>
                                    <span id="pdf_filename_label" class="text-xs font-bold text-gray-800 block truncate max-w-xs mx-auto">
                                        {{ $page->pdf_file ? basename($page->pdf_file) : '' }}
                                    </span>
                                    @if($page->pdf_file)
                                        <a href="{{ asset('storage/' . $page->pdf_file) }}" target="_blank" onclick="event.stopPropagation();" class="inline-block px-3 py-1 bg-red-100 text-red-700 hover:bg-red-200 font-bold rounded-lg text-xs">
                                            <i class="fas fa-eye mr-1"></i> Buka Dokumen
                                        </a>
                                    @endif
                                </div>

                                <div id="pdf_placeholder" class="{{ $page->pdf_file ? 'hidden' : '' }} space-y-2">
                                    <i class="fas fa-file-upload text-4xl text-red-500 block"></i>
                                    <span class="text-xs font-bold text-gray-700 block">Klik atau Seret & Lepas Berkas PDF ke Sini</span>
                                    <span class="text-[11px] text-gray-400 block">Format: PDF, Word, Excel, ZIP (Maksimal 50MB)</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Link Web Eksternal -->
                    <div class="border border-indigo-100 rounded-2xl p-5 bg-indigo-50/30 space-y-2">
                        <label class="block text-xs font-bold text-indigo-900 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-link text-indigo-600"></i> Tautan Web Eksternal (Opsional)
                        </label>
                        <input type="url" name="external_url" value="{{ old('external_url', $page->external_url) }}" placeholder="https://lapor.go.id atau https://probolinggokab.go.id" class="w-full px-4 py-2.5 border border-indigo-200 rounded-xl outline-none text-xs font-mono bg-white">
                        <p class="text-[11px] text-indigo-700 leading-tight">
                            *Jika diisi, pengunjung yang membuka menu ini akan <strong>otomatis dipindahkan ke URL website luar</strong> yang dituju.
                        </p>
                    </div>
                </div>

                <!-- TAB 3: OPTIMASI SEO -->
                <div id="tab-seo" class="tab-pane hidden space-y-6">
                    <div class="border border-gray-200 rounded-2xl p-6 bg-gray-50/50 space-y-5">
                        <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider flex items-center gap-2 border-b pb-3">
                            <i class="fas fa-search text-blue-600"></i> Pengaturan Mesin Pencari (Google SEO)
                        </h4>

                        <!-- Live Snippet Google Preview Card -->
                        <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-1 shadow-sm">
                            <span class="text-[11px] text-gray-400 block uppercase font-bold">Simulasi Tampilan Pencarian Google:</span>
                            <div class="text-xs text-emerald-700 font-mono truncate" id="google_url_preview">{{ url('/page/' . $page->slug) }}</div>
                            <div class="text-base font-bold text-blue-800 hover:underline cursor-pointer truncate" id="google_title_preview">
                                {{ $page->seo_title ?: $page->title }}
                            </div>
                            <div class="text-xs text-gray-600 line-clamp-2" id="google_desc_preview">
                                {{ $page->seo_description ?: 'Ringkasan halaman ini akan muncul pada hasil pencarian Google.' }}
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-xs font-bold text-gray-700">SEO Title (Judul Pencarian)</label>
                                    <span class="text-[11px] text-gray-400 font-mono" id="seo_title_count">0 / 60 Karakter</span>
                                </div>
                                <input type="text" name="seo_title" id="seo_title_input" value="{{ old('seo_title', $page->seo_title) }}" oninput="updateSeoPreview()" placeholder="Judul khusus untuk Google..." class="w-full px-4 py-2.5 border border-gray-300 rounded-xl outline-none text-xs bg-white font-semibold">
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-xs font-bold text-gray-700">SEO Meta Description (Deskripsi Singkat)</label>
                                    <span class="text-[11px] text-gray-400 font-mono" id="seo_desc_count">0 / 160 Karakter</span>
                                </div>
                                <textarea name="seo_description" id="seo_desc_input" rows="3" oninput="updateSeoPreview()" placeholder="Ringkasan halaman untuk mesin pencari..." class="w-full px-4 py-2.5 border border-gray-300 rounded-xl outline-none text-xs bg-white">{{ old('seo_description', $page->seo_description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<!-- Modal Cropper -->
<div id="cropModal" class="fixed inset-0 bg-black/80 z-[9999] hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl flex flex-col overflow-hidden max-h-[90vh]">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800"><i class="fas fa-crop text-emerald-600 mr-2"></i> Sesuaikan Ukuran Gambar (Rasio 16:9)</h3>
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
                <button type="button" id="btnSaveCrop" class="px-5 py-2 bg-emerald-600 text-white font-bold rounded hover:bg-emerald-700 transition shadow text-sm">
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
    // Tab Navigation Logic
    function switchTab(tabId, btn) {
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.nav-tab-btn').forEach(el => el.classList.remove('active'));

        document.getElementById(tabId).classList.remove('hidden');
        btn.classList.add('active');
    }

    // Submit with explicit status
    function submitFormWithStatus(status) {
        document.getElementById('status_input').value = status;
        document.getElementById('editPageForm').submit();
    }

    // Update Status Badge UI
    function updateStatusBadge(status) {
        const badge = document.getElementById('status_badge');
        if (status === 'publish') {
            badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide bg-green-100 text-green-800 border border-green-200';
            badge.innerText = '🟢 Diterbitkan';
        } else {
            badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide bg-amber-100 text-amber-800 border border-amber-200';
            badge.innerText = '🟠 Draf';
        }
    }

    // Toggle Custom Menu Group Input
    function toggleCustomMenuGroup(select) {
        const wrapper = document.getElementById('custom_group_wrapper');
        if (select.value === 'CUSTOM') {
            wrapper.classList.remove('hidden');
        } else {
            wrapper.classList.add('hidden');
        }
    }

    // Permalink & Google SEO Live Preview
    function updatePermalinkPreview(title) {
        const slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        const url = '{{ url("/page") }}/' + (slug || '{{ $page->slug }}');
        document.getElementById('permalink_preview').innerText = url;
        document.getElementById('google_url_preview').innerText = url;
        
        if (!document.getElementById('seo_title_input').value) {
            document.getElementById('google_title_preview').innerText = title || '{{ $page->title }}';
        }
    }

    function updateSeoPreview() {
        const titleInput = document.getElementById('seo_title_input');
        const descInput = document.getElementById('seo_desc_input');

        document.getElementById('seo_title_count').innerText = titleInput.value.length + ' / 60 Karakter';
        document.getElementById('seo_desc_count').innerText = descInput.value.length + ' / 160 Karakter';

        document.getElementById('google_title_preview').innerText = titleInput.value || document.getElementById('page_title_input').value;
        document.getElementById('google_desc_preview').innerText = descInput.value || 'Ringkasan halaman ini akan muncul pada hasil pencarian Google.';
    }

    let cropper;
    const cropModal = document.getElementById('cropModal');
    const cropImage = document.getElementById('cropImage');
    const croppedInput = document.getElementById('croppedInput');
    const closeCropModal = document.getElementById('closeCropModal');
    const btnSaveCrop = document.getElementById('btnSaveCrop');
    const btnResetCrop = document.getElementById('btnResetCrop');
    const imageInput = document.getElementById('image_file_input');
    
    // Image File Drag & Drop + Live Preview + Cropper
    function previewImageFile(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                cropImage.src = e.target.result;
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
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    if(closeCropModal) {
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
                width: 1200,
                height: 675,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
                fillColor: '#fff'
            });
            
            const croppedData = canvas.toDataURL('image/jpeg', 0.9);
            croppedInput.value = croppedData;
            
            document.getElementById('image_preview_img').src = croppedData;
            document.getElementById('image_preview_container').classList.remove('hidden');
            document.getElementById('image_placeholder').classList.add('hidden');
            
            imageInput.value = '';
            cropModal.classList.add('hidden');
            cropModal.classList.remove('flex');
        });
    }

    // PDF File Drag & Drop + Live Preview Label
    function previewPdfFile(input) {
        if (input.files && input.files[0]) {
            document.getElementById('pdf_filename_label').innerText = input.files[0].name;
            document.getElementById('pdf_preview_container').classList.remove('hidden');
            document.getElementById('pdf_placeholder').classList.add('hidden');
        }
    }

    // Setup Drag & Drop Listeners
    document.addEventListener('DOMContentLoaded', function () {
        createCkEditor('#ck-page-editor', 'Tulis konten artikel atau halaman di sini... (Mendukung Microsoft Word, Upload Gambar, PDF, Word, Excel, ZIP, Embed YouTube, dan Fullscreen)');

        updateSeoPreview();

        // Drag & Drop Setup
        ['image_drop_zone', 'pdf_drop_zone'].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;

            ['dragenter', 'dragover'].forEach(eventName => {
                el.addEventListener(eventName, (e) => { e.preventDefault(); el.classList.add('dragover'); }, false);
            });
            ['dragleave', 'drop'].forEach(eventName => {
                el.addEventListener(eventName, (e) => { e.preventDefault(); el.classList.remove('dragover'); }, false);
            });
        });
    });
</script>
@endpush
@endsection
