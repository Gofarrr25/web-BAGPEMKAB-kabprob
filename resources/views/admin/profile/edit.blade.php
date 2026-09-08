@extends('layouts.admin')

@section('title', 'Profil Saya - Admin Panel')
@section('page_title', 'Profil Saya')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<style>
    #cropperImage {
        display: block;
        max-width: 100%;
    }
</style>
@endpush

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 max-w-3xl">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Kelola Profil Saya</h3>
        <div class="flex items-center gap-2">
            <span class="bg-brand-blue-light text-brand-blue text-xs px-3 py-1.5 rounded-full font-bold">Profil</span>
            <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700 text-sm font-semibold transition flex items-center justify-center gap-1 bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </div>
    
    <div class="p-4 md:p-6">
        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="flex flex-col md:flex-row gap-6 mb-6">
                <!-- Foto Profil Section -->
                <div class="flex flex-col items-center gap-3">
                    <label class="block text-sm font-bold text-gray-700 w-full text-center">Foto Profil</label>
                    <div class="relative w-32 h-32 rounded-full border-4 border-gray-100 bg-gray-50 flex items-center justify-center overflow-hidden shadow-sm group">
                        @if($user->profile_photo_path)
                            <img id="photo-preview" src="{{ asset('storage/' . $user->profile_photo_path) }}" class="w-full h-full object-cover">
                            <div id="photo-placeholder" class="text-gray-400 flex-col items-center hidden">
                                <i class="fas fa-camera text-2xl mb-1"></i>
                                <span class="text-[10px] uppercase font-bold tracking-wider">Upload</span>
                            </div>
                        @else
                            <img id="photo-preview" src="" class="w-full h-full object-cover hidden">
                            <div id="photo-placeholder" class="text-gray-400 flex flex-col items-center">
                                <i class="fas fa-camera text-2xl mb-1"></i>
                                <span class="text-[10px] uppercase font-bold tracking-wider">Upload</span>
                            </div>
                        @endif
                        
                        <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity" onclick="document.getElementById('profile_photo').click()">
                            <i class="fas fa-upload text-white mb-1"></i>
                            <span class="text-white text-xs font-semibold">Ubah Foto</span>
                        </div>
                    </div>
                    <input type="file" name="profile_photo" id="profile_photo" class="hidden" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewPhoto(event)">
                    <input type="hidden" name="cropped_photo" id="cropped_photo">
                    <p class="text-[10px] text-gray-400 text-center w-32">Otomatis di-crop 1:1<br>Maks: 2MB (JPG/PNG)</p>
                    @error('profile_photo') <span class="text-red-500 text-xs mt-1 text-center block">{{ $message }}</span> @enderror
                </div>

                <!-- Fields Utama -->
                <div class="flex-1 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name', $user->name) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-brand-blue focus:border-brand-blue outline-none transition">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Username <span class="text-red-500">*</span></label>
                <input type="text" name="username" required value="{{ old('username', $user->username) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-brand-blue focus:border-brand-blue outline-none transition">
                @error('username') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" required value="{{ old('email', $user->email) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-brand-blue focus:border-brand-blue outline-none transition">
                <p class="text-xs text-gray-500 mt-1">Alamat email resmi akun website (digunakan untuk reset password/notifikasi)</p>
                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Ubah Password Area -->
            <div class="border-t border-b border-gray-100 my-6 py-6 bg-brand-blue-light/30 p-4 rounded-xl">
                <h4 class="font-bold text-brand-blue text-sm mb-1">Ubah Password Admin</h4>
                <p class="text-xs text-gray-500 mb-4">Kosongkan jika tidak ingin mengubah password.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Password Baru (opsional)</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" placeholder="Masukkan password baru..." autocomplete="new-password"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-brand-blue focus:border-brand-blue outline-none transition pr-10 text-sm">
                            <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-brand-blue-hover focus:outline-none cursor-pointer" title="Tampilkan/Sembunyikan Password">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password baru..." autocomplete="new-password"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-brand-blue focus:border-brand-blue outline-none transition pr-10 text-sm">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-brand-blue-hover focus:outline-none cursor-pointer" title="Tampilkan/Sembunyikan Password">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Widget Indikator Password Policy -->
                @include('admin.partials.password-strength-widget')
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2.5 bg-brand-blue text-white font-bold rounded-lg hover:bg-brand-blue-hover transition shadow-md text-sm">Simpan Profil Saya</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Cropper -->
<div id="cropperModal" class="fixed inset-0 z-[10000] hidden bg-black/80 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
        <div class="px-4 py-3 border-b flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800">Geser dan Sesuaikan Foto</h3>
            <button type="button" onclick="closeCropper()" class="text-gray-500 hover:text-red-500"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-4 bg-gray-100 flex-1 relative flex items-center justify-center min-h-[300px]">
            <img id="cropperImage" src="" class="max-w-full hidden">
        </div>
        <div class="px-4 py-3 border-t flex justify-end gap-3 bg-gray-50">
            <button type="button" onclick="closeCropper()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded font-bold hover:bg-gray-300 transition text-sm">Batal</button>
            <button type="button" onclick="applyCrop()" class="px-4 py-2 bg-brand-blue text-white rounded font-bold hover:bg-brand-blue-hover transition text-sm">Gunakan Foto</button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    let cropper = null;
    let originalFile = null;

    document.addEventListener('DOMContentLoaded', function () {
        attachPasswordStrengthMeter('password');
    });

    function previewPhoto(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            originalFile = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                const image = document.getElementById('cropperImage');
                image.src = e.target.result;
                image.classList.remove('hidden');
                document.getElementById('cropperModal').classList.remove('hidden');
                
                if (cropper) {
                    cropper.destroy();
                }
                
                cropper = new Cropper(image, {
                    aspectRatio: 1,
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
            reader.readAsDataURL(originalFile);
        }
    }

    function closeCropper() {
        document.getElementById('cropperModal').classList.add('hidden');
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        document.getElementById('profile_photo').value = ""; // reset input
        originalFile = null;
    }

    function applyCrop() {
        if (!cropper) return;
        
        const canvas = cropper.getCroppedCanvas({
            width: 500,
            height: 500,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });
        
        const base64Data = canvas.toDataURL('image/jpeg', 0.9);
        
        // Update hidden input
        document.getElementById('cropped_photo').value = base64Data;
        
        // Update preview
        const preview = document.getElementById('photo-preview');
        const placeholder = document.getElementById('photo-placeholder');
        
        preview.src = base64Data;
        preview.classList.remove('hidden');
        if (placeholder) placeholder.classList.add('hidden');
        
        // Clear file input to prevent validation conflicts
        document.getElementById('profile_photo').value = "";
        
        // Close modal
        document.getElementById('cropperModal').classList.add('hidden');
        cropper.destroy();
        cropper = null;
    }
</script>
@endpush
@endsection
