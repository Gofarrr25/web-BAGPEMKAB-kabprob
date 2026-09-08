@extends('layouts.admin')

@section('page_title', 'Kelola Widget Home')

@section('content')
<div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-100">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Daftar Widget Home</h2>
            <p class="text-xs text-gray-500 mt-1">Kelola urutan dan visibilitas widget di sidebar kanan halaman Home.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded shadow-sm hover:bg-gray-200 transition font-bold text-sm flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
            <a href="{{ route('admin.home-widgets.create') }}" class="px-4 py-2 bg-brand-blue text-white rounded shadow hover:bg-brand-dark transition font-bold text-sm flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Widget
            </a>
        </div>
    </div>

    <div class="overflow-x-auto w-full">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                    <th class="px-4 py-3 font-bold border-b w-16 text-center whitespace-nowrap">Order</th>
                    <th class="px-4 py-3 font-bold border-b whitespace-nowrap">Widget / Judul</th>
                    <th class="px-4 py-3 font-bold border-b whitespace-nowrap">Link / URL</th>
                    <th class="px-4 py-3 font-bold border-b text-center whitespace-nowrap">Status</th>
                    <th class="px-4 py-3 font-bold border-b text-right whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($widgets as $widget)
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
                            <a href="{{ $widget->link_url }}" target="_blank" class="text-brand-blue hover:underline text-xs flex items-center gap-1">
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
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.home-widgets.edit', $widget->id) }}" class="w-8 h-8 rounded bg-yellow-100 text-yellow-600 flex items-center justify-center hover:bg-yellow-200 transition" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.home-widgets.destroy', $widget->id) }}" method="POST" onsubmit="event.preventDefault(); confirmDelete(this, 'Widget', 'Data Terpilih', true);">
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
@endsection



