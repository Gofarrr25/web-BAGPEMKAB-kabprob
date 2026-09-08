@extends('layouts.public')

@section('title', 'Kontak Resmi - Bagian Pemerintahan Kabupaten Probolinggo')

@section('content')
<div class="bg-gray-50 min-h-screen pb-16">
    <div class="container mx-auto px-4 lg:px-8 mt-10">
        


        <div class="bg-white p-4 md:p-8 md:p-12 rounded-xl shadow-sm border border-gray-100 flex flex-col lg:flex-row gap-6 lg:gap-10 items-stretch">
            
            <!-- Contact Info (Left Side) -->
            <div class="w-full lg:w-1/3 flex flex-col justify-center space-y-6">
                <h2 class="text-3xl font-bold text-brand-blue">Hubungi Kami</h2>
                
                <div>
                    <h3 class="text-xl font-semibold text-brand-blue mb-4">Alamat Kantor</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt text-brand-blue mt-1"></i>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                <span class="font-bold text-gray-800">Address:</span> {{ $siteSettings['office_address'] ?? 'Jl. Panglima Sudirman No. 134 lt. 3 - Kraksaan - Probolinggo' }}
                            </p>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <i class="fas fa-phone-alt text-brand-blue"></i>
                            <p class="text-sm text-gray-600">
                                <span class="font-bold text-gray-800">Call:</span> {{ $siteSettings['phone'] ?? '0335 844554' }}
                            </p>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <i class="fas fa-envelope text-brand-blue"></i>
                            <p class="text-sm text-gray-600">
                                <span class="font-bold text-gray-800">Mail:</span> <a href="mailto:{{ $siteSettings['email'] ?? 'bagpemerintahan@probolinggokab.go.id' }}" class="text-brand-blue hover:underline">{{ $siteSettings['email'] ?? 'bagpemerintahan@probolinggokab.go.id' }}</a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="flex items-center gap-3 pt-4">
                    @if(!empty($siteSettings['facebook_url']))
                        <a href="{{ $siteSettings['facebook_url'] }}" target="_blank" class="w-10 h-10 rounded-full bg-brand-blue text-white flex items-center justify-center hover:bg-brand-blue-hover transition shadow-sm">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    @endif
                    @if(!empty($siteSettings['twitter_url']))
                        <a href="{{ $siteSettings['twitter_url'] }}" target="_blank" class="w-10 h-10 rounded-full bg-brand-blue text-white flex items-center justify-center hover:bg-brand-blue-hover transition shadow-sm">
                            <i class="fab fa-twitter"></i>
                        </a>
                    @endif
                    @if(!empty($siteSettings['instagram_url']))
                        <a href="{{ $siteSettings['instagram_url'] }}" target="_blank" class="w-10 h-10 rounded-full bg-brand-blue text-white flex items-center justify-center hover:bg-brand-blue-hover transition shadow-sm">
                            <i class="fab fa-instagram"></i>
                        </a>
                    @endif
                    @if(!empty($siteSettings['tiktok_url']))
                        <a href="{{ $siteSettings['tiktok_url'] }}" target="_blank" class="w-10 h-10 rounded-full bg-brand-blue text-white flex items-center justify-center hover:bg-brand-blue-hover transition shadow-sm">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Google Maps (Right Side) -->
            <div class="w-full lg:w-2/3">
                <div class="w-full h-80 lg:h-full min-h-[350px] rounded-xl overflow-hidden shadow-sm border border-gray-100 p-2 bg-gray-50 flex items-stretch">
                    <div class="w-full h-full flex-grow relative [&>iframe]:absolute [&>iframe]:top-0 [&>iframe]:left-0 [&>iframe]:w-full [&>iframe]:h-full [&>iframe]:rounded-lg [&>iframe]:border-0">
                        {!! $siteSettings['google_maps_iframe'] ?? '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.257002013898!2d113.4079815!3d-7.7625121!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd70068ca6cfd39%3A0xbbfd114620f4c3a2!2sKantor%20Bupati%20Probolinggo!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Peta Lokasi Kantor"></iframe>' !!}
                    </div>
                </div>
            </div>
        </div>



    </div>
</div>
@endsection
