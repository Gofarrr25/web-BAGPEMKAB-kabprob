<!DOCTYPE html>
<html lang="id"> 
	<head><base href="../../../">
		<title>Reset Password - Bagian Pemerintahan Kabupaten Probolinggo</title>
		<meta name="description" content="Website Resmi Bagian Pemerintahan Kabupaten Probolinggo" />
		<meta name="keywords" content="Bagian Pemerintahan, probolinggo" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta charset="utf-8" />
		<meta name="theme-color" content="#ffffff">
		
        @php
            $faviconUrl = (isset($siteSettings['site_logo']) && $siteSettings['site_logo']) 
                ? asset('storage/' . $siteSettings['site_logo']) . '?v=' . time() 
                : asset('favicon.png') . '?v=' . time();
        @endphp
        <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
        <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
		 
		<link rel="dns-prefetch" href="//fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
		<link href="https://diskominfo.probolinggokab.go.id/backend/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="https://diskominfo.probolinggokab.go.id/backend/css/style.bundle.css" rel="stylesheet" type="text/css" />
	</head>
	<body id="kt_body" class="bg-body">
		<div class="d-flex flex-column flex-root">
			<div class="d-flex flex-column flex-lg-row flex-column-fluid">
				<div class="d-flex flex-column flex-lg-row-auto w-xl-600px positon-xl-relative bgi-no-repeat" style="background-color: #ffffff; background-image: url('{{ asset('images/bupati_wakil.jpg') }}'); background-size: cover; background-position: center;">
					<!-- Konten visual logo dan teks bawaan telah dihapus sesuai permintaan agar foto tampil penuh -->
				</div>				 
				<div class="d-flex flex-column flex-lg-row-fluid py-10" style="background-color: #f9f6f5">					
					<div class="d-flex flex-center flex-column flex-column-fluid">						
						<div class="w-lg-500px p-10 p-lg-15 mx-auto">							 
                            
                            @if($errors->any())
                                <div class="alert alert-danger mb-5">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" class="form w-100" action="{{ route('password.update') }}">
                            @csrf
                                <input type="hidden" name="token" value="{{ $token }}">

								<div class="text-center mb-10">									 
									<h1 class="text-dark mb-3">Buat Password Baru</h1>	
                                    <div class="text-gray-400 fw-bold fs-4">Sudah membuat password baru? <a href="{{ route('login') }}" class="link-primary fw-bolder">Login di sini</a></div>
								</div>		 
								
                                <div class="fv-row mb-7">
                                    <label class="form-label fw-bolder text-dark fs-6 required">Email</label>
                                    <input class="form-control form-control-lg form-control-solid" type="email" placeholder="Email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus />
                                </div>

                                <div class="mb-7 fv-row">
                                    <label class="form-label fw-bolder text-dark fs-6 required">Password Baru</label>
                                    <div class="position-relative mb-3">
                                        <input class="form-control form-control-lg form-control-solid pe-12" type="password" placeholder="Password Baru" name="password" id="password" autocomplete="new-password" required />
                                        <button type="button" onclick="togglePasswordVisibility('password', this)" class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-2 text-gray-600 hover:text-primary" style="z-index: 10; border: none; background: transparent;">
                                            <i class="fa-solid fa-eye fs-5"></i>
                                        </button>
                                    </div>
                                    <div class="text-muted">Gunakan 8 karakter atau lebih dengan campuran huruf, angka & simbol.</div>
                                </div>

                                <div class="fv-row mb-10">
                                    <label class="form-label fw-bolder text-dark fs-6 required">Konfirmasi Password Baru</label>
                                    <div class="position-relative mb-3">
                                        <input class="form-control form-control-lg form-control-solid pe-12" type="password" placeholder="Konfirmasi Password Baru" name="password_confirmation" id="password_confirmation" autocomplete="new-password" required />
                                        <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-2 text-gray-600 hover:text-primary" style="z-index: 10; border: none; background: transparent;">
                                            <i class="fa-solid fa-eye fs-5"></i>
                                        </button>
                                    </div>
                                </div>
						 
								<div class="text-center"> 
									<button type="submit" id="kt_new_password_submit" class="btn btn-lg btn-primary w-100">
										<span class="indicator-label">Reset Password</span>
										<span class="indicator-progress">Please wait...
										<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
									</button>
								</div>
							</form>							 
						</div>						
					</div>					
					<div class="d-flex flex-center flex-wrap fs-6 p-5 pb-0">
						<div class="d-flex flex-center fw-bold fs-6">
							<a href="#" class="text-muted text-hover-primary px-2">{{ date('Y') }} Bagian Pemerintahan Kabupaten Probolinggo</a>								 						 
						</div>
					</div>
				</div>
			</div>
		</div>  
		<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>	 
		<script src="https://diskominfo.probolinggokab.go.id/backend/plugins/global/plugins.bundle.js"></script>
		<script src="https://diskominfo.probolinggokab.go.id/backend/js/scripts.bundle.js"></script>	
        <script type="text/javascript">
            function togglePasswordVisibility(inputId, btn) {
                const input = document.getElementById(inputId);
                const icon = btn.querySelector('i');
                if (!input) return;
                
                if (input.type === 'password') {
                    input.type = 'text';
                    if (icon) {
                        icon.className = 'fa-solid fa-eye-slash fs-5 text-primary';
                    }
                } else {
                    input.type = 'password';
                    if (icon) {
                        icon.className = 'fa-solid fa-eye fs-5 text-gray-600';
                    }
                }
            }
        </script>
	</body>	 
</html>
