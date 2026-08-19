@extends('layouts.public')

@section('title', 'Struktur Organisasi - Bagian Pemerintahan')

@push('scripts')
<style>
/* CSS Tree for Organizational Chart */
.org-tree {
    display: flex;
    justify-content: center;
    overflow-x: auto;
    padding: 20px 0 40px 0;
}
.org-tree ul {
    padding-top: 20px;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}
.org-tree li {
    float: left;
    text-align: center;
    list-style-type: none;
    position: relative;
    padding: 20px 10px 0 10px;
}
.org-tree li::before, .org-tree li::after {
    content: '';
    position: absolute; top: 0; right: 50%;
    border-top: 2px solid #64748b; /* Slate-500 line */
    width: 50%; height: 20px;
}
.org-tree li::after {
    right: auto; left: 50%;
    border-left: 2px solid #64748b;
}
.org-tree li:only-child::after, .org-tree li:only-child::before {
    display: none;
}
.org-tree li:only-child { padding-top: 0; }
.org-tree li:first-child::before, .org-tree li:last-child::after {
    border: 0 none;
}
.org-tree li:last-child::before {
    border-right: 2px solid #64748b;
    border-radius: 0 5px 0 0;
}
.org-tree li:first-child::after {
    border-radius: 5px 0 0 0;
}
.org-tree ul ul::before {
    content: '';
    position: absolute; top: 0; left: 50%;
    border-left: 2px solid #64748b;
    width: 0; height: 20px;
    margin-left: -1px;
}

/* Background Pattern similar to screenshot */
.bg-wave-pattern {
    background-color: #f1f5f9;
    background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
    background-size: 20px 20px;
}
</style>
@endpush

@section('content')
<!-- Bagan Struktur Organisasi -->
<div class="bg-wave-pattern py-16 relative overflow-hidden min-h-[60vh]">
    <div class="container mx-auto px-4 relative z-10 w-full">
        
        @if(!isset($mode) || $mode == 'dynamic')
            <div class="org-tree">
                <ul>
                    @forelse($members as $member)
                        @include('frontend.partials.org_node', ['member' => $member, 'level' => 1])
                    @empty
                        <div class="text-center py-20 text-gray-500 w-full">
                            <i class="fas fa-sitemap text-6xl mb-4 text-gray-300 block"></i>
                            <p class="font-bold">Struktur organisasi belum dikonfigurasi.</p>
                        </div>
                    @endforelse
                </ul>
            </div>
        @elseif($mode == 'photo')
            @if(isset($photo) && $photo)
                <div class="w-full overflow-x-auto bg-white rounded-xl shadow-lg border border-gray-200 p-4">
                    <img src="{{ asset('storage/' . $photo) }}" alt="Struktur Organisasi" class="w-full max-w-none h-auto object-contain block mx-auto" style="min-width: 800px;">
                </div>
            @else
                <div class="text-center py-20 text-gray-500 w-full bg-white rounded-xl shadow border border-gray-200">
                    <i class="fas fa-image text-6xl mb-4 text-gray-300 block"></i>
                    <p class="font-bold">Foto Struktur organisasi belum diunggah.</p>
                </div>
            @endif
        @endif

    </div>
</div>
@endsection
