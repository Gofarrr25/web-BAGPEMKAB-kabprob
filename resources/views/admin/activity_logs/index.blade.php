@extends('layouts.admin')

@section('title', 'Log Aktivitas Sistem - Admin Panel')
@section('page_title', 'Log Aktivitas Sistem')

@section('content')
<div class="space-y-6">

    <!-- Overview & Filters -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="font-extrabold text-gray-900 text-lg uppercase tracking-tight flex items-center gap-2">
                    <i class="fas fa-history text-blue-600"></i> Log Aktivitas Sistem
                </h3>
                <p class="text-xs text-gray-500 mt-1">Pantau seluruh aktivitas yang terjadi di dalam sistem oleh Admin dan Super Admin. Fitur ini merekam secara otomatis jejak keamanan dan perubahan data.</p>
            </div>
            
            @if(auth()->user()->hasRole('Superadmin'))
            <div>
                <form action="{{ route('admin.activity-logs.purge-old') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus permanen {{ $oldLogsCount }} log aktivitas yang berusia lebih dari 3 bulan?\n\nTindakan ini tidak dapat dibatalkan.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" @if($oldLogsCount == 0) disabled @endif class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 disabled:opacity-50 disabled:cursor-not-allowed border border-red-200 rounded-lg text-xs font-bold transition flex items-center gap-2 shadow-sm">
                        <i class="fas fa-trash-alt"></i> Bersihkan Log Lama (> 3 Bulan)
                        @if($oldLogsCount > 0)
                            <span class="bg-red-600 text-white px-2 py-0.5 rounded-full text-[10px]">{{ $oldLogsCount }}</span>
                        @endif
                    </button>
                </form>
            </div>
            @endif
        </div>

        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="bg-gray-50/70 p-4 rounded-xl border border-gray-100 flex flex-col gap-4">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1.5">Pencarian Universal</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="IP, URL, User, Deskripsi..." class="w-full pl-8 pr-3 py-1.5 bg-white border border-gray-200 rounded-lg outline-none focus:border-blue-500 text-xs transition shadow-sm">
                    </div>
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1.5">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg outline-none focus:border-blue-500 text-xs transition shadow-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg outline-none focus:border-blue-500 text-xs transition shadow-sm">
                </div>

                <!-- Filter Role -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1.5">Filter Role</label>
                    <select name="role" class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg outline-none focus:border-blue-500 text-xs transition shadow-sm">
                        <option value="">Semua Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ $role }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1.5">Filter Status HTTP</label>
                    <select name="status" class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg outline-none focus:border-blue-500 text-xs transition shadow-sm">
                        <option value="">Semua Status</option>
                        <option value="200" {{ request('status') == '200' ? 'selected' : '' }}>200 OK</option>
                        <option value="201" {{ request('status') == '201' ? 'selected' : '' }}>201 Created</option>
                        <option value="302" {{ request('status') == '302' ? 'selected' : '' }}>302 Redirect</option>
                        <option value="403" {{ request('status') == '403' ? 'selected' : '' }}>403 Forbidden</option>
                        <option value="404" {{ request('status') == '404' ? 'selected' : '' }}>404 Not Found</option>
                        <option value="500" {{ request('status') == '500' ? 'selected' : '' }}>500 Error</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Filter User -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1.5">Filter Pengguna</label>
                    <select name="user_id" class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg outline-none focus:border-blue-500 text-xs transition shadow-sm">
                        <option value="">Semua Pengguna</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Aktivitas -->
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
                    <a href="{{ route('admin.activity-logs.index') }}" class="px-4 py-1.5 text-xs font-bold text-gray-500 hover:text-red-600 hover:bg-red-50 border border-transparent hover:border-red-100 rounded-lg transition h-[34px] flex items-center">Reset</a>
                    <button type="submit" class="px-5 py-1.5 bg-gray-900 hover:bg-black text-white rounded-lg text-xs font-bold shadow-md transition flex items-center gap-2 h-[34px]">
                        <i class="fas fa-filter text-[10px]"></i> Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative w-full">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[1200px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        <th class="px-5 py-4 w-12 text-center">#</th>
                        <th class="px-5 py-4">Waktu</th>
                        <th class="px-5 py-4 min-w-[180px]">User & Role</th>
                        <th class="px-5 py-4 min-w-[250px]">Modul & Aktivitas</th>
                        <th class="px-5 py-4">IP Address</th>
                        <th class="px-5 py-4">Browser/Client</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-5 py-4 text-right sticky right-0 bg-gray-50 border-l border-gray-100 shadow-[-4px_0_6px_-1px_rgba(0,0,0,0.05)]">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($logs as $index => $log)
                        @php
                            $props = $log->properties;
                            $type = $props['type'] ?? 'Aktivitas';
                            $status = $props['status'] ?? 200;
                            
                            // Pewarnaan baris khusus untuk aktivitas mencurigakan
                            $isWarning = in_array($status, [401, 403, 404, 500]) || in_array($type, ['Login Gagal', 'Akses Ditolak', 'Error Server']);
                            
                            $statusColor = 'bg-green-100 text-green-700 border-green-200';
                            if ($status >= 300 && $status < 400) $statusColor = 'bg-blue-100 text-blue-700 border-blue-200';
                            if ($status >= 400 && $status < 500) $statusColor = 'bg-orange-100 text-orange-700 border-orange-200';
                            if ($status >= 500) $statusColor = 'bg-red-100 text-red-700 border-red-200';
                            if ($type === 'Login Gagal') $statusColor = 'bg-red-100 text-red-700 border-red-200';
                        @endphp
                    <tr class="group transition {{ $isWarning ? 'bg-red-50/40 hover:bg-red-50/80' : 'hover:bg-blue-50/30' }}">
                        <td class="px-5 py-4 text-center font-mono text-xs text-gray-500">
                            {{ $logs->firstItem() + $index }}
                        </td>
                        
                        <!-- Waktu -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="font-bold text-gray-800 block">{{ $log->created_at->format('d M Y') }}</span>
                            <span class="text-xs text-gray-500 font-mono flex items-center gap-1 mt-0.5"><i class="far fa-clock"></i> {{ $log->created_at->format('H:i:s') }}</span>
                        </td>
                        
                        <!-- User & Role -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    {{ substr($log->causer ? $log->causer->name : ($props['username'] ?? 'G'), 0, 1) }}
                                </div>
                                <div>
                                    @if($log->causer)
                                        <div class="font-bold text-gray-900 text-[13px]">{{ $log->causer->name }}</div>
                                    @else
                                        <div class="font-bold text-gray-600 text-[13px]">{{ $props['username'] ?? 'Guest' }}</div>
                                    @endif
                                    <div class="text-[9px] uppercase font-bold text-blue-600 mt-0.5 tracking-wider bg-blue-50 inline-block px-1.5 py-0.5 rounded">{{ $props['role'] ?? 'Unknown' }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Modul & Aktivitas -->
                        <td class="px-5 py-4 min-w-[250px]">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2 whitespace-nowrap">
                                    @php
                                        $level = $props['level'] ?? 'INFO';
                                        $levelColor = 'bg-blue-100 text-blue-700 border-blue-200';
                                        if ($level === 'WARNING') $levelColor = 'bg-orange-100 text-orange-700 border-orange-200';
                                        if ($level === 'CRITICAL') $levelColor = 'bg-red-100 text-red-700 border-red-200';
                                    @endphp
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold border {{ $levelColor }} uppercase tracking-wider">
                                        {{ $level }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                        {{ $props['module'] ?? 'Sistem' }}
                                    </span>
                                    <span class="font-bold text-[13px] {{ $isWarning ? 'text-red-700' : 'text-gray-900' }}">{{ $type }}</span>
                                </div>
                                <div class="text-[11px] text-gray-500 max-w-xs break-words leading-relaxed" title="{{ $log->description }}">
                                    {{ $log->description }}
                                </div>
                            </div>
                        </td>

                        <!-- IP Address -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="font-mono text-[11px] font-bold text-gray-700 bg-gray-100 px-2 py-1 rounded inline-flex items-center gap-1.5 border border-gray-200">
                                <i class="fas fa-network-wired text-gray-400"></i> {{ $props['ip'] ?? 'Unknown' }}
                            </div>
                        </td>

                        <!-- Browser/Client -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-[11px] text-gray-600 font-semibold flex items-center gap-1.5">
                                <i class="fab fa-chrome text-gray-400 text-sm"></i> {{ $props['browser'] ?? 'Unknown Browser' }}
                            </span>
                        </td>

                        <!-- Status -->
                        <td class="px-5 py-4 text-center whitespace-nowrap">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <span class="inline-block px-2.5 py-1 rounded-md text-[10px] font-bold border {{ $statusColor }} font-mono shadow-sm">
                                    {{ $status }}
                                </span>
                                <span class="text-[9px] font-bold text-gray-400 tracking-widest font-mono">{{ $props['method'] ?? 'GET' }}</span>
                            </div>
                        </td>

                        <!-- Detail Button -->
                        <td class="px-5 py-4 text-right sticky right-0 {{ $isWarning ? 'bg-[#fef2f2] group-hover:bg-[#fee2e2]' : 'bg-white group-hover:bg-[#eff6ff]' }} border-l border-gray-100 shadow-[-4px_0_6px_-1px_rgba(0,0,0,0.02)] transition-colors duration-200 z-10">
                            <button onclick="openDetailModal('{{ $log->id }}')" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-lg text-xs font-bold transition shadow-sm flex items-center gap-1.5 ml-auto">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-history text-4xl mb-3 block opacity-20"></i>
                            <p class="font-bold text-gray-500">Tidak ada log aktivitas yang ditemukan.</p>
                            <p class="text-xs mt-1">Coba sesuaikan filter pencarian Anda.</p>
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

<!-- Modal Detail Log -->
<div id="detailModal" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-3xl w-full shadow-2xl flex flex-col overflow-hidden max-h-[90vh]">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-extrabold text-gray-800 flex items-center gap-2">
                <i class="fas fa-search-location text-blue-600"></i> Detail Aktivitas
            </h3>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-red-500 transition"><i class="fas fa-times text-xl"></i></button>
        </div>
        
        <div class="p-6 overflow-y-auto" id="detailModalContent">
            <div class="flex justify-center py-8"><i class="fas fa-circle-notch fa-spin text-3xl text-blue-500"></i></div>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
            <button onclick="closeDetailModal()" class="px-5 py-2 bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-lg text-sm font-bold transition shadow-sm">Tutup</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openDetailModal(id) {
        document.getElementById('detailModal').classList.remove('hidden');
        document.getElementById('detailModal').classList.add('flex');
        
        fetch(`/admin/activity-logs/${id}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('detailModalContent').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('detailModalContent').innerHTML = '<div class="text-red-500 text-center py-8"><i class="fas fa-exclamation-triangle text-3xl mb-2 block"></i> Gagal memuat detail log.</div>';
            });
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
        document.getElementById('detailModal').classList.remove('flex');
        setTimeout(() => {
            document.getElementById('detailModalContent').innerHTML = '<div class="flex justify-center py-8"><i class="fas fa-circle-notch fa-spin text-3xl text-blue-500"></i></div>';
        }, 300);
    }
</script>
@endpush
@endsection
