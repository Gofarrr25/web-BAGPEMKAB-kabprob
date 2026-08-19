@extends('layouts.admin')

@section('title', 'Manajemen Admin - Admin Panel')
@section('page_title', 'Manajemen Pengguna (Admin OPD)')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <h3 class="font-bold text-gray-800">Daftar Admin OPD Terdaftar</h3>
        <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm transition">
            <i class="fas fa-plus mr-1"></i> Tambah Admin Baru
        </a>
    </div>
    
    <div class="overflow-x-auto w-full">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-white border-b border-gray-100 text-sm text-gray-500 uppercase tracking-wider whitespace-nowrap">
                    <th class="px-6 py-4 font-semibold">Nama Lengkap</th>
                    <th class="px-6 py-4 font-semibold">Username / Email</th>
                    <th class="px-6 py-4 font-semibold">Role</th>
                    <th class="px-6 py-4 font-semibold text-center">Status Akses</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($users as $user)
                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 font-bold text-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            {{ $user->name }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-700">{{ $user->username }}</p>
                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-bold">
                            {{ $user->roles->pluck('name')->first() ?? 'Admin' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($user->is_active)
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold"><i class="fas fa-check-circle mr-1"></i> Aktif</span>
                        @else
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold"><i class="fas fa-ban mr-1"></i> Diblokir</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <!-- Tombol Edit Admin & Ubah Password -->
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-800 font-bold px-2 py-1.5 rounded border border-blue-200 hover:bg-blue-50 transition inline-block text-xs" title="Edit Admin & Ubah Password">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>

                        <!-- Tombol Toggle Status -->
                        <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="text-{{ $user->is_active ? 'orange' : 'green' }}-500 hover:text-{{ $user->is_active ? 'orange' : 'green' }}-700 font-bold px-2 py-1 rounded border border-{{ $user->is_active ? 'orange' : 'green' }}-200 hover:bg-{{ $user->is_active ? 'orange' : 'green' }}-50 transition" title="{{ $user->is_active ? 'Blokir Akun' : 'Aktifkan Akun' }}">
                                <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                            </button>
                        </form>
                        
                        <!-- Tombol Hapus -->
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus admin ini secara permanen?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold px-2 py-1 rounded border border-red-200 hover:bg-red-50 transition" title="Hapus Permanen">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
                
                @if($users->isEmpty())
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada admin OPD yang terdaftar selain Anda.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection



