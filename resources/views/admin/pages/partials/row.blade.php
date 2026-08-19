@php
    $isSpecial = false;
    $specialModule = '';
    $specialRoute = '';
    
    $moduleType = $menu ? $menu->module_type : 'page';
    $url = $menu ? $menu->url : '/page/' . $page->slug;
    $url = $url ?? '';

    // Identifikasi eksplist dari database
    if ($moduleType === 'posts') {
        $isSpecial = true;
        $specialModule = 'Berita & Artikel';
        $specialRoute = route('admin.posts.index');
    } elseif ($moduleType === 'documents') {
        $isSpecial = true;
        $specialModule = 'Dokumen PPID';
        $specialRoute = route('admin.documents.index');
    } elseif ($moduleType === 'agendas') {
        $isSpecial = true;
        $specialModule = 'Agenda Kegiatan';
        $specialRoute = route('admin.agendas.index');
    } elseif ($moduleType === 'galleries') {
        $isSpecial = true;
        $specialModule = 'Galeri & Video';
        $specialRoute = route('admin.galleries.index');
    } elseif ($moduleType === 'members') {
        $isSpecial = true;
        $specialModule = 'Struktur Organisasi';
        $specialRoute = route('admin.organization-members.index');
    } elseif ($moduleType === 'contact') {
        $isSpecial = true;
        $specialModule = 'Kontak & Pesan';
        $specialRoute = url('admin/settings'); // Or wherever contact is
    } elseif ($moduleType === 'custom') {
        $isSpecial = true;
        $specialModule = 'Tautan Eksternal';
        $specialRoute = $url;
    }
    
    // Fallback berdasarkan URL jika tipe modul belum diset (data lama)
    if (!$isSpecial) {
        if (\Illuminate\Support\Str::startsWith($url, ['/informasi', '/berita', '/posts'])) {
            $isSpecial = true;
            $specialModule = 'Berita & Artikel';
            $specialRoute = route('admin.posts.index');
        } elseif (\Illuminate\Support\Str::startsWith($url, ['/dokumen'])) {
            $isSpecial = true;
            $specialModule = 'Dokumen PPID';
            $specialRoute = route('admin.documents.index');
        } elseif (\Illuminate\Support\Str::startsWith($url, ['/agenda'])) {
            $isSpecial = true;
            $specialModule = 'Agenda Kegiatan';
            $specialRoute = route('admin.agendas.index');
        } elseif (\Illuminate\Support\Str::startsWith($url, ['/galeri', '/page/galeri', '/page/video', '/galleries'])) {
            $isSpecial = true;
            $specialModule = 'Galeri & Video';
            $specialRoute = route('admin.galleries.index');
        } elseif (\Illuminate\Support\Str::startsWith($url, ['/struktur-organisasi', '/page/struktur-organisasi'])) {
            $isSpecial = true;
            $specialModule = 'Struktur Organisasi';
            $specialRoute = route('admin.organization-members.index');
        } elseif (\Illuminate\Support\Str::startsWith($url, ['/kontak', '/page/kontak', '/kontak-resmi'])) {
            $isSpecial = true;
            $specialModule = 'Kontak & Pesan';
            $specialRoute = url('admin/settings');
        }
    }
