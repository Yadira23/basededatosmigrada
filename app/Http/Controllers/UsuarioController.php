<?php

namespace App\Http\Controllers;

class UsuarioController extends Controller
{
    public function dashboard()
    {
        return view('usuario.dashboard');
    }
}
