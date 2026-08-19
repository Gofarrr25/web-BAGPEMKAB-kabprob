@extends('layouts.admin')

@section('title', 'Manajemen Feed Instagram - Admin Panel')
@section('page_title', 'Kelola Instagram Feed Instansi')

@section('content')
<div class="space-y-8 max-w-6xl mx-auto">
    
    <!-- Banner Informasi Header -->
    <div class="bg-gradient-to-r from-pink-600 via-purple-600 to-indigo-700 text-white rounded-2xl p-6 shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="p-2 bg-white/20 rounded-lg text-white"><i class="fab fa-instagram text-2xl"></i></span>
                <h3 class="font-extrabold text-xl">Integrasi & Feed Instagram Resmi</h3>
            </div>
            <p class="text-xs text-pink-100 mt-1 max-w-xl leading-relaxed">
                Kelola profil, informasi followers/posts, dan postingan Instagram resmi instansi. Tampilan feed pada beranda website publik akan otomatis diperbarui sesuai pengaturan ini.
            </p>
        </div>
        @if(isset($settings['instagram_url']) && $settings['instagram_url'])
            <a href="{{ $settings['instagram_url'] }}" target="_blank" class="px-4 py-2.5 bg-white text-pink-700 font-extrabold rounded-xl text-xs hover:bg-pink-50 transition shadow-sm flex items-center gap-2">
                <i class="fab fa-instagram text-base"></i> Buka Profil @ {{ $username }}
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- KOLOM KIRI: SETTING PROFIL & TAMBAH POSTINGAN -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- FORM 1: SETTING AKUN INSTAGRAM -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
                <h4 class="font-bold text-gray-800 text-sm flex items-center gap-2 border-b pb-3">
                    <i class="fas fa-link text-pink-600"></i> Tautan Profil Instagram
                </h4>

                <form action="{{ route('admin.instagram.profile') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            Link / URL Instagram Instansi <span class="text-red-500">*</span>
                        </label>
                        <input type="url" name="instagram_url" placeholder="Contoh: https://www.instagram.com/bagpemerintahan_probolinggokab" value="{{ $settings['instagram_url'] ?? 'https://www.instagram.com/bagpemerintahan_probolinggokab' }}" required class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs font-bold text-gray-800 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-pink-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            Kode Embed / Script Widget (Opsional - Sangat Disarankan)
                        </label>
                        <textarea name="instagram_embed_script" rows="4" placeholder="<script src='https://apps.elfsight.com/p/platform.js' defer></script>..." class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs font-mono">{{ $settings['instagram_embed_script'] ?? '' }}</textarea>
                        <p class="text-[10px] text-gray-500 mt-1">Karena Instagram memblokir penarikan foto otomatis, Anda bisa menggunakan layanan gratis seperti <strong>Elfsight, SnapWidget, atau TagEmbed</strong> dan menempelkan kodenya di sini agar otomatis tampil.</p>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-pink-600 hover:bg-pink-700 text-white font-bold rounded-xl text-xs transition shadow-sm flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Terapkan Pengaturan
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-4">
            <!-- PREVIEW WIDGET -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h4 class="font-bold text-gray-800 text-sm flex items-center gap-2 border-b pb-3 mb-4">
                    <i class="fas fa-desktop text-pink-600"></i> Preview Widget Halaman Depan
                </h4>

                <div class="border-[4px] border-gray-800 rounded-[2rem] p-2 bg-gray-50 mx-auto w-[320px] shadow-lg relative overflow-hidden">
                    <!-- Top Bar -->
                    <div class="w-20 h-5 bg-gray-800 absolute top-0 left-1/2 transform -translate-x-1/2 rounded-b-xl z-10"></div>
                    
                    <!-- Native Instagram Embed Preview -->
                    <div class="bg-white w-full h-[500px] overflow-y-auto">
                        @if(!empty($settings['instagram_embed_script']))
                            {!! $settings['instagram_embed_script'] !!}
                        @else
                            <blockquote class="instagram-media" 
                                data-instgrm-permalink="{{ $settings['instagram_url'] ?? 'https://www.instagram.com/bagpemerintahan_probolinggokab' }}?utm_source=ig_embed&amp;utm_campaign=loading" 
                                data-instgrm-version="14" 
                                style=" background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:100%; min-width:326px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);">
                            </blockquote>
                            <script async src="https://www.instagram.com/embed.js"></script>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
