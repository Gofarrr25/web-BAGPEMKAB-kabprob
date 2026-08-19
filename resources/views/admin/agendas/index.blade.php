@extends('layouts.admin')

@section('title', 'Agenda Kegiatan - Admin Panel')
@section('page_title', 'Manajemen Agenda Kegiatan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Kolom Kiri: Form Tambah Agenda -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-24">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Buat Jadwal Agenda Baru</h3>
            <form action="{{ route('admin.agendas.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Agenda/Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="Rapat Koordinasi..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal & Waktu <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="event_date" required value="{{ old('event_date') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Lokasi Acara</label>
                    <input type="text" name="location" value="{{ old('location') }}" placeholder="Ruang Rapat Bupati..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>
                
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi (Opsional)</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none transition">{{ old('description') }}</textarea>
                </div>
                
                <button type="submit" class="w-full py-2 bg-blue-600 text-white font-bold rounded hover:bg-blue-700 transition shadow-md">
                    <i class="fas fa-calendar-check mr-1"></i> Simpan Agenda
                </button>
            </form>
        </div>
    </div>

    <!-- Kolom Kanan: Tabel Agenda -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-gray-800">Daftar Agenda Terjadwal</h3>
            </div>
            <div class="p-0 overflow-x-auto w-full">
        <table class="w-full text-left border-collapse min-w-[800px]">
                    <tbody class="text-sm">
                        @foreach($agendas as $agenda)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 w-32">
                                <div class="bg-brand-blue text-white text-center rounded-lg overflow-hidden shadow-sm">
                                    <div class="bg-black/20 py-1 text-xs font-bold uppercase">{{ $agenda->event_date->format('M') }}</div>
                                    <div class="text-2xl font-extrabold py-1">{{ $agenda->event_date->format('d') }}</div>
                                    <div class="bg-black/10 py-1 text-xs">{{ $agenda->event_date->format('Y') }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <h4 class="font-bold text-gray-800 text-base mb-1">{{ $agenda->title }}</h4>
                                <div class="text-gray-500 text-xs space-y-1 mb-2">
                                    <p><i class="far fa-clock w-4"></i> {{ $agenda->event_date->format('H:i') }} WIB</p>
                                    <p><i class="fas fa-map-marker-alt w-4"></i> {{ $agenda->location ?? 'Tidak ada lokasi' }}</p>
                                </div>
                                @if($agenda->description)
                                    <p class="text-sm text-gray-600 bg-gray-50 p-2 rounded border border-gray-100">{{ $agenda->description }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right align-top">
                                <form action="{{ route('admin.agendas.destroy', $agenda->id) }}" method="POST" onsubmit="return confirm('Hapus agenda ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold px-2 py-1 rounded border border-red-200 hover:bg-red-50 transition" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @if($agendas->isEmpty())
                        <tr><td colspan="3" class="px-6 py-10 text-center text-gray-500">Belum ada agenda kegiatan yang dijadwalkan.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">{{ $agendas->links() }}</div>
        </div>
    </div>
</div>
@endsection


