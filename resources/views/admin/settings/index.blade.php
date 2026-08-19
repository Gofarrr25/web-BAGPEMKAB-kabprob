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
                        <input type="file" name="site_logo" accept="image/*" class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
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
                        <input type="file" name="berakhlak_logo" accept="image/*" class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
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
                                <input type="file" name="footer_logo" accept="image/*" class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-amber-600 file:text-white hover:file:bg-amber-700 cursor-pointer">
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
                                <input type="file" name="survey_qr_image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-amber-600 file:text-white hover:file:bg-amber-700 cursor-pointer">
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

        <!-- SECTION KELOLA WIDGET HOME -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4">
                <div>
                    <h4 class="font-bold text-purple-900 mb-1 text-base flex items-center gap-2">
                        <i class="fas fa-th-large text-purple-600"></i> Kelola Widget Home
                    </h4>
                    <p class="text-xs text-gray-600">Atur widget yang tampil di sidebar kanan halaman Home (Maklumat Pelayanan, Kepala Bagian, dll).</p>
                </div>
                <div>
                    <a href="{{ route('admin.home-widgets.create') }}" class="px-5 py-2.5 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 transition shadow flex items-center justify-center gap-2 text-xs">
                        <i class="fas fa-plus"></i> Tambah Widget
                    </a>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto w-full">
        <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                <th class="px-4 py-3 font-bold border-b w-16 text-center">Order</th>
                                <th class="px-4 py-3 font-bold border-b">Widget / Judul</th>
                                <th class="px-4 py-3 font-bold border-b">Link / URL</th>
                                <th class="px-4 py-3 font-bold border-b text-center">Status</th>
                                <th class="px-4 py-3 font-bold border-b text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($homeWidgets as $widget)
                            <tr class="border-b hover:bg-gray-50 transition group">
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block w-8 h-8 rounded-full bg-gray-100 text-gray-600 font-bold leading-8">{{ $widget->order_index }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($widget->image_path)
                                            <img src="{{ asset('storage/' . $widget->image_path) }}" alt="{{ $widget->title }}" class="w-12 h-12 rounded object-cover border border-gray-200">
                                        @else
                                            <div class="w-12 h-12 rounded bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                        <span class="font-bold text-gray-800">{{ $widget->title }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($widget->link_url)
                                        <a href="{{ $widget->link_url }}" target="_blank" class="text-blue-500 hover:underline text-xs flex items-center gap-1">
                                            {{ Str::limit($widget->link_url, 30) }} <i class="fas fa-external-link-alt text-[10px]"></i>
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs italic">Tanpa Link</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($widget->is_active)
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Aktif</span>
                                    @else
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.home-widgets.edit', $widget->id) }}" class="w-8 h-8 rounded bg-yellow-100 text-yellow-600 flex items-center justify-center hover:bg-yellow-200 transition" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.home-widgets.destroy', $widget->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus widget ini?');" class="inline-block m-0 p-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded bg-red-100 text-red-600 flex items-center justify-center hover:bg-red-200 transition" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-th-large text-4xl text-gray-300 mb-2"></i>
                                    <p class="font-bold">Belum ada Widget Home</p>
                                    <p class="text-xs">Silakan tambah widget baru untuk ditampilkan di Home.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END WIDGET SECTION -->

        <!-- SECTION KELOLA LINK TERKAIT -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4">
                <div>
                    <h4 class="font-bold text-teal-900 mb-1 text-base flex items-center gap-2">
                        <i class="fas fa-link text-teal-600"></i> Kelola Link Terkait
                    </h4>
                    <p class="text-xs text-gray-600">Atur link instansi atau website terkait yang tampil di bagian bawah footer.</p>
                </div>
                <div>
                    <a href="{{ route('admin.related-links.create') }}" class="px-5 py-2.5 bg-teal-600 text-white font-bold rounded-lg hover:bg-teal-700 transition shadow flex items-center justify-center gap-2 text-xs">
                        <i class="fas fa-plus"></i> Tambah Link
                    </a>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                <th class="px-4 py-3 font-bold border-b w-16 text-center">Urutan</th>
                                <th class="px-4 py-3 font-bold border-b">Logo & Nama</th>
                                <th class="px-4 py-3 font-bold border-b">URL Website</th>
                                <th class="px-4 py-3 font-bold border-b text-center">Status</th>
                                <th class="px-4 py-3 font-bold border-b text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($relatedLinks ?? [] as $link)
                            <tr class="border-b hover:bg-gray-50 transition group">
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block w-8 h-8 rounded-full bg-gray-100 text-gray-600 font-bold leading-8">{{ $link->order }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-24 h-12 rounded border border-gray-200 bg-white flex items-center justify-center p-1 shrink-0">
                                            @php 
                                                $domain = parse_url($link->url, PHP_URL_HOST); 
                                                $logoUrl = $link->logo_url ? $link->logo_url : "https://logo.clearbit.com/{$domain}";
                                            @endphp
                                            <img src="{{ $logoUrl }}" alt="Logo" class="max-w-full max-h-full object-contain" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($link->name) }}&background=f3f4f6&color=4b5563';">
                                        </div>
                                        <span class="font-bold text-gray-800">{{ $link->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ $link->url }}" target="_blank" class="text-blue-500 hover:underline text-xs flex items-center gap-1">
                                        {{ Str::limit($link->url, 40) }} <i class="fas fa-external-link-alt text-[10px]"></i>
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($link->is_active)
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Aktif</span>
                                    @else
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.related-links.edit', $link->id) }}" class="w-8 h-8 rounded bg-yellow-100 text-yellow-600 flex items-center justify-center hover:bg-yellow-200 transition" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.related-links.destroy', $link->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus link ini?');" class="inline-block m-0 p-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded bg-red-100 text-red-600 flex items-center justify-center hover:bg-red-200 transition" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-link text-4xl text-gray-300 mb-2"></i>
                                    <p class="font-bold">Belum ada Link Terkait</p>
                                    <p class="text-xs">Silakan tambah link website terkait untuk ditampilkan di footer.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END LINK TERKAIT SECTION -->

    </div>
</div>
@endsection



