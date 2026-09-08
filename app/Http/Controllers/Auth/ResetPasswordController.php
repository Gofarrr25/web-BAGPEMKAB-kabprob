<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'string', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                activity('admin_log')
                    ->causedBy($user)
                    ->performedOn($user)
                    ->withProperties([
                        'module' => 'Authentication',
                        'type' => 'Reset Password',
                        'role' => $user->roles->pluck('name')->first() ?? 'User',
                        'username' => $user->name,
                        'ip' => request()->ip(),
                        'browser' => request()->userAgent() ?? 'Unknown',
                        'os' => 'Unknown',
                        'status' => 200,
                        'level' => 'INFO',
                    ])
                    ->log('User berhasil melakukan reset password melalui email link');

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's login form. If there is an error we can redirect them
        // back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('success', 'Password Anda berhasil diubah. Silakan login dengan password baru.')
                    : back()->withErrors(['email' => __($status)]);
    }
}
