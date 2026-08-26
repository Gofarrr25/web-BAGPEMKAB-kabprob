<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $email = trim($request->input('email'));

        $status = Password::broker()->sendResetLink(
            ['email' => $email]
        );

        // Security best practice: Always return a generic success message
        // whether the email exists or not, to prevent email enumeration.
        return back()->with('status', 'Jika alamat email terdaftar, instruksi reset password akan dikirim ke email tersebut.');
    }
}