@endphp
<tr class="hover:bg-blue-50/40 transition searchable-row" data-search="{{ strtolower($page->title) }} {{ strtolower($page->slug) }}">
    <td class="px-4 py-4 text-center font-bold text-gray-400 align-middle">
        @if($isSubmenu)
            <i class="fas fa-level-up-alt rotate-90 text-gray-300"></i>
        @else
            <i class="fas fa-circle text-[8px] text-gray-300"></i>
        @endif
    </td>
    <td class="px-4 py-4 font-extrabold text-gray-900 align-middle">
        <div class="flex items-center gap-2 {{ $isSubmenu ? 'ml-4' : '' }}">
            <i class="{{ $menu->icon ?? 'fas fa-file-alt' }} text-blue-600 text-sm w-4 text-center"></i>
            <span class="truncate max-w-[200px]" title="{{ $page->title }}">{{ $page->title }}</span>
        </div>
    </td>
    <td class="px-4 py-4 align-middle">
        <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-md font-bold text-[11px] border border-blue-200 uppercase tracking-wider whitespace-nowrap">
            {{ $isSubmenu ? $parentTitle : ($menu->title ?? '-') }}
        </span>
    </td>
    <td class="px-4 py-4 align-middle">
        @if($isSubmenu)
            <span class="text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200 font-semibold text-[11px] whitespace-nowrap">
                {{ $menu->title }}
            </span>
        @else
            <span class="text-gray-400 text-[11px] font-bold">-</span>
        @endif
    </td>
    <td class="px-4 py-4 font-medium align-middle">
        @if($page->pdf_file)
            <a href="{{ asset('storage/' . $page->pdf_file) }}" target="_blank" class="inline-flex items-center gap-1.5 px-2 py-1 bg-red-50 text-red-700 border border-red-200 rounded-md font-bold text-[10px] hover:bg-red-100 transition whitespace-nowrap">
                <i class="fas fa-file-pdf text-red-600"></i> Dokumen PDF
            </a>
        @elseif($page->image)
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md text-[10px] font-bold whitespace-nowrap">
                <i class="fas fa-image text-emerald-600"></i> Foto/Gambar
            </span>
        @else
            <span class="text-gray-400 text-[10px] font-bold whitespace-nowrap"><i class="fas fa-align-left text-gray-300 mr-1"></i> Teks Konten</span>
        @endif
    </td>
    <td class="px-4 py-4 align-middle">
        <div class="flex items-center gap-1.5">
            <a href="{{ url('/page/' . $page->slug) }}" target="_blank" class="text-[11px] bg-slate-50 hover:bg-slate-100 text-blue-600 font-mono font-semibold border border-slate-200 px-2 py-1 rounded-md transition truncate max-w-[150px] inline-block" title="/page/{{ $page->slug }}">
                /page/{{ $page->slug }}
            </a>
            <a href="{{ url('/page/' . $page->slug) }}" target="_blank" class="text-gray-400 hover:text-blue-600 transition" title="Buka Halaman Publik">
                <i class="fas fa-external-link-alt text-[10px]"></i>
            </a>
        </div>
    </td>
    <td class="px-4 py-4 text-center align-middle">
        @if($page->status === 'publish')
            <span class="bg-emerald-100 text-emerald-800 px-2 py-1 rounded-full font-bold text-[10px] inline-flex items-center gap-1 whitespace-nowrap">
                <i class="fas fa-check-circle text-emerald-600"></i> Publish
            </span>
        @else
            <span class="bg-amber-100 text-amber-800 px-2 py-1 rounded-full font-bold text-[10px] inline-flex items-center gap-1 whitespace-nowrap">
                <i class="fas fa-clock text-amber-600"></i> Draft
            </span>
        @endif
    </td>
    <td class="px-4 py-4 align-middle whitespace-nowrap">
        @if($isSpecial)
            <div class="flex items-center justify-end">
                <a href="{{ $specialRoute }}" class="inline-flex justify-center items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white font-bold rounded-lg border border-indigo-200 transition text-[11px] shadow-sm whitespace-nowrap">
                    <i class="fas fa-arrow-right"></i> Kelola Modul Khusus
                </a>
            </div>
        @else
            <div class="flex items-center justify-end gap-1.5">
                <a href="{{ url('/page/' . $page->slug) }}" target="_blank" class="inline-flex justify-center items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white font-bold rounded-lg border border-emerald-200 transition text-[11px] shadow-sm whitespace-nowrap">
                    <i class="fas fa-eye"></i> Preview
                </a>
                <a href="{{ route('admin.pages.edit', $page->id) }}" class="inline-flex justify-center items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white font-bold rounded-lg border border-blue-200 transition text-[11px] shadow-sm whitespace-nowrap">
                    <i class="fas fa-edit"></i> Edit Konten
                </a>
            </div>
        @endif
    </td>
</tr>
