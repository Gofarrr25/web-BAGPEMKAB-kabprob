@extends('layouts.admin')

@section('title', 'Edit Admin & Ubah Password - Admin Panel')
@section('page_title', 'Edit Admin & Ubah Password')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 max-w-3xl">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Form Edit Admin & Ubah Password</h3>
            <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-semibold transition flex items-center justify-center gap-1 bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left"></i> Kembali</a>
        <span class="bg-yellow-100 text-yellow-800 text-xs px-3 py-1 rounded-full font-bold">Edit Admin</span>
    </div>
    
    <div class="p-6">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap Admin <span class="text-red-500">*</span></label>
                <input type="text" name="name" required value="{{ old('name', $user->name) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Username Login <span class="text-red-500">*</span></label>
                <input type="text" name="username" required value="{{ old('username', $user->username) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                @error('username') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" required value="{{ old('email', $user->email) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Ubah Password Area -->
            <div class="border-t border-b border-gray-100 my-6 py-6 bg-blue-50/30 p-4 rounded-xl">
                <h4 class="font-bold text-blue-900 text-sm mb-1">Ubah Password Admin</h4>
                <p class="text-xs text-gray-500 mb-4">Biarkan password kosong jika tidak ingin mengubah password akun admin ini.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Password Baru</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" placeholder="Masukkan password baru..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition pr-10 text-sm">
                            <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 focus:outline-none cursor-pointer" title="Tampilkan/Sembunyikan Password">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password baru..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition pr-10 text-sm">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 focus:outline-none cursor-pointer" title="Tampilkan/Sembunyikan Password">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Widget Indikator Password Policy -->
                @include('admin.partials.password-strength-widget')
            </div>

            <div class="flex gap-4">
                
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition shadow-md text-sm">Perbarui Data Admin</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        attachPasswordStrengthMeter('password');
    });
</script>
@endpush
@endsection
