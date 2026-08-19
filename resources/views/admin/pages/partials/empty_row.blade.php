@php
    $isSpecial = false;
    $specialModule = '';
    $specialRoute = '';
    
    $moduleType = $menu->module_type ?? 'page';
    $url = $menu->url ?? '';

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
        $specialRoute = url('admin/settings');
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
        } elseif (\Illuminate\Support\Str::startsWith($url, ['http://', 'https://'])) {
            $isSpecial = true;
            $specialModule = 'Tautan Eksternal';
            $specialRoute = $url;
        }
    }
@endphp
<tr class="hover:bg-red-50/40 transition searchable-row" data-search="{{ strtolower($menu->title) }}">
    <td class="px-4 py-4 text-center font-bold text-gray-400 align-middle">
        <i class="fas fa-level-up-alt rotate-90 text-gray-300"></i>
    </td>
    <td class="px-4 py-4 font-extrabold text-gray-900 align-middle">
        <div class="flex items-center gap-2 ml-4">
            <i class="{{ $menu->icon ?? 'fas fa-link' }} text-gray-400 text-sm w-4 text-center"></i>
            <span class="text-gray-500 truncate max-w-[200px]" title="{{ $menu->title }}">{{ $menu->title }}</span>
        </div>
    </td>
    <td class="px-4 py-4 align-middle">
        <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-md font-bold text-[11px] border border-blue-200 uppercase tracking-wider whitespace-nowrap">
            {{ $parentTitle }}
        </span>
    </td>
    <td class="px-4 py-4 align-middle">
        <span class="text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200 font-semibold text-[11px] whitespace-nowrap">
            {{ $menu->title }}
        </span>
    </td>
    <td class="px-4 py-4 font-medium align-middle" colspan="3">
        @if($isSpecial)
            <div class="flex items-center gap-2">
                <span class="text-indigo-600 text-[10px] font-bold bg-indigo-50 px-2 py-1 rounded-md border border-indigo-100 whitespace-nowrap">
                    <i class="fas fa-layer-group"></i> Modul Khusus Terpisah
                </span>
                <span class="text-[10px] text-gray-400 font-medium hidden md:inline">({{ $specialModule }})</span>
            </div>
        @else
            <span class="text-red-500 text-[10px] font-bold italic bg-red-50 px-2 py-1 rounded-md border border-red-100 whitespace-nowrap">
                <i class="fas fa-exclamation-triangle"></i> Belum ada konten halaman statis
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
            <div class="flex items-center justify-end w-full">
                @role('Superadmin')
                <a href="{{ route('admin.menus.content', $menu->id) }}" class="inline-flex justify-center items-center gap-1.5 px-3 py-1.5 bg-yellow-50 text-yellow-700 hover:bg-yellow-600 hover:text-white font-bold rounded-lg border border-yellow-200 transition text-[11px] shadow-sm whitespace-nowrap">
                    <i class="fas fa-plus"></i> Buat Konten
                </a>
                @else
                <span class="text-[10px] text-gray-400 italic text-right w-full block whitespace-nowrap">Hubungi Superadmin untuk membuat konten</span>
                @endrole
            </div>
        @endif
    </td>
</tr>
