@extends('layouts.admin')

@section('title', 'Pengaturan Website & Footer - Admin Panel')
@section('page_title', 'Pengaturan Profil Instansi & CRUD Footer Website')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 max-w-5xl mx-auto">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800"><i class="fas fa-sliders-h text-blue-600 mr-2"></i> Identitas Website & Kelola Konten Footer</h3>
        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">Khusus Superadmin</span>
    </div>
    
    <div class="p-6">
        <form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Tab / Section Header Logos & Identitas Utama -->
            <div class="mb-8 p-5 bg-blue-50/60 border border-blue-100 rounded-xl">
                <h4 class="font-bold text-blue-900 mb-4 text-sm flex items-center gap-2">
                    <i class="fas fa-landmark text-blue-600"></i> Identitas & Logo Utama (Header & Navbar)
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Logo Header Navbar</label>
                        <div class="w-full h-28 bg-white border border-gray-300 rounded-lg flex items-center justify-center p-2 shadow-xs mb-2 relative overflow-hidden">
                            @if(isset($settings['site_logo']) && $settings['site_logo'])
                                <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Logo Header" class="max-h-full max-w-full object-contain">
                            @else
                                <img src="https://diskominfo.probolinggokab.go.id/backend/gambar/logo_backend.png" alt="Logo Default" class="max-h-full max-w-full object-contain filter invert">
                            @endif
                        </div>
                        <input type="file" name="site_logo" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                        <p class="text-[10px] text-gray-500 mt-1"><strong>Rekomendasi: Format PNG (Landscape 3:1).</strong></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Logo Header Kanan</label>
                        <div class="w-full h-28 bg-white border border-gray-300 rounded-lg flex items-center justify-center p-2 shadow-xs mb-2 relative overflow-hidden">
                            @if(isset($settings['berakhlak_logo']) && $settings['berakhlak_logo'])
                                <img src="{{ asset('storage/' . $settings['berakhlak_logo']) }}" alt="Logo Header Kanan" class="max-h-full max-w-full object-contain">
                            @else
                                <img src="https://diskominfo.probolinggokab.go.id/frontend/images/img-berakhlak.png" alt="Logo Header Kanan Default" class="max-h-full max-w-full object-contain">
                            @endif
                        </div>
                        <input type="file" name="berakhlak_logo" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                        <p class="text-[10px] text-gray-500 mt-1"><strong>Rekomendasi: Format PNG Transparan.</strong></p>
                    </div>

                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Nama Instansi / Website <span class="text-red-500">*</span></label>
                            <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'Bagian Pemerintahan Kabupaten Probolinggo' }}" required
                                   class="w-full px-3.5 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-800 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Singkat SEO</label>
                            <textarea name="site_description" rows="2" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-xs">{{ $settings['site_description'] ?? 'Website Resmi Bagian Pemerintahan Sekretariat Daerah Kabupaten Probolinggo.' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION CRUD KONTEN FOOTER WEBSITE -->
            <div class="mb-8 p-5 bg-amber-50/50 border border-amber-200 rounded-xl">
                <h4 class="font-bold text-amber-900 mb-4 text-sm flex items-center gap-2 border-b border-amber-200 pb-3">
                    <i class="fas fa-shoe-prints text-amber-600"></i> CRUD Pengaturan Konten Footer Website
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Kolom Footer 1: Logo & Deskripsi Footer -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Logo Khusus Footer (Opsional)</label>
                            <div class="flex items-center gap-4 mb-2">
                                <div class="w-24 h-16 bg-gray-900 rounded p-1 flex items-center justify-center">
                                    @if(isset($settings['footer_logo']) && $settings['footer_logo'])
                                        <img src="{{ asset('storage/' . $settings['footer_logo']) }}" alt="Logo Footer" class="max-h-full max-w-full object-contain">
                                    @else
                                        <img src="https://diskominfo.probolinggokab.go.id/backend/gambar/logo_backend.png" alt="Logo Default" class="max-h-full max-w-full object-contain filter invert">
                                    @endif
                                </div>
                                <input type="file" name="footer_logo" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-amber-600 file:text-white hover:file:bg-amber-700 cursor-pointer">
                                <p class="text-[10px] text-gray-500 mt-1"><strong>Rekomendasi: Format PNG Transparan, disarankan warna teks putih/terang untuk background gelap.</strong></p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Teks Deskripsi Profil di Footer</label>
                            <textarea name="footer_description" rows="4" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 outline-none text-xs leading-relaxed" placeholder="Tuliskan deskripsi profil singkat yang tampil pada footer kiri website...">{{ $settings['footer_description'] ?? 'Website Resmi Bagian Pemerintahan Sekretariat Daerah Kabupaten Probolinggo. Merupakan media informasi elektronik satu pintu meliputi pelayanan publik, dokumen transparansi kinerja, dan informasi umum bagi masyarakat.' }}</textarea>
                        </div>
                    </div>

                    <!-- Kolom Footer 2: Links Survey & QR Code SKM -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Judul Kolom Survey Footer</label>
                            <input type="text" name="survey_title" value="{{ $settings['survey_title'] ?? 'Links Survey / QR SKM' }}" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg text-xs font-bold">
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="w-20 h-20 bg-white border border-gray-300 rounded p-1 flex flex-col items-center justify-center">
                                @if(isset($settings['survey_qr_image']) && $settings['survey_qr_image'])
                                    <img src="{{ asset('storage/' . $settings['survey_qr_image']) }}" alt="QR Code" class="max-h-full max-w-full object-contain">
                                @else
                                    <img src="https://diskominfo.probolinggokab.go.id/backend/gambar/qr_code_kominfo.png" alt="QR Default" class="max-h-full max-w-full object-contain">
                                @endif
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Unggah Gambar QR Code Survey SKM</label>
                                <input type="file" name="survey_qr_image" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-amber-600 file:text-white hover:file:bg-amber-700 cursor-pointer">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Tautan Teks / Form Survey Online (URL)</label>
                            <input type="url" name="survey_link" value="{{ $settings['survey_link'] ?? 'https://probolinggokab.go.id' }}" placeholder="https://..." class="w-full px-3.5 py-2 border border-gray-300 rounded-lg text-xs font-mono">
                        </div>
                    </div>

                </div>

                <!-- Teks Hak Cipta / Copyright Bottom Footer -->
                <div class="mt-4 pt-4 border-t border-amber-200">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Teks Hak Cipta / Copyright Bottom Footer</label>
                    <input type="text" name="footer_copyright" value="{{ $settings['footer_copyright'] ?? 'BAGIAN PEMERINTAHAN KABUPATEN PROBOLINGGO © ' . date('Y') . '. All Rights Reserved' }}" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg text-xs font-semibold text-gray-800">
                </div>

            </div>



            <div class="border-t border-gray-100 mt-6 pt-5 mb-6">
                <button type="submit" class="w-full py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-blue-700 transition shadow text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Simpan Seluruh Pengaturan Website & Footer
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
