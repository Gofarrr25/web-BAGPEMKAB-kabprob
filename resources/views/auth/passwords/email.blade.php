<!DOCTYPE html>
<html lang="id"> 
	<head><base href="../../../">
		<title>Lupa Password - Bagian Pemerintahan Kabupaten Probolinggo</title>
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
				<div class="d-flex flex-column flex-lg-row-auto w-xl-600px positon-xl-relative bgi-no-repeat bgi-position-x-right bgi-position-y-bottom" style="background-color: #ffffff; background-image: url('https://diskominfo.probolinggokab.go.id/backend/gambar/smart.jpg');">
					<div class="d-flex flex-column position-xl-fixed top-0 bottom-0 w-xl-600px scroll-y">
						<div class="d-flex flex-row-fluid flex-column text-center p-10 pt-lg-20">
							<a href="#" class="py-2 mb-2">								 
                                <img src="https://diskominfo.probolinggokab.go.id/backend/gambar/logoprob.png" class="h-100px" style="max-height: 150px !important" alt="Logo"/>
                            </a>
						</div>						 
					</div>					
				</div>				 
				<div class="d-flex flex-column flex-lg-row-fluid py-10" style="background-color: #f9f6f5">					
					<div class="d-flex flex-center flex-column flex-column-fluid">						
						<div class="w-lg-500px p-10 p-lg-15 mx-auto">							 
                            
                            @if(session('status'))
                                <div class="alert alert-success mb-5">
                                    {{ session('status') }}
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger mb-5">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" class="form w-100" action="{{ route('password.email') }}">
                            @csrf
								<div class="text-center mb-10">									 
									<h1 class="text-dark mb-3">Lupa Password?</h1>	
                                    <div class="text-gray-400 fw-bold fs-4">Masukkan email Anda untuk reset password.</div>								 
								</div>		 
								
                                <div class="fv-row mb-10">
                                    <label class="form-label fw-bolder text-gray-900 fs-6">Email</label>
                                    <input class="form-control form-control-solid" type="email" placeholder="Email terdaftar" name="email" autocomplete="off" required value="{{ old('email') }}"/>
                                </div>
						 
								<div class="text-center"> 
									<div class="d-flex">
										<a href="{{ route('login') }}" class="btn btn-lg btn-light-primary fw-bolder w-100 mb-5 me-2">Batal</a>
										<button type="submit" id="kt_password_reset_submit" class="btn btn-lg btn-primary w-100 mb-5">
											<span class="indicator-label">Kirim Link Reset</span>
											<span class="indicator-progress">Please wait...
											<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
										</button>
									</div>									 									 
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
	</body>	 
</html>
