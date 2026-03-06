<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // 🔎 Buscar usuario por email_usr (tu columna real)
        $user = \App\Models\Usuario::where('email_usr', $request->email)->first();

        if (! $user) {
            return back()->withErrors([
                'email' => 'No existe un usuario con ese correo.',
            ]);
        }

        // 📩 Enviar enlace de recuperación usando email_usr
        $status = \Illuminate\Support\Facades\Password::sendResetLink([
            'email_usr' => $user->email_usr,
        ]);

        return $status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
