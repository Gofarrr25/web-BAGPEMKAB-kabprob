@extends('layouts.admin')

@section('title', 'Manajemen Tautan Eksternal')

@section('content')
<div class="p-4 md:p-6">
    <!-- Header Page -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tautan Eksternal</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola Tautan Eksternal tambahan di Footer Website (Sebelah Link Survey)</p>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700 font-medium">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Alert Errors -->
    @if ($errors->any())
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500"></i>
            </div>
            <div class="ml-3">
                <ul class="text-sm text-red-700 font-medium list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 md:p-6">
        <form action="{{ route('admin.external-links.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Tautan Eksternal</label>
                    <input type="text" name="external_link_title" value="{{ old('external_link_title', $settings['external_link_title'] ?? '') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-brand-blue outline-none transition" placeholder="Contoh: Aplikasi Lapor">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Link/URL Tautan Eksternal</label>
                    <input type="text" name="external_link_url" value="{{ old('external_link_url', $settings['external_link_url'] ?? '') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-brand-blue outline-none transition" placeholder="Contoh: https://lapor.go.id">
                    <p class="text-xs text-gray-500 mt-1">Arahkan pengunjung ke tautan ini ketika QR / link diklik.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Gambar QR Code Tautan Eksternal</label>
                    <div class="flex items-start space-x-6">
                        <!-- Preview -->
                        <div id="externalQrPreviewContainer" class="w-32 h-32 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden bg-gray-50 relative group">
                            @if(isset($settings['external_link_qr_image']) && $settings['external_link_qr_image'] !== '')
                                <img id="externalQrPreviewImage" src="{{ asset('storage/' . $settings['external_link_qr_image']) }}" alt="QR Code Default" class="max-h-full max-w-full object-contain">
                            @else
                                <img id="externalQrPreviewImage" src="" alt="QR Code Preview" class="max-h-full max-w-full object-contain hidden">
                                <i id="externalQrPreviewIcon" class="fas fa-qrcode text-3xl text-gray-400"></i>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <input type="file" id="externalQrFileInput" name="external_link_qr_image" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-blue-light file:text-brand-blue hover:file:bg-brand-blue-light cursor-pointer">
                            <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG, WebP (Rekomendasi ukuran: 300x300 pixel proporsional).</p>
                        </div>
                    </div>
                </div>


            </div>

            <!-- Submit Button -->
            <div class="mt-8 pt-5 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-brand-blue hover:bg-brand-blue-hover text-white font-semibold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('externalQrFileInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('externalQrPreviewImage');
                const icon = document.getElementById('externalQrPreviewIcon');
                
                img.src = e.target.result;
                img.classList.remove('hidden');
                
                if (icon) {
                    icon.classList.add('hidden');
                }
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
