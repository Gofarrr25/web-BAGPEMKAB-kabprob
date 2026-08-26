@extends('layouts.admin')

@section('title', 'Riwayat Aktivitas Konten - Admin Panel')
@section('page_title', 'Riwayat Aktivitas Konten')

@section('content')
<div class="space-y-6">

    <!-- Overview & Filters -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="font-extrabold text-gray-900 text-lg uppercase tracking-tight flex items-center gap-2">
                    <i class="fas fa-history text-blue-600"></i> Riwayat Aktivitas Konten
                </h3>
                <p class="text-xs text-gray-500 mt-1">Pantau seluruh riwayat aktivitas yang berkaitan dengan pengelolaan konten website.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.content-activities.index') }}" class="bg-gray-50/70 p-4 rounded-xl border border-gray-100 flex flex-col gap-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1.5">Pencarian</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul konten, nama, dll..." class="w-full pl-8 pr-3 py-1.5 bg-white border border-gray-200 rounded-lg outline-none focus:border-blue-500 text-xs transition shadow-sm">
                    </div>
                </div>

                <!-- Modul -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1.5">Modul/Konten</label>
                    <select name="module" class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg outline-none focus:border-blue-500 text-xs transition shadow-sm">
                        <option value="">Semua Modul</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>{{ $module }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Aktivitas -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1.5">Jenis Aktivitas</label>
                    <select name="type" class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg outline-none focus:border-blue-500 text-xs transition shadow-sm">
                        <option value="">Semua Jenis</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end justify-end gap-3 h-full">
                    <a href="{{ route('admin.content-activities.index') }}" class="px-4 py-1.5 text-xs font-bold text-gray-500 hover:text-red-600 hover:bg-red-50 border border-transparent hover:border-red-100 rounded-lg transition h-[34px] flex items-center">Reset</a>
                    <button type="submit" class="px-5 py-1.5 bg-gray-900 hover:bg-black text-white rounded-lg text-xs font-bold shadow-md transition flex items-center gap-2 h-[34px]">
                        <i class="fas fa-filter text-[10px]"></i> Terapkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative w-full">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-[10px] uppercase tracking-wider text-gray-500">
                        <th class="px-5 py-3.5 font-extrabold rounded-tl-xl w-12 text-center">No</th>
                        <th class="px-5 py-3.5 font-extrabold w-48">Tanggal / Waktu</th>
                        <th class="px-5 py-3.5 font-extrabold">Modul / Konten</th>
                        <th class="px-5 py-3.5 font-extrabold w-48">Aktivitas</th>
                        <th class="px-5 py-3.5 font-extrabold w-48 rounded-tr-xl">Penulis / Pelaksana</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logs as $index => $log)
                    @php
                        $props = $log->properties;
                        $type = $props['type'] ?? 'Aktivitas';
                        $module = $props['module'] ?? 'Sistem';
                    @endphp
                    <tr class="hover:bg-blue-50/30 transition">
                        <!-- No -->
                        <td class="px-5 py-4 text-center">
                            <span class="text-xs font-bold text-gray-400">{{ $logs->firstItem() + $index }}</span>
                        </td>

                        <!-- Tanggal & Waktu -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="font-bold text-gray-800 block text-sm">{{ $log->created_at->format('d M Y') }}</span>
                            <span class="text-xs text-gray-500 font-mono flex items-center gap-1 mt-0.5"><i class="far fa-clock"></i> {{ $log->created_at->format('H:i') }}</span>
                        </td>

                        <!-- Modul & Konten -->
                        <td class="px-5 py-4 min-w-[250px]">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                        {{ $module }}
                                    </span>
                                </div>
                                <div class="text-[13px] font-bold text-gray-800 break-words leading-relaxed" title="{{ $log->description }}">
                                    {{ $log->description }}
                                </div>
                            </div>
                        </td>

                        <!-- Aktivitas -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="font-bold text-[13px] text-blue-700 bg-blue-50 px-2.5 py-1 rounded-md border border-blue-100">
                                {{ $type }}
                            </span>
                        </td>

                        <!-- Penulis / Pelaksana -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center font-bold text-xs shadow-sm ring-2 ring-white">
                                    {{ substr($log->causer ? $log->causer->name : ($props['username'] ?? 'G'), 0, 1) }}
                                </div>
                                <div>
                                    @if($log->causer)
                                        <div class="font-bold text-gray-900 text-[13px]">{{ $log->causer->name }}</div>
                                    @else
                                        <div class="font-bold text-gray-600 text-[13px]">{{ $props['username'] ?? 'Guest' }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-history text-4xl mb-3 block opacity-20"></i>
                            <p class="font-bold text-gray-500">Tidak ada riwayat aktivitas konten yang ditemukan.</p>
                            <p class="text-xs mt-1">Belum ada aktivitas pengelolaan konten atau coba sesuaikan filter pencarian Anda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
            {{ $logs->links() }}
        </div>
    </div>
</div>

@endsection
