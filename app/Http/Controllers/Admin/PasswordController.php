<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function edit()
    {
        return view('admin.password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password_actual' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->password_actual, auth()->user()->password)) {
            return back()->withErrors([
                'password_actual' => 'La contraseña actual no es correcta',
            ]);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('admin.perfil')
            ->with('success', 'Contraseña actualizada correctamente');
    }
}