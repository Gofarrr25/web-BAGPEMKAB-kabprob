@extends('layouts.admin')

@section('title', 'Dashboard - Admin Panel')
@section('page_title', 'Dashboard Utama')

@section('content')
    <!-- Alert Khusus Welcome -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-400 text-white p-6 rounded-xl shadow-md mb-8 relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name ?? 'Administrator' }}! 👋</h2>
            <p class="text-blue-100 mb-4">Anda login sebagai <span class="font-bold bg-white/20 px-2 py-1 rounded">{{ auth()->user()->roles->pluck('name')->first() ?? 'Admin' }}</span>. Kelola seluruh konten Portal Resmi Pemerintah Kabupaten Probolinggo dengan bijak dan profesional.</p>
            <p class="text-sm text-blue-200"><i class="fas fa-info-circle"></i> Gunakan menu di sidebar kiri untuk mengakses modul-modul sistem.</p>
        </div>
        <!-- Dekorasi Background -->
        <i class="fas fa-chart-pie absolute -bottom-10 -right-10 text-9xl text-white opacity-10"></i>
    </div>

    <!-- Statistik Utama (Superadmin / Admin) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Card 1 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Berita</p>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ \App\Models\Post::count() }}</h3>
                <p class="text-xs text-green-500 font-semibold mt-2"><i class="fas fa-arrow-up"></i> +12 Bulan ini</p>
            </div>
            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-2xl">
                <i class="fas fa-newspaper"></i>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Dokumen PPID</p>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ \App\Models\Document::count() }}</h3>
                <p class="text-xs text-green-500 font-semibold mt-2"><i class="fas fa-arrow-up"></i> Publik</p>
            </div>
            <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center text-2xl">
                <i class="fas fa-file-pdf"></i>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Galeri Foto/Video</p>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ \App\Models\Gallery::count() }}</h3>
                <p class="text-xs text-gray-400 font-semibold mt-2">Media Tersimpan</p>
            </div>
            <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center text-2xl">
                <i class="fas fa-images"></i>
            </div>
        </div>

        <!-- Card 4 (Khusus Superadmin) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Admin</p>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ \App\Models\User::count() }}</h3>
                @role('Superadmin')
                <a href="#" class="text-xs text-blue-500 hover:text-blue-700 font-semibold mt-2 inline-block">Kelola Pengguna &rarr;</a>
                @else
                <p class="text-xs text-gray-400 font-semibold mt-2">Akun Terdaftar</p>
                @endrole
            </div>
            <div class="w-14 h-14 bg-brand-green/10 text-brand-green rounded-full flex items-center justify-center text-2xl">
                <i class="fas fa-users"></i>
            </div>
        </div>

    </div>

    <!-- Aktivitas Terbaru -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Aktivitas Konten Terbaru</h3>
            <button class="text-sm text-blue-600 font-semibold hover:text-blue-800">Lihat Semua</button>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[500px]">
                <thead>
                    <tr class="border-b border-gray-100 text-sm text-gray-500 whitespace-nowrap">
                        <th class="px-6 py-3 font-semibold">Judul Konten</th>
                        <th class="px-6 py-3 font-semibold">Tipe</th>
                        <th class="px-6 py-3 font-semibold">Penulis</th>
                        <th class="px-6 py-3 font-semibold">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse(\App\Models\Post::latest()->take(5)->get() as $post)
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">{{ $post->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap"><span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold">Berita</span></td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $post->user->name ?? 'Admin' }}</td>
                        <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ $post->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">Belum ada aktivitas konten terbaru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
