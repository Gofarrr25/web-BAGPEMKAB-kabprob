@extends('layouts.admin')

@section('page_title', 'Pengaturan Kontak & Peta')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Manajemen Kontak Halaman</h2>
    <p class="text-sm text-gray-500 mt-1">Atur informasi alamat, telepon, media sosial, dan lokasi peta yang akan ditampilkan di website.</p>
</div>

@if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle text-xl"></i>
        <div>
            <p class="font-bold">Berhasil!</p>
            <p class="text-xs">{{ session('success') }}</p>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm mb-6">
        <div class="flex items-center gap-2 mb-2">
            <i class="fas fa-exclamation-triangle font-bold"></i>
            <span class="font-bold">Terjadi Kesalahan:</span>
        </div>
        <ul class="list-disc list-inside text-xs">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 md:p-4 md:p-8">
    <form action="{{ route('admin.contact-settings.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8 mb-8">
            <!-- Data Alamat & Kontak Dasar -->
            <div class="space-y-4">
                <h4 class="font-bold text-gray-700 border-b pb-2 mb-4 text-sm flex items-center gap-2">
                    <i class="fas fa-info-circle text-brand-blue"></i> Informasi Dasar
                </h4>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Alamat Kantor Resmi</label>
                    <textarea name="office_address" rows="3" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg text-xs">{{ $settings['office_address'] ?? 'Jl. Panglima Sudirman No. 134 lt. 3 - Kraksaan - Probolinggo' }}</textarea>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nomor Telepon Kantor</label>
                    <input type="text" name="phone" value="{{ $settings['phone'] ?? '0335 844554' }}" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg text-xs font-semibold">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Email Resmi Instansi</label>
                    <input type="email" name="email" value="{{ $settings['email'] ?? 'bagpemerintahan@probolinggokab.go.id' }}" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg text-xs font-semibold">
                </div>

            </div>
            
            <!-- Media Sosial -->
            <div class="space-y-4">
                <h4 class="font-bold text-gray-700 border-b pb-2 mb-4 text-sm flex items-center gap-2">
                    <i class="fas fa-share-alt text-green-500"></i> Link Tautan Media Sosial
                </h4>
                <p class="text-xs text-gray-500 mb-3">Kosongkan link jika Anda tidak ingin menampilkan ikon platform tersebut di halaman Kontak website.</p>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1"><i class="fab fa-facebook text-brand-blue w-4"></i> Facebook (URL)</label>
                    <input type="url" name="facebook_url" value="{{ $settings['facebook_url'] ?? '' }}" placeholder="https://facebook.com/..." class="w-full px-3.5 py-2 border border-gray-300 rounded-lg text-xs font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1"><i class="fab fa-twitter text-brand-blue-pale w-4"></i> Twitter / X (URL)</label>
                    <input type="url" name="twitter_url" value="{{ $settings['twitter_url'] ?? '' }}" placeholder="https://twitter.com/..." class="w-full px-3.5 py-2 border border-gray-300 rounded-lg text-xs font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1"><i class="fab fa-instagram text-pink-500 w-4"></i> Instagram (URL)</label>
                    <input type="url" name="instagram_url" value="{{ $settings['instagram_url'] ?? '' }}" placeholder="https://instagram.com/..." class="w-full px-3.5 py-2 border border-gray-300 rounded-lg text-xs font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1"><i class="fab fa-tiktok text-black w-4"></i> TikTok (URL)</label>
                    <input type="url" name="tiktok_url" value="{{ $settings['tiktok_url'] ?? '' }}" placeholder="https://tiktok.com/@..." class="w-full px-3.5 py-2 border border-gray-300 rounded-lg text-xs font-mono">
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-6 mb-8">
            <h4 class="font-bold text-gray-700 border-b pb-2 mb-4 text-sm flex items-center gap-2">
                <i class="fas fa-map-marked-alt text-red-500"></i> Pencarian Lokasi Peta (Google Maps)
            </h4>
            <p class="text-xs text-gray-500 mb-3">Ketik nama tempat/alamat, lalu pilih dari hasil pencarian untuk memperbarui lokasi peta secara otomatis.</p>
            
            <div class="relative mb-4">
                <div class="flex gap-2">
                    <input type="text" id="mapSearchInput" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-brand-blue" placeholder="Contoh: Kantor Bupati Probolinggo..." autocomplete="off">
                    <button type="button" id="btnSearchMap" class="px-5 py-2 bg-gray-800 text-white font-bold rounded-lg hover:bg-gray-700 text-sm whitespace-nowrap transition flex items-center gap-2">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
                <!-- Search Results Dropdown -->
                <div id="searchResults" class="absolute z-10 w-full bg-white border border-gray-200 shadow-xl rounded-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
            </div>

            <input type="hidden" name="google_maps_iframe" id="google_maps_iframe" value="{{ $settings['google_maps_iframe'] ?? '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.257002013898!2d113.4079815!3d-7.7625121!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd70068ca6cfd39%3A0xbbfd114620f4c3a2!2sKantor%20Bupati%20Probolinggo!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>' }}">
            
            <div class="mt-2 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-xs font-bold text-gray-600 mb-2">Preview Peta Saat Ini:</p>
                <div class="w-full h-48 rounded overflow-hidden shadow-sm" id="mapPreviewContainer">
                    {!! $settings['google_maps_iframe'] ?? '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.257002013898!2d113.4079815!3d-7.7625121!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd70068ca6cfd39%3A0xbbfd114620f4c3a2!2sKantor%20Bupati%20Probolinggo!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" class="w-full h-full border-0" allowfullscreen="" loading="lazy"></iframe>' !!}
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-5">
            <button type="submit" class="w-full md:w-auto px-8 py-3 bg-brand-blue text-white font-bold rounded-lg hover:bg-brand-blue-hover transition shadow text-sm flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Simpan Pengaturan Kontak
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('mapSearchInput');
        const btnSearch = document.getElementById('btnSearchMap');
        const resultsContainer = document.getElementById('searchResults');
        const hiddenIframeInput = document.getElementById('google_maps_iframe');
        const mapPreview = document.getElementById('mapPreviewContainer');

        function performSearch() {
            const query = searchInput.value.trim();
            if (query.length < 3) return;

            btnSearch.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Tunggu...';
            btnSearch.disabled = true;
            
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=id&limit=5`, {
                headers: {
                    'Accept-Language': 'id-ID,id;q=0.9'
                }
            })
                .then(res => res.json())
                .then(data => {
                    btnSearch.innerHTML = '<i class="fas fa-search"></i> Cari';
                    btnSearch.disabled = false;
                    resultsContainer.innerHTML = '';
                    
                    // Opsi pencarian langsung persis dengan kata kunci (Akurat menggunakan engine Google Maps)
                    const exactDiv = document.createElement('div');
                    exactDiv.className = 'p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 text-sm font-bold text-brand-blue transition flex items-center gap-2';
                    exactDiv.innerHTML = `<i class="fas fa-map-marker-alt"></i> Gunakan kata kunci "${query}" (Pencarian Akurat)`;
                    exactDiv.onclick = function() {
                        selectLocation(query, query);
                    };
                    resultsContainer.appendChild(exactDiv);
                    
                    if (data.length > 0) {
                        data.forEach(place => {
                            const div = document.createElement('div');
                            div.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 text-sm text-gray-700 transition';
                            const title = place.name ? place.name : query;
                            div.innerHTML = `<strong>${title}</strong><br><span class="text-xs text-gray-500">${place.display_name}</span>`;
                            div.onclick = function() {
                                // Pass coordinates and name for precise placement
                                selectLocationByCoords(place.lat, place.lon, title);
                            };
                            resultsContainer.appendChild(div);
                        });
                    }
                    resultsContainer.classList.remove('hidden');
                })
                .catch(err => {
                    btnSearch.innerHTML = '<i class="fas fa-search"></i> Cari';
                    btnSearch.disabled = false;
                    
                    // Fallback
                    resultsContainer.innerHTML = '';
                    const exactDiv = document.createElement('div');
                    exactDiv.className = 'p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 text-sm font-bold text-brand-blue transition flex items-center gap-2';
                    exactDiv.innerHTML = `<i class="fas fa-map-marker-alt"></i> Gunakan kata kunci "${query}" (Pencarian Akurat)`;
                    exactDiv.onclick = function() {
                        selectLocation(query, query);
                    };
                    resultsContainer.appendChild(exactDiv);
                    resultsContainer.classList.remove('hidden');
                });
        }

        function selectLocation(searchQuery, displayTitle) {
            resultsContainer.classList.add('hidden');
            searchInput.value = displayTitle;
            
            // Build embed URL using text query - allows Google to perfectly resolve the exact location
            const iframeHtml = `<iframe src="https://maps.google.com/maps?q=${encodeURIComponent(searchQuery)}&hl=id&z=16&output=embed" class="w-full h-full border-0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>`;
            
            hiddenIframeInput.value = iframeHtml;
            mapPreview.innerHTML = iframeHtml;
            
            triggerSaveAnimation();
        }
        
        function selectLocationByCoords(lat, lon, shortName) {
            resultsContainer.classList.add('hidden');
            searchInput.value = shortName;
            
            // Build embed URL using coordinates with label
            const queryData = `${lat},${lon}(${encodeURIComponent(shortName)})`;
            const iframeHtml = `<iframe src="https://maps.google.com/maps?q=${queryData}&hl=id&z=16&output=embed" class="w-full h-full border-0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>`;
            
            hiddenIframeInput.value = iframeHtml;
            mapPreview.innerHTML = iframeHtml;
            
            triggerSaveAnimation();
        }

        function triggerSaveAnimation() {
            const saveBtn = document.querySelector('button[type="submit"]');
            saveBtn.classList.add('ring-4', 'ring-brand-blue-pale', 'animate-pulse');
            setTimeout(() => {
                saveBtn.classList.remove('ring-4', 'ring-brand-blue-pale', 'animate-pulse');
            }, 3000);
        }

        btnSearch.addEventListener('click', performSearch);
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target) && !btnSearch.contains(e.target)) {
                resultsContainer.classList.add('hidden');
            }
        });
    });
</script>
@endpush
