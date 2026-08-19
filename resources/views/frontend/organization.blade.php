@extends('layouts.public')

@section('title', 'Struktur Organisasi')

@section('content')
<!-- Page Header Start -->
<div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
    <div class="container py-5">
        <h1 class="display-3 text-white mb-4 animated slideInDown">Struktur Organisasi</h1>
        <nav aria-label="breadcrumb animated slideInDown">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="#">Profil</a></li>
                <li class="breadcrumb-item active" aria-current="page">Struktur Organisasi</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header End -->

<!-- Organization Structure Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <p class="d-inline-block bg-secondary text-primary py-1 px-4 rounded-pill">Bagan Struktur</p>
            <h1 class="mb-4">Struktur Organisasi</h1>
            <p class="mb-4">Berikut adalah struktur organisasi pada bagian kami.</p>
        </div>

        <div class="row justify-content-center wow fadeInUp" data-wow-delay="0.3s">
            <div class="col-lg-12 text-center">
                @php
                    $photo = \App\Models\Setting::where('key', 'org_structure_photo')->value('value');
                @endphp
                
                @if($photo)
                    <div class="bg-white p-2 p-md-4 rounded shadow">
                        <img src="{{ asset('storage/' . $photo) }}" alt="Struktur Organisasi" class="img-fluid w-100 rounded" style="max-height: 2500px; object-fit: contain;">
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="btn btn-primary rounded-pill py-3 px-5">
                            <i class="fa fa-search-plus me-2"></i>Perbesar Gambar (Zoom)
                        </a>
                    </div>
                @else
                    <div class="bg-light p-5 rounded text-center border">
                        <i class="fa fa-sitemap fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Bagan Struktur Organisasi belum diunggah oleh Administrator.</h4>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Organization Structure End -->
@endsection
