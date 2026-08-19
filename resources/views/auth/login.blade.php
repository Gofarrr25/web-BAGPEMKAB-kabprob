<!DOCTYPE html>
<html lang="id"> 
	<head><base href="../../../">
		<title>Halaman Login Bagian Pemerintahan Kabupaten Probolinggo</title>
		<meta name="description" content="Website Resmi Bagian Pemerintahan Kabupaten Probolinggo" />
		<meta name="keywords" content="Bagian Pemerintahan, probolinggo" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta charset="utf-8" />
		<meta property="og:locale" content="id" />
		<meta property="og:type" content="Informasi Bagian Pemerintahan" />
		<meta property="og:title" content="Website Resmi Bagian Pemerintahan Kabupaten Probolinggo" />
		<meta property="og:url" content="https://diskominfo.probolinggokab.go.id/" />
		<meta property="og:site_name" content="Tim Bagian Pemerintahan Probolinggo" />		 
		<meta name="theme-color" content="#ffffff">
		<link rel="canonical" href="https://diskominfo.probolinggokab.go.id/" />
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
		 
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
                            <h2>Bagian Pemerintahan</h2>
                            <h3>Kabupaten Probolinggo</h3> 
						</div>						 
					</div>					
				</div>				 
				<div class="d-flex flex-column flex-lg-row-fluid py-10" style="background-color: #f9f6f5">					
					<div class="d-flex flex-center flex-column flex-column-fluid">						
						<div class="w-lg-500px p-10 p-lg-15 mx-auto">							 
                            
                            @if(session('error'))
                                <div class="alert alert-danger mb-5">
                                    {{ session('error') }}
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

                            <form method="POST" class="form w-100" action="{{ route('login.post') }}">
                            @csrf
								<div class="text-center mb-10">									 
									<h1 class="text-dark mb-3">Login Admin</h1>									 
								</div>		 
								<div class="col-lg-12">
        <div class="mb-5 fv-row">
            <label class="form-label required" for="username">Username</label>             
            <input 
                class="form-control form-control-lg form-control-solid " 
                id="username" 
                type="text"
                name="username" 
                placeholder="Username" 
                value="{{ old('username') }}"
                required 
                 
                autocomplete="off" 
                autofocus 
                
                /> 
            
        </div>
            </div>
								<div class="fv-row mb-5">	 
									<label class="form-label">Password</label>		 									 
									<div class="fv-row position-relative mb-3">
										<input id="password" type="password" class="form-control form-control-lg form-control-solid pe-12" name="password" required autocomplete="off" />
										  
										<button type="button" onclick="togglePasswordVisibility('password', this)" class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-2 text-gray-600 hover:text-primary" style="z-index: 10; border: none; background: transparent;" title="Tampilkan/Sembunyikan Password">
											<i class="fa-solid fa-eye fs-5"></i>
										</button> 
									</div>  
								</div>		
                                <div class="fv-row mb-5">
                                    <label class="form-label">Masukkan kode berikut </label>
                                    <div class="col-md-12">      
                                        <div class="form-group">                                             
                                                        
                                            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 1rem;">                      
                                                <div class="captcha rounded overflow-hidden shadow-sm border border-gray-200" style="margin-bottom: 0;">
                                                    {!! captcha_img('flat') !!}
                                                </div>    
                                                <button type="button" class="btn btn-primary btn-refresh" style="width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;" title="Refresh CAPTCHA">
                                                    <i class="fa-solid fa-arrows-rotate fs-3"></i>
                                                </button>                      
                                            </div>                    
                                            <input id="captcha" type="text" class="form-control form-control-lg form-control-solid w-100 text-center" name="captcha" required autocomplete="off" placeholder="" style="letter-spacing: 12px; font-weight: bold; font-size: 20px;">                                                                  
                                                                  
                                        </div>  
                                    </div>
                                </div>						 
								<div class="text-center"> 
									<div class="d-flex">
										<a href="/" class="btn btn-lg btn-secondary w-100 mb-5 me-2">Kembali</a> &nbsp;
										<button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-primary w-100 mb-5">
											<span class="indicator-label">Masuk</span>
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
		<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true"> 
			<span class="svg-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
					<rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="black" />
					<path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="black" />
				</svg>
			</span> 
		</div>
		<!--end::Scrolltop-->
		<script>var hostUrl = "https://diskominfo.probolinggokab.go.id/backend";</script>	
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>	 
		<script src="https://diskominfo.probolinggokab.go.id/backend/plugins/global/plugins.bundle.js"></script>
		<script src="https://diskominfo.probolinggokab.go.id/backend/js/scripts.bundle.js"></script>	
        <script type="text/javascript">
            function togglePasswordVisibility(inputId, btn) {
                const input = document.getElementById(inputId) || btn.parentElement.querySelector('input');
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

            $(".btn-refresh").click(function(){    
                $.ajax({    
                    type:'GET',    
                    url:'/refresh_captcha',    
                    success:function(data){    
                        $(".captcha span").html(data.captcha);    
                    }    
                });    
            }); 
        </script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6-beta.29/jquery.inputmask.min.js"></script>
        <script>
            // Disabled rigid inputmask for captcha to allow dynamic length
        </script> 
	</body>	 
</html>

