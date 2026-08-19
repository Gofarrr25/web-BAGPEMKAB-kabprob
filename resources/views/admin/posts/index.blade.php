@extends('layouts.admin')

@section('title', 'Manajemen Berita - Admin Panel')
@section('page_title', 'Manajemen Berita & Artikel')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <h3 class="font-bold text-gray-800">Daftar Berita Instansi</h3>
        <a href="{{ route('admin.posts.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm transition shadow-sm">
            <i class="fas fa-edit mr-1"></i> Tulis Berita Baru
        </a>
    </div>
    
    <div class="overflow-x-auto w-full">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-white border-b border-gray-100 text-sm text-gray-500 uppercase tracking-wider whitespace-nowrap">
                    <th class="px-6 py-4 font-semibold w-24">Thumbnail</th>
                    <th class="px-6 py-4 font-semibold">Judul Berita</th>
                    <th class="px-6 py-4 font-semibold">Kategori</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($posts as $post)
                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4">
                        @if($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" class="w-16 h-12 object-cover rounded shadow-sm" alt="Thumbnail">
                        @else
                            <div class="w-16 h-12 bg-gray-200 rounded flex items-center justify-center text-gray-400">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-800 mb-1">{{ $post->title }}</p>
                        <p class="text-xs text-gray-500"><i class="fas fa-user mr-1"></i> {{ $post->user->name }} &bull; <i class="fas fa-clock mr-1"></i> {{ $post->created_at->format('d M Y') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-semibold border border-gray-200">
                            {{ $post->category->name ?? 'Uncategorized' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($post->is_published)
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Publik</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-bold">Draft</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-blue-500 hover:text-blue-700 font-bold px-2 py-1 rounded border border-blue-200 hover:bg-blue-50 transition" title="Edit">
                            <i class="fas fa-pen"></i>
                        </a>
                        <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus berita ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold px-2 py-1 rounded border border-red-200 hover:bg-red-50 transition" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
                
                @if($posts->isEmpty())
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada berita yang diterbitkan.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $posts->links() }}
    </div>
</div>
@endsection



