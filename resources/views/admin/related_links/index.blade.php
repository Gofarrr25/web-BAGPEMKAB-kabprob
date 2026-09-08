@extends('layouts.admin')

@section('page_title', 'Kelola Link Terkait')

@section('content')
<div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-100">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Daftar Link Terkait</h2>
            <p class="text-xs text-gray-500 mt-1">Kelola link instansi atau website terkait yang tampil di bagian bawah footer.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.related-links.create') }}" class="px-4 py-2 bg-brand-blue text-white rounded shadow hover:bg-brand-dark transition font-bold text-sm flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Link Terkait
            </a>
        </div>
    </div>

    <div class="overflow-x-auto w-full">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                    <th class="px-4 py-3 font-bold border-b w-16 text-center whitespace-nowrap">No</th>
                    <th class="px-4 py-3 font-bold border-b whitespace-nowrap">Nama Link / Instansi</th>
                    <th class="px-4 py-3 font-bold border-b whitespace-nowrap">Logo</th>
                    <th class="px-4 py-3 font-bold border-b whitespace-nowrap">URL</th>
                    <th class="px-4 py-3 font-bold border-b text-right w-28 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($related_links as $link)
                <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                    <td class="px-4 py-3 text-center text-gray-500 font-medium">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3">
                        <p class="font-bold text-gray-800">{{ $link->name }}</p>
                    </td>
                    <td class="px-4 py-3">
                        @php 
                            $domain = parse_url($link->url, PHP_URL_HOST); 
                            $logoUrl = $link->logo_url ? $link->logo_url : "https://logo.clearbit.com/{$domain}";
                        @endphp
                        <div class="w-[200px] h-[70px] bg-white border border-gray-200 rounded flex items-center justify-center overflow-hidden shrink-0 p-1">
                            <img src="{{ $logoUrl }}" 
                                 class="w-full h-full object-contain" 
                                 alt="{{ $link->name }}"
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($link->name) }}&background=f3f4f6&color=4b5563&size=128';">
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ $link->url }}" target="_blank" class="text-brand-blue hover:underline text-xs">{{ $link->url }}</a>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <div class="flex justify-end gap-2 items-center">
                            <!-- Toggle switch -->
                            <label class="relative inline-flex items-center cursor-pointer mr-2" title="Aktif/Nonaktifkan Link Terkait">
                                <input type="checkbox" onchange="toggleRelatedLink('{{ $link->id }}', this)" class="sr-only peer" {{ $link->is_active ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                            
                            <a href="{{ route('admin.related-links.edit', $link->id) }}" class="w-8 h-8 rounded bg-yellow-100 text-yellow-600 flex items-center justify-center hover:bg-yellow-200 transition" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.related-links.destroy', $link->id) }}" method="POST" onsubmit="event.preventDefault(); confirmDelete(this, 'Link Terkait', 'Data Terpilih', true);" class="inline-block m-0 p-0">
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
@endsection

@push('scripts')
<script>
    function toggleRelatedLink(id, checkbox) {
        const isActive = checkbox.checked;
        fetch(`/admin/related-links/${id}/toggle`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ is_active: isActive })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                checkbox.checked = !isActive;
                alert('Gagal mengubah status link');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            checkbox.checked = !isActive;
            alert('Terjadi kesalahan saat mengubah status');
        });
    }
</script>
@endpush
