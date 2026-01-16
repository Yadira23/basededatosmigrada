<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::guard('web')->attempt([
        'email_usr' => $credentials['email'],
        'password'  => $credentials['password'],
    ])) {

        $request->session()->regenerate();

        return redirect()->intended('/redirect-por-rol');
    }

    return back()->withErrors([
        'email' => 'Credenciales incorrectas',
    ]);
}


    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
