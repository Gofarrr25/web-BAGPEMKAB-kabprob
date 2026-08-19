@php
    $props = $log->properties;
    $type = $props['type'] ?? 'Aktivitas';
    $status = $props['status'] ?? 200;
    $level = $props['level'] ?? 'INFO';
    $payload = $props['payload'] ?? null;
    
    $isWarning = in_array($status, [401, 403, 404, 500]) || in_array($type, ['Login Gagal', 'Akses Ditolak', 'Error Server']);
    $statusColor = 'text-green-600 bg-green-50 border-green-200';
    if ($status >= 300 && $status < 400) $statusColor = 'text-blue-600 bg-blue-50 border-blue-200';
    if ($status >= 400 && $status < 500) $statusColor = 'text-orange-600 bg-orange-50 border-orange-200';
    if ($status >= 500) $statusColor = 'text-red-600 bg-red-50 border-red-200';
    if ($type === 'Login Gagal' || $isWarning) $statusColor = 'text-red-600 bg-red-50 border-red-200';

    $levelColor = 'text-blue-600 bg-blue-50 border-blue-200';
    if ($level === 'WARNING') $levelColor = 'text-orange-600 bg-orange-50 border-orange-200';
    if ($level === 'CRITICAL') $levelColor = 'text-red-600 bg-red-50 border-red-200';
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Kolom Kiri -->
    <div class="space-y-4">
        <!-- Info Pengguna -->
        <div>
            <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Informasi Pengguna</h4>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 space-y-3">
                <div class="flex justify-between border-b border-gray-200 pb-2">
                    <span class="text-xs text-gray-500">Nama Pengguna</span>
                    <span class="text-xs font-bold text-gray-800">{{ $log->causer ? $log->causer->name : ($props['username'] ?? 'Guest') }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-200 pb-2">
                    <span class="text-xs text-gray-500">Email / ID</span>
                    <span class="text-xs font-bold text-gray-800">{{ $log->causer ? $log->causer->email : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Role Sistem</span>
                    <span class="text-[10px] font-extrabold text-blue-600 bg-blue-50 px-2 py-0.5 rounded uppercase">{{ $props['role'] ?? 'Unknown' }}</span>
                </div>
            </div>
        </div>

        <!-- Info Klien -->
        <div>
            <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Jejak Perangkat (Client)</h4>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 space-y-3">
                <div class="flex justify-between border-b border-gray-200 pb-2">
                    <span class="text-xs text-gray-500">IP Address</span>
                    <span class="text-xs font-mono font-bold text-gray-800 bg-white px-2 py-0.5 rounded border border-gray-200">{{ $props['ip'] ?? 'Unknown' }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-200 pb-2">
                    <span class="text-xs text-gray-500">Browser</span>
                    <span class="text-xs font-bold text-gray-800 flex items-center gap-1.5"><i class="fab fa-chrome text-gray-400"></i> {{ $props['browser'] ?? 'Unknown Browser' }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-200 pb-2">
                    <span class="text-xs text-gray-500">Operating System</span>
                    <span class="text-xs font-bold text-gray-800 flex items-center gap-1.5"><i class="fab fa-windows text-gray-400"></i> {{ $props['os'] ?? 'Unknown OS' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-gray-500 block mb-1">User Agent Lengkap:</span>
                    <div class="p-2 bg-white rounded border border-gray-200 text-[10px] font-mono text-gray-600 break-all h-16 overflow-y-auto leading-relaxed">
                        {{ $props['user_agent'] ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan -->
    <div class="space-y-4">
        <!-- Info HTTP -->
        <div>
            <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Detail Request & Endpoint</h4>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 space-y-3">
                <div class="flex justify-between border-b border-gray-200 pb-2">
                    <span class="text-xs text-gray-500">Waktu (Server)</span>
                    <span class="text-xs font-bold text-gray-800">{{ $log->created_at->format('d M Y - H:i:s') }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-200 pb-2">
                    <span class="text-xs text-gray-500">Modul Target</span>
                    <span class="text-xs font-bold text-gray-800 bg-gray-200 px-2 py-0.5 rounded">{{ $props['module'] ?? 'Sistem' }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-200 pb-2 items-center">
                    <span class="text-xs text-gray-500">Metode & Status HTTP</span>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-gray-200 text-gray-800 font-mono text-[10px] font-bold rounded">{{ $props['method'] ?? 'GET' }}</span>
                        <span class="px-2 py-0.5 font-mono text-[10px] font-bold rounded border {{ $statusColor }}">{{ $status }}</span>
                    </div>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-gray-500 block mb-1">URL / Route yang Diakses:</span>
                    <div class="p-2 bg-white rounded border border-gray-200 text-[10px] font-mono text-blue-600 break-all">
                        {{ $props['url'] ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Aktivitas -->
        <div>
            <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Tindakan Sistem</h4>
            <div class="bg-{{ $isWarning ? 'red' : 'blue' }}-50 rounded-xl p-4 border border-{{ $isWarning ? 'red' : 'blue' }}-100 h-full space-y-3">
                <div class="flex justify-between border-b border-{{ $isWarning ? 'red' : 'blue' }}-200 pb-2 items-center">
                    <span class="text-xs text-gray-600">Level & Jenis</span>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 border font-bold text-[9px] rounded uppercase {{ $levelColor }}">{{ $level }}</span>
                        <span class="font-bold text-xs {{ $isWarning ? 'text-red-700' : 'text-blue-700' }}">{{ $type }}</span>
                    </div>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-gray-500 block mb-1">Deskripsi Aktivitas:</span>
                    <div class="flex items-start gap-2">
                        <i class="fas {{ $isWarning ? 'fa-exclamation-triangle text-red-500' : 'fa-info-circle text-blue-500' }} mt-0.5 text-sm"></i>
                        <p class="text-[13px] font-semibold text-gray-800 leading-relaxed">{{ $log->description }}</p>
                    </div>
                </div>
                
                @if($payload && count($payload) > 0)
                <div class="mt-3 pt-3 border-t border-{{ $isWarning ? 'red' : 'blue' }}-200">
                    <span class="text-[10px] font-bold text-gray-500 block mb-1 flex items-center justify-between">
                        Data Terkirim (Payload):
                    </span>
                    <div class="p-2 bg-white rounded border border-gray-200 text-[10px] font-mono text-gray-700 max-h-32 overflow-y-auto w-full break-all whitespace-pre-wrap">
@php
    try {
        echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    } catch (\Exception $e) {
        echo 'Data tidak dapat ditampilkan.';
    }
@endphp
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
