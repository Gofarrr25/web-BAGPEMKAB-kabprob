<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <h2 style="color: #333; text-align: center; border-bottom: 1px solid #ddd; padding-bottom: 10px;">Bagian Pemerintahan Kabupaten Probolinggo</h2>

    <p>Halo <strong>{{ $user->name }}</strong>,</p>
    
    <p>Anda menerima email ini karena kami menerima permintaan pengaturan ulang kata sandi (reset password) untuk akun Anda di Portal Bagian Pemerintahan Kabupaten Probolinggo.</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $url }}" style="background-color: #3182ce; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 4px; font-size: 16px; font-weight: bold; display: inline-block;">Reset Password Anda</a>
    </div>

    <p>Tautan reset password ini akan kedaluwarsa dalam 60 menit ke depan.</p>
    
    <p>Jika Anda tidak merasa meminta pengaturan ulang kata sandi, tidak ada tindakan lebih lanjut yang perlu Anda lakukan. Akun Anda tetap aman.</p>
    
    <br>
    <p>Salam hangat,<br>
    <strong>Administrator Sistem</strong><br>
    Bagian Pemerintahan Kab. Probolinggo</p>

    <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
    
    <p style="font-size: 12px; color: #777;">
        Jika Anda kesulitan mengklik tombol "Reset Password Anda", salin dan tempel URL di bawah ini ke peramban web Anda:<br>
        <a href="{{ $url }}" style="color: #3182ce; word-break: break-all;">{{ $url }}</a>
    </p>
    
</body>
</html>
