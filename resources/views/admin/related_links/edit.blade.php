@extends('layouts.admin')

@section('title', 'Edit Link Terkait - Admin Panel')
@section('page_title', 'Edit Link Terkait')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800"><i class="fas fa-edit text-yellow-500 mr-2"></i> Form Edit Link</h3>
            <a href="{{ route('admin.settings.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-semibold transition flex items-center justify-center gap-1 bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left"></i> Kembali</a>
            
        </div>
        
        <form action="{{ route('admin.related-links.update', $related_link->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Website / Instansi <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $related_link->name) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 outline-none transition">
                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">URL Website <span class="text-red-500">*</span></label>
                <input type="url" name="url" value="{{ old('url', $related_link->url) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 outline-none transition font-mono">
                @error('url') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Preview Area -->
            <div class="mb-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-bold text-gray-700 mb-2">Pratinjau Logo</label>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white border border-gray-300 rounded flex items-center justify-center overflow-hidden shrink-0">
                        <img src="{{ $related_link->logo_url ?? '' }}" id="logoPreview" class="max-w-full max-h-full object-contain {{ $related_link->logo_url ? '' : 'hidden' }}" alt="Logo Preview">
                        <i class="fas fa-image text-gray-300 text-2xl {{ $related_link->logo_url ? 'hidden' : '' }}" id="logoPlaceholder"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-600" id="logoStatusText">
                            @if($related_link->logo_url)
                                Logo saat ini. Ubah URL Website untuk mencari logo baru secara otomatis.
                            @else
                                Sistem akan otomatis mengambil logo saat Anda mengetik URL Website.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <input type="hidden" name="logo_url" id="hiddenLogoUrl" value="{{ old('logo_url', $related_link->logo_url) }}">

            <!-- Manual Upload Area -->
            <div id="manualUploadArea" class="mb-6 p-4 border border-gray-200 bg-gray-50 rounded-lg">
                <label class="block text-sm font-bold text-gray-700 mb-2">Upload Logo Manual <span class="text-xs text-gray-500 font-normal">(Opsional, akan menimpa logo otomatis)</span></label>
                <div class="flex flex-col gap-2">
                    <input type="file" name="logo_file" id="logoFileInput" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100 cursor-pointer">
                    <span class="text-xs text-gray-500 text-center font-bold my-1">ATAU MASUKAN URL LOGO</span>
                    <input type="url" name="logo_url_manual" id="logoUrlManualInput" placeholder="URL Logo Alternatif (misal: https://...)" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 outline-none transition font-mono text-sm">
                </div>
            </div>

            @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const urlInput = document.querySelector('input[name="url"]');
                    const logoPreview = document.getElementById('logoPreview');
                    const logoPlaceholder = document.getElementById('logoPlaceholder');
                    const logoStatusText = document.getElementById('logoStatusText');
                    const hiddenLogoUrl = document.getElementById('hiddenLogoUrl');
                    const manualUploadArea = document.getElementById('manualUploadArea');
                    const logoUrlManualInput = document.getElementById('logoUrlManualInput');
                    const logoFileInput = document.getElementById('logoFileInput');
                    
                    let timeout = null;
                    let initialUrl = urlInput.value.trim();
                    let initialLogoUrl = hiddenLogoUrl.value;

                    // Handle File Input Change for Live Preview
                    logoFileInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(event) {
                                logoPreview.src = event.target.result;
                                logoPreview.classList.remove('hidden');
                                logoPlaceholder.classList.add('hidden');
                                logoStatusText.innerHTML = '<span class="text-blue-600 font-bold"><i class="fas fa-image mr-1"></i> Preview dari file yang diupload</span>';
                            };
                            reader.readAsDataURL(file);
                        } else {
                            // If user cancels file selection, revert to initial/auto logo
                            if (hiddenLogoUrl.value) {
                                logoPreview.src = hiddenLogoUrl.value;
                                logoStatusText.innerHTML = '<span class="text-green-600 font-bold"><i class="fas fa-check-circle mr-1"></i> Logo otomatis/sebelumnya aktif</span>';
                            } else {
                                logoPreview.classList.add('hidden');
                                logoPlaceholder.classList.remove('hidden');
                                logoStatusText.innerHTML = 'Silakan isi URL atau upload file.';
                            }
                        }
                    });

                    urlInput.addEventListener('input', function() {
                        // Jika URL sama dengan URL awal, biarkan logo yang sudah ada
                        if (urlInput.value.trim() === initialUrl && initialLogoUrl !== '') {
                            return;
                        }
                        // Jangan timpa otomatis jika user sudah upload file
                        if (logoFileInput.files.length > 0) {
                            return;
                        }
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
                            let domain = new URL(url).hostname;
                            domain = domain.replace('www.', '');
                            const clearbitUrl = `https://logo.clearbit.com/${domain}`;
                            
                            logoStatusText.innerHTML = '<span class="text-yellow-600"><i class="fas fa-spinner fa-spin mr-1"></i> Mencari logo otomatis...</span>';
                            
                            const img = new Image();
                            img.onload = function() {
                                logoPreview.src = clearbitUrl;
                                logoPreview.classList.remove('hidden');
                                logoPlaceholder.classList.add('hidden');
                                logoStatusText.innerHTML = '<span class="text-green-600 font-bold"><i class="fas fa-check-circle mr-1"></i> Logo berhasil didapatkan!</span>';
                                hiddenLogoUrl.value = clearbitUrl;
                            };
                            img.onerror = function() {
                                logoPreview.src = initialLogoUrl;
                                if (!initialLogoUrl) {
                                    logoPreview.classList.add('hidden');
                                    logoPlaceholder.classList.remove('hidden');
                                }
                                logoStatusText.innerHTML = '<span class="text-red-600 font-bold"><i class="fas fa-exclamation-triangle mr-1"></i> Logo tidak dapat diambil otomatis. Silakan upload logo secara manual.</span>';
                                hiddenLogoUrl.value = initialLogoUrl; 
                            };
                            img.src = clearbitUrl;
                        } catch (e) {
                            resetPreview();
                        }
                    }

                    function resetPreview() {
                        if(logoFileInput.files.length === 0) {
                            logoPreview.src = '';
                            logoPreview.classList.add('hidden');
                            logoPlaceholder.classList.remove('hidden');
                            logoStatusText.innerHTML = 'Sistem akan otomatis mengambil logo saat Anda mengetik URL Website.';
                            hiddenLogoUrl.value = initialLogoUrl;
                        }
                    }
                });
            </script>
            @endpush

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Urutan Tampil (Order)</label>
                    <input type="number" name="order" value="{{ old('order', $related_link->order) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status Visibilitas</label>
                    <label class="flex items-center cursor-pointer mt-2">
                        <div class="relative">
                            <input type="checkbox" name="is_active" class="sr-only" value="1" {{ old('is_active', $related_link->is_active) ? 'checked' : '' }}>
                            <div class="block bg-gray-200 w-10 h-6 rounded-full transition bg-toggle"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform translate-x-full bg-yellow-500 border-yellow-500"></div>
                        </div>
                        <div class="ml-3 text-sm font-bold text-gray-700">Aktifkan Link</div>
                    </label>
                </div>
            </div>

            <style>
                input:checked ~ .block { background-color: #eab308; }
                input:checked ~ .dot { transform: translateX(100%); background-color: white; }
            </style>

            <div class="border-t border-gray-100 pt-5 mt-2">
                <button type="submit" class="w-full py-2.5 bg-yellow-500 text-white font-bold rounded-lg hover:bg-yellow-600 transition shadow flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Perbarui Link Terkait
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
