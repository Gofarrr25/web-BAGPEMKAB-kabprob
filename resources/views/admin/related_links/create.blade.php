@extends('layouts.admin')

@section('title', 'Tambah Link Terkait - Admin Panel')
@section('page_title', 'Tambah Link Terkait Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800"><i class="fas fa-plus text-teal-600 mr-2"></i> Form Tambah Link</h3>
            <a href="{{ route('admin.related-links.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-semibold transition flex items-center justify-center gap-1 bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left"></i> Kembali</a>
            
        </div>
        
        <form action="{{ route('admin.related-links.store') }}" method="POST" enctype="multipart/form-data" class="p-4 md:p-6">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Website / Instansi <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="nameInput" value="{{ old('name') }}" required placeholder="Misal: SP4N LAPOR"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none transition">
                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">URL Website <span class="text-red-500">*</span></label>
                <input type="url" name="url" id="urlInput" value="{{ old('url') }}" required placeholder="https://..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none transition font-mono">
                @error('url') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Preview Area -->
            <div class="mb-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-bold text-gray-700 mb-2">Pratinjau Logo <span class="text-xs font-normal text-brand-blue bg-blue-50 px-2 py-0.5 rounded border border-brand-blue-pale ml-2">Tampilan Aktual: 200 &times; 70 px</span></label>
                <div class="flex items-center gap-4">
                    <div class="w-[200px] h-[70px] bg-white border border-dashed border-gray-300 rounded flex items-center justify-center overflow-hidden shrink-0 p-1">
                        <img src="" id="logoPreview" class="w-full h-full object-contain hidden" alt="Logo Preview">
                        <i class="fas fa-image text-gray-300 text-2xl" id="logoPlaceholder"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-600" id="logoStatusText">Sistem akan otomatis mengambil logo saat Anda mengetik URL Website.</p>
                    </div>
                </div>
            </div>

            <input type="hidden" name="logo_url" id="hiddenLogoUrl" value="{{ old('logo_url') }}">

            <!-- Manual Fallback Area (Hidden by default) -->
            <div id="manualUploadArea" class="mb-6 hidden p-4 border border-gray-200 bg-gray-50 rounded-lg">
                <label class="block text-sm font-bold text-gray-700 mb-1">Upload Logo Manual <span class="text-xs text-gray-500 font-normal">(Opsional, akan menimpa logo otomatis)</span></label>
                <p class="text-xs text-amber-600 font-semibold mb-3"><i class="fas fa-info-circle"></i> Rekomendasi 400 &times; 140 px (rasio 20:7)</p>
                <div class="flex flex-col gap-2">
                    <input type="file" name="logo_file" id="logoFileInput" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-yellow-100 file:text-yellow-700 hover:file:bg-yellow-200 cursor-pointer">
                    
                    <!-- Cropper Container -->
                    <div id="cropperContainer" class="hidden mt-3 p-3 border border-gray-300 rounded bg-white">
                        <p class="text-xs font-bold text-gray-700 mb-2">Sesuaikan Area Logo</p>
                        <div class="w-full max-w-md mx-auto mb-3 bg-gray-100 overflow-hidden" style="max-height: 400px;">
                            <img id="cropperImage" class="max-w-full block" src="">
                        </div>
                        <div class="flex justify-center gap-2 mb-3">
                            <button type="button" id="btnZoomIn" class="px-3 py-1.5 bg-gray-200 text-gray-800 font-semibold rounded shadow-sm hover:bg-gray-300 text-xs"><i class="fas fa-search-plus"></i> Zoom In</button>
                            <button type="button" id="btnZoomOut" class="px-3 py-1.5 bg-gray-200 text-gray-800 font-semibold rounded shadow-sm hover:bg-gray-300 text-xs"><i class="fas fa-search-minus"></i> Zoom Out</button>
                        </div>
                        <div class="flex justify-end gap-2 border-t pt-3">
                            <button type="button" id="btnCancelCrop" class="px-4 py-2 bg-gray-500 text-white font-bold rounded shadow-sm hover:bg-gray-600 text-sm">Batal</button>
                            <button type="button" id="btnApplyCrop" class="px-4 py-2 bg-teal-600 text-white font-bold rounded shadow-sm hover:bg-teal-700 text-sm">Terapkan Logo</button>
                        </div>
                    </div>
                    
                    <input type="hidden" name="logo_base64" id="logoBase64Input">
                    
                    <span class="text-xs text-gray-500 text-center font-bold my-1">ATAU</span>
                    <input type="url" name="logo_url_manual" id="logoUrlManualInput" placeholder="URL Logo Alternatif (misal: https://...)" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none transition font-mono text-sm">
                </div>
            </div>

            @push('scripts')
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
            <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const urlInput = document.getElementById('urlInput');
                    const logoPreview = document.getElementById('logoPreview');
                    const logoPlaceholder = document.getElementById('logoPlaceholder');
                    const logoStatusText = document.getElementById('logoStatusText');
                    const hiddenLogoUrl = document.getElementById('hiddenLogoUrl');
                    const manualUploadArea = document.getElementById('manualUploadArea');
                    const logoUrlManualInput = document.getElementById('logoUrlManualInput');
                    const logoFileInput = document.getElementById('logoFileInput');
                    const cropperContainer = document.getElementById('cropperContainer');
                    const cropperImage = document.getElementById('cropperImage');
                    const logoBase64Input = document.getElementById('logoBase64Input');
                    
                    let timeout = null;
                    let cropper = null;

                    logoFileInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(event) {
                                cropperImage.src = event.target.result;
                                cropperContainer.classList.remove('hidden');
                                
                                if (cropper) {
                                    cropper.destroy();
                                }
                                
                                cropper = new Cropper(cropperImage, {
                                    aspectRatio: 20 / 7,
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

                    document.getElementById('btnZoomIn').addEventListener('click', function() {
                        if (cropper) cropper.zoom(0.1);
                    });
                    
                    document.getElementById('btnZoomOut').addEventListener('click', function() {
                        if (cropper) cropper.zoom(-0.1);
                    });
                    
                    document.getElementById('btnCancelCrop').addEventListener('click', function() {
                        cropperContainer.classList.add('hidden');
                        logoFileInput.value = '';
                        logoBase64Input.value = '';
                        if (cropper) {
                            cropper.destroy();
                            cropper = null;
                        }
                        if(hiddenLogoUrl.value) {
                            logoPreview.src = hiddenLogoUrl.value;
                            logoPreview.classList.remove('hidden');
                            logoPlaceholder.classList.add('hidden');
                            logoStatusText.innerHTML = '<span class="text-green-600 font-bold"><i class="fas fa-check-circle mr-1"></i> Logo otomatis aktif</span>';
                        } else {
                            resetPreview();
                        }
                    });
                    
                    document.getElementById('btnApplyCrop').addEventListener('click', function() {
                        if (cropper) {
                            const canvas = cropper.getCroppedCanvas({
                                width: 400,
                                height: 140,
                                fillColor: 'transparent',
                                imageSmoothingEnabled: true,
                                imageSmoothingQuality: 'high',
                            });
                            
                            const base64data = canvas.toDataURL('image/png');
                            logoBase64Input.value = base64data;
                            
                            logoPreview.src = base64data;
                            logoPreview.classList.remove('hidden');
                            logoPlaceholder.classList.add('hidden');
                            logoStatusText.innerHTML = '<span class="text-brand-blue font-bold"><i class="fas fa-crop-alt mr-1"></i> Hasil Crop Logo diterapkan</span>';
                            
                            cropperContainer.classList.add('hidden');
                        }
                    });

                    urlInput.addEventListener('input', function() {
                        clearTimeout(timeout);
                        timeout = setTimeout(checkAndFetchLogo, 800);
                    });

                    function checkAndFetchLogo() {
                        const url = urlInput.value.trim();
                        if (!url || !url.startsWith('http')) {
                            resetPreview();
                            return;
                        }

                        try {
                            // Coba hapus www. untuk probabilitas logo yang lebih baik di clearbit
                            let domain = new URL(url).hostname;
                            domain = domain.replace('www.', '');
                            const clearbitUrl = `https://logo.clearbit.com/${domain}`;
                            
                            logoStatusText.innerHTML = '<span class="text-teal-600"><i class="fas fa-spinner fa-spin mr-1"></i> Mencari logo otomatis...</span>';
                            
                            // Try loading image
                            const img = new Image();
                            img.onload = function() {
                                // Success!
                                logoPreview.src = clearbitUrl;
                                logoPreview.classList.remove('hidden');
                                logoPlaceholder.classList.add('hidden');
                                logoStatusText.innerHTML = '<span class="text-green-600 font-bold"><i class="fas fa-check-circle mr-1"></i> Logo berhasil didapatkan!</span>';
                                hiddenLogoUrl.value = clearbitUrl;
                                manualUploadArea.classList.add('hidden');
                                logoUrlManualInput.name = 'ignore_manual'; // Avoid overwriting if they typed something before
                            };
                            img.onerror = function() {
                                // Failed!
                                logoPreview.classList.add('hidden');
                                logoPlaceholder.classList.remove('hidden');
                                logoStatusText.innerHTML = '<span class="text-red-600 font-bold"><i class="fas fa-exclamation-triangle mr-1"></i> Logo tidak dapat diambil otomatis. Silakan upload logo secara manual.</span>';
                                hiddenLogoUrl.value = '';
                                manualUploadArea.classList.remove('hidden');
                                logoUrlManualInput.name = 'logo_url'; // Enable manual URL field
                            };
                            img.src = clearbitUrl;
                        } catch (e) {
                            resetPreview();
                        }
                    }

                    function resetPreview() {
                        logoPreview.src = '';
                        logoPreview.classList.add('hidden');
                        logoPlaceholder.classList.remove('hidden');
                        logoStatusText.innerHTML = 'Sistem akan otomatis mengambil logo saat Anda mengetik URL Website.';
                        hiddenLogoUrl.value = '';
                        manualUploadArea.classList.add('hidden');
                    }
                    
                    // Trigger on load if there's old value
                    if(urlInput.value) {
                        checkAndFetchLogo();
                    }
                });
            </script>
            @endpush

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Urutan Tampil (Order)</label>
                    <input type="number" name="order" value="{{ old('order', 0) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status Visibilitas</label>
                    <label class="flex items-center cursor-pointer mt-2">
                        <div class="relative">
                            <input type="checkbox" name="is_active" class="sr-only" value="1" checked>
                            <div class="block bg-gray-200 w-10 h-6 rounded-full transition bg-toggle"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform translate-x-full bg-teal-500 border-teal-500"></div>
                        </div>
                        <div class="ml-3 text-sm font-bold text-gray-700">Aktifkan Link</div>
                    </label>
                </div>
            </div>

            <style>
                input:checked ~ .block { background-color: #0d9488; }
                input:checked ~ .dot { transform: translateX(100%); background-color: white; }
            </style>

            <div class="border-t border-gray-100 pt-5 mt-2">
                <button type="submit" class="w-full py-2.5 bg-teal-600 text-white font-bold rounded-lg hover:bg-teal-700 transition shadow flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Simpan Link Terkait
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
