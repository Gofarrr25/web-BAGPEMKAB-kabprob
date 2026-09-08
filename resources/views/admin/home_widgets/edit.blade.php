@extends('layouts.admin')

@section('page_title', 'Edit Widget Home')

@section('content')
<div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-100 max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
        <h2 class="text-xl font-bold text-gray-800">Edit Widget: {{ $home_widget->title }}</h2>
        <a href="{{ route('admin.home-widgets.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-semibold transition flex items-center justify-center gap-1 bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded text-sm text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.home-widgets.update', $home_widget->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Judul Widget <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $home_widget->title) }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-brand-blue outline-none transition">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Gambar / Foto (Opsional)</label>
            <input type="file" name="image" id="imageInput" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-brand-blue outline-none transition">
            <p class="text-xs text-gray-500 mt-1">
                Kosongkan jika tidak ingin mengganti gambar. Disarankan format JPG, PNG.<br>
                <strong>Rekomendasi ukuran: 600 x 400 px (Rasio 3:2) atau 600 x 600 px (Persegi 1:1). Gambar akan dipotong sesuai rasio 3:2.</strong>
            </p>
            <input type="hidden" name="cropped_image" id="croppedInput">
            
            <div id="imagePreviewContainer" class="mt-3 {{ $home_widget->image_path ? '' : 'hidden' }} relative w-64 aspect-[3/2] border border-gray-200 rounded overflow-hidden">
                <img id="imagePreview" src="{{ $home_widget->image_path ? asset('storage/' . $home_widget->image_path) : '#' }}" alt="Preview" class="w-full h-full object-cover">
                <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 hover:opacity-100 transition-opacity">
                    <span class="text-white text-xs font-bold px-2 py-1 bg-black/70 rounded">Hasil Potongan Baru</span>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Link / URL (Opsional)</label>
            <input type="text" name="link_url" value="{{ old('link_url', $home_widget->link_url) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-brand-blue outline-none transition" placeholder="Contoh: https://lapor.go.id atau /halaman-internal">
            <p class="text-xs text-gray-500 mt-1">Isi jika widget ini perlu diarahkan ke halaman lain saat di-klik.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Urutan Tampil (Order)</label>
                <input type="number" name="order_index" value="{{ old('order_index', $home_widget->order_index) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-brand-blue outline-none transition">
                <p class="text-xs text-gray-500 mt-1">Angka lebih kecil akan tampil lebih atas (contoh: 1, 2, 3).</p>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Status Visibilitas</label>
                <div class="flex items-center gap-2 mt-3">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $home_widget->is_active) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Aktif (Tampilkan di Home)</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-brand-blue text-white font-bold rounded-lg shadow-md hover:bg-brand-dark transition flex items-center gap-2">
                <i class="fas fa-save"></i> Perbarui Widget
            </button>
        </div>
</form>
</div>

<!-- Modal Cropper -->
<div id="cropModal" class="fixed inset-0 bg-black/80 z-[9999] hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl flex flex-col overflow-hidden max-h-[90vh]">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800"><i class="fas fa-crop text-brand-blue mr-2"></i> Sesuaikan Ukuran Gambar (Rasio 3:2)</h3>
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
                <i class="fas fa-info-circle mr-1"></i> Geser, zoom, dan sesuaikan kotak crop. Rasio sudah dikunci 3:2.
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
    const previewContainer = document.getElementById('imagePreviewContainer');
    const previewImage = document.getElementById('imagePreview');
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
                        aspectRatio: 3 / 2,
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
                width: 600,
                height: 400,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
                fillColor: '#fff'
            });
            
            const croppedData = canvas.toDataURL('image/jpeg', 0.9);
            croppedInput.value = croppedData;
            
            previewImage.src = croppedData;
            previewContainer.classList.remove('hidden');
            
            imageInput.value = '';
            cropModal.classList.add('hidden');
            cropModal.classList.remove('flex');
        });
    }
</script>
@endpush
@endsection
