@extends('layouts.admin')

@section('title', 'Dashboard - Admin Panel')
@section('page_title', 'Dashboard Utama')

@section('content')
    <!-- Alert Khusus Welcome -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-400 text-white p-4 md:p-6 rounded-xl shadow-md mb-8 relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name ?? 'Administrator' }}! 👋</h2>
            <p class="text-brand-blue-pale mb-4">Anda login sebagai <span class="font-bold bg-white/20 px-2 py-1 rounded">{{ auth()->user()->roles->pluck('name')->first() ?? 'Admin' }}</span>. Kelola seluruh konten Portal Resmi Pemerintah Kabupaten Probolinggo dengan bijak dan profesional.</p>
            <p class="text-sm text-brand-blue-pale"><i class="fas fa-info-circle"></i> Gunakan menu di sidebar kiri untuk mengakses modul-modul sistem.</p>
        </div>
        <!-- Dekorasi Background -->
        <i class="fas fa-chart-pie absolute -bottom-10 -right-10 text-9xl text-white opacity-10"></i>
    </div>



    <!-- Statistik Utama -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Card 1 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Berita</p>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $totalPosts }}</h3>
                <p class="text-xs text-green-500 font-semibold mt-2"><i class="fas fa-newspaper"></i> Terpublikasi</p>
            </div>
            <div class="w-14 h-14 bg-brand-blue-light text-brand-blue rounded-full flex items-center justify-center text-2xl">
                <i class="fas fa-newspaper"></i>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Dokumen PPID</p>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $totalDocuments }}</h3>
                <p class="text-xs text-green-500 font-semibold mt-2"><i class="fas fa-file-pdf"></i> Tersimpan</p>
            </div>
            <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center text-2xl">
                <i class="fas fa-file-pdf"></i>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Galeri Foto</p>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $totalGalleries }}</h3>
                <p class="text-xs text-gray-400 font-semibold mt-2">Media</p>
            </div>
            <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center text-2xl">
                <i class="fas fa-images"></i>
            </div>
        </div>
        
        <!-- Card 4 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Video</p>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $totalVideos }}</h3>
                <p class="text-xs text-gray-400 font-semibold mt-2">Media Terpublikasi</p>
            </div>
            <div class="w-14 h-14 bg-red-50 text-red-600 rounded-full flex items-center justify-center text-2xl">
                <i class="fas fa-video"></i>
            </div>
        </div>

    </div>

    @role('Superadmin')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 5 (Superadmin) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Admin</p>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $totalAdmins }}</h3>
                <a href="{{ route('admin.users.index') }}" class="text-xs text-brand-blue hover:text-brand-blue-hover font-semibold mt-2 inline-block">Kelola Admin &rarr;</a>
            </div>
            <div class="w-14 h-14 bg-brand-green/10 text-brand-green rounded-full flex items-center justify-center text-2xl">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>

        <!-- Card 6 (Superadmin) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Staf</p>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $totalStafs }}</h3>
                <a href="{{ route('admin.users.index') }}" class="text-xs text-brand-blue hover:text-brand-blue-hover font-semibold mt-2 inline-block">Kelola Staf &rarr;</a>
            </div>
            <div class="w-14 h-14 bg-teal-50 text-teal-600 rounded-full flex items-center justify-center text-2xl">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    @endrole

    <!-- Aktivitas Terbaru -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Aktivitas Konten Terbaru</h3>
            @if(Auth::user()->hasRole('Superadmin'))
                <a href="{{ route('admin.content-activities.index') }}" class="text-sm text-brand-blue font-semibold hover:text-brand-blue-hover">Lihat Semua</a>
            @else
                <a href="{{ route('admin.content-activities.index') }}" class="text-sm text-brand-blue font-semibold hover:text-brand-blue-hover">Lihat Semua</a>
            @endif
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[500px]">
                <thead>
                    <tr class="border-b border-gray-100 text-sm text-gray-500 whitespace-nowrap">
                        <th class="px-6 py-3 font-semibold whitespace-nowrap">Judul Konten / Aktivitas</th>
                        <th class="px-6 py-3 font-semibold whitespace-nowrap">Tipe</th>
                        <th class="px-6 py-3 font-semibold whitespace-nowrap">Aksi</th>
                        <th class="px-6 py-3 font-semibold whitespace-nowrap">Penulis</th>
                        <th class="px-6 py-3 font-semibold whitespace-nowrap">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($recentActivities as $activity)
                        @php
                            // Get title dynamically
                            $model = $activity->subject;
                            $title = $model->title ?? $model->name ?? $activity->properties['attributes']['title'] ?? $activity->properties['attributes']['name'] ?? $activity->description;
                            
                            // Map type
                            $type = class_basename($activity->subject_type);
                            $typeMap = [
                                'Post' => 'Berita',
                                'Page' => 'Halaman',
                                'Document' => 'Dokumen',
                                'Gallery' => 'Galeri',
                                'Video' => 'Video',
                                'Banner' => 'Banner',
                                'Agenda' => 'Agenda',
                                'Category' => 'Kategori',
                                'User' => 'Pengguna',
                                'Setting' => 'Pengaturan',
                                'OrganizationMember' => 'Struktur',
                                'InstagramPost' => 'Instagram',
                                'RelatedLink' => 'Link Terkait',
                            ];
                            $type = $typeMap[$type] ?? $type;
                            
                            // Action colors
                            $event = $activity->event;
                            $color = 'blue';
                            if ($event == 'created') $color = 'green';
                            elseif ($event == 'updated') $color = 'yellow';
                            elseif ($event == 'deleted') $color = 'red';
                            
                            $eventMap = [
                                'created' => 'Tambah',
                                'updated' => 'Edit',
                                'deleted' => 'Hapus',
                            ];
                            $event = $eventMap[$event] ?? $event;
                        @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">{{ Str::limit($title, 40) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap"><span class="bg-gray-100 text-gray-700 border border-gray-200 px-2 py-1 rounded text-xs font-bold">{{ $type }}</span></td>
                        <td class="px-6 py-4 whitespace-nowrap"><span class="bg-{{ $color }}-100 text-{{ $color }}-700 px-2 py-1 rounded text-xs font-bold">{{ $event }}</span></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-brand-blue-light text-brand-blue flex items-center justify-center font-bold text-[10px]">
                                    {{ substr($activity->causer->name ?? 'S', 0, 1) }}
                                </div>
                                {{ $activity->causer->name ?? 'Sistem' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ $activity->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">Belum ada aktivitas konten terbaru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
