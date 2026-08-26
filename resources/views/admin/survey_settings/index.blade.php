@extends('layouts.admin')

@section('title', 'Manajemen Link Survey / QR SKM')

@section('content')
<div class="p-6">
    <!-- Header Page -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Link Survey / QR SKM</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola tautan dan QR Code Survey Kepuasan Masyarakat (SKM) di Footer Website</p>
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

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.survey-settings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Pengaturan Survey / QR SKM -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Teks Survey / SKM di Footer</label>
                    <input type="text" name="survey_title" value="{{ old('survey_title', $settings['survey_title'] ?? '') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-brand-blue outline-none transition" placeholder="Contoh: Survey Kepuasan Masyarakat (SKM)">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Link/URL Survey SKM</label>
                    <input type="text" name="survey_link" value="{{ old('survey_link', $settings['survey_link'] ?? '') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-brand-blue outline-none transition" placeholder="Contoh: https://forms.gle/xyz...">
                    <p class="text-xs text-gray-500 mt-1">Arahkan pengunjung ke kuesioner online ketika QR / link diklik.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Gambar QR Code SKM</label>
                    <div class="flex items-start space-x-6">
                        <!-- Preview -->
                        <div class="w-32 h-32 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden bg-gray-50 relative group">
                            @if(isset($settings['survey_qr_image']) && $settings['survey_qr_image'] !== '')
                                <img src="{{ asset('storage/' . $settings['survey_qr_image']) }}" alt="QR Code Default" class="max-h-full max-w-full object-contain">
                            @else
                                <i class="fas fa-qrcode text-3xl text-gray-400"></i>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <input type="file" name="survey_qr_image" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                            <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG, WebP (Rekomendasi ukuran: 300x300 pixel proporsional).</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 pt-5 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-[#729b48] hover:bg-[#5f8439] text-white font-semibold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
