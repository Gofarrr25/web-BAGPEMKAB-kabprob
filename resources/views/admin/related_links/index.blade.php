@extends('layouts.admin')

@section('page_title', 'Kelola Link Terkait')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
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
                        @if($link->logo)
                        <div class="w-12 h-12 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('storage/' . $link->logo) }}" alt="Logo" class="max-w-full max-h-full object-contain">
                        </div>
                        @else
                        <div class="w-12 h-12 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400">
                            <i class="fas fa-link"></i>
                        </div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ $link->url }}" target="_blank" class="text-blue-500 hover:underline text-xs">{{ $link->url }}</a>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
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
@endsection
