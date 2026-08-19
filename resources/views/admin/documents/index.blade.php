@extends('layouts.admin')

@section('title', 'Dokumen PPID / Arsip ZIP - Admin Panel')
@section('page_title', 'Manajemen Dokumen PPID & Arsip Berkas')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <div>
            <h3 class="font-bold text-gray-800">Daftar Dokumen Information & Paket Berkas ZIP</h3>
            <p class="text-xs text-gray-500">Mendukung format .PDF, .ZIP, .RAR, .7Z, .DOCX, .XLSX hingga 50MB.</p>
        </div>
        <a href="{{ route('admin.documents.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition shadow-sm flex items-center gap-1.5">
            <i class="fas fa-upload"></i> Unggah Dokumen / Zip Baru
        </a>
    </div>
    
    <div class="overflow-x-auto w-full">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-white border-b border-gray-100 text-sm text-gray-500 uppercase tracking-wider whitespace-nowrap">
                    <th class="px-6 py-4 font-semibold">Judul Dokumen / Arsip</th>
                    <th class="px-6 py-4 font-semibold">Kategori / Klasifikasi</th>
                    <th class="px-6 py-4 font-semibold">Pengunggah</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($documents as $doc)
                @php
                    $ext = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                @endphp
                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="text-2xl">
                                @if(in_array($ext, ['zip', 'rar', '7z']))
                                    <i class="fas fa-file-archive text-amber-500"></i>
                                @elseif(in_array($ext, ['doc', 'docx']))
                                    <i class="fas fa-file-word text-blue-500"></i>
                                @elseif(in_array($ext, ['xls', 'xlsx']))
                                    <i class="fas fa-file-excel text-green-500"></i>
                                @else
                                    <i class="fas fa-file-pdf text-red-500"></i>
                                @endif
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">{{ $doc->title }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-500"><i class="far fa-clock mr-1"></i>{{ $doc->created_at->format('d M Y, H:i') }}</span>
                                    <span class="text-[10px] font-mono font-bold uppercase bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">.{{ $ext }}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold border border-blue-100">
                            {{ $doc->category }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600 font-semibold">{{ $doc->user->name ?? 'Admin' }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" download class="text-blue-600 hover:text-blue-800 font-bold px-3 py-1.5 rounded-lg border border-blue-200 hover:bg-blue-50 transition text-xs inline-flex items-center gap-1" title="Unduh File">
                            <i class="fas fa-download"></i> Download {{ strtoupper($ext) }}
                        </a>
                        <form action="{{ route('admin.documents.destroy', $doc->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus dokumen/arsip ini secara permanen?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold px-2 py-1.5 rounded-lg border border-red-200 hover:bg-red-50 transition text-xs" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
                
                @if($documents->isEmpty())
                <tr><td colspan="4" class="px-6 py-10 text-center text-gray-500">Belum ada dokumen PPID atau berkas ZIP yang diunggah.</td></tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">{{ $documents->links() }}</div>
</div>
@endsection



