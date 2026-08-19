@extends('layouts.admin')

@section('title', 'Tambah Link Terkait - Admin Panel')
@section('page_title', 'Tambah Link Terkait Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800"><i class="fas fa-plus text-teal-600 mr-2"></i> Form Tambah Link</h3>
            <a href="{{ route('admin.settings.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-semibold transition flex items-center justify-center gap-1 bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left"></i> Kembali</a>
            
        </div>
        
        <form action="{{ route('admin.related-links.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
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
                <label class="block text-sm font-bold text-gray-700 mb-2">Pratinjau Logo</label>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white border border-gray-300 rounded flex items-center justify-center overflow-hidden shrink-0">
                        <img src="" id="logoPreview" class="max-w-full max-h-full object-contain hidden" alt="Logo Preview">
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
                <label class="block text-sm font-bold text-gray-700 mb-2">Upload Logo Manual <span class="text-xs text-gray-500 font-normal">(Fallback jika otomatis gagal)</span></label>
                <div class="flex flex-col gap-2">
                    <input type="file" name="logo_file" id="logoFileInput" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
                    <span class="text-xs text-gray-500 text-center font-bold my-1">ATAU</span>
                    <input type="url" name="logo_url_manual" id="logoUrlManualInput" placeholder="URL Logo Alternatif (misal: https://...)" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none transition font-mono text-sm">
                </div>
            </div>

            @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const urlInput = document.getElementById('urlInput');
                    const logoPreview = document.getElementById('logoPreview');
                    const logoPlaceholder = document.getElementById('logoPlaceholder');
                    const logoStatusText = document.getElementById('logoStatusText');
                    const hiddenLogoUrl = document.getElementById('hiddenLogoUrl');
                    const manualUploadArea = document.getElementById('manualUploadArea');
                    const logoUrlManualInput = document.getElementById('logoUrlManualInput');
                    
                    let timeout = null;

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
