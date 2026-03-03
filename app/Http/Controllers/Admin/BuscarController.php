<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Ajusta modelos a los nombres reales de tu proyecto
use App\Models\Dependencia;
use App\Models\Indicador;
use App\Models\Formulario;
use App\Models\Usuario;

class BuscarController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $k = mb_strtolower($q);

        // Si el usuario escribe el nombre del módulo, listamos "todo" de ese módulo
        if (in_array($k, ['formularios', 'formulario'])) {
            $formularios = \App\Models\Formulario::query()
                ->latest()
                ->limit(10)
                ->get();

            return view('admin.busqueda', [
                'q' => $q,
                'dependencias' => collect(),
                'indicadores' => collect(),
                'formularios' => $formularios,
                'usuarios' => collect(),
            ]);
        }

        if (in_array($k, ['dependencias', 'dependencia'])) {
            $dependencias = \App\Models\Dependencia::query()->latest()->limit(10)->get();

            return view('admin.busqueda', [
                'q' => $q,
                'dependencias' => $dependencias,
                'indicadores' => collect(),
                'formularios' => collect(),
                'usuarios' => collect(),
            ]);
        }

        if (in_array($k, ['usuarios', 'usuario'])) {
            $usuarios = \App\Models\Usuario::query()->latest()->limit(10)->get();

            return view('admin.busqueda', [
                'q' => $q,
                'dependencias' => collect(),
                'indicadores' => collect(),
                'formularios' => collect(),
                'usuarios' => $usuarios,
            ]);
        }

        if (in_array($k, ['indicadores', 'indicador'])) {
            $indicadores = \App\Models\Indicador::query()->latest()->limit(10)->get();

            return view('admin.busqueda', [
                'q' => $q,
                'dependencias' => collect(),
                'indicadores' => $indicadores,
                'formularios' => collect(),
                'usuarios' => collect(),
            ]);
        }

        // Si viene vacío, mostramos vista sin resultados
        if ($q === '') {
            return view('admin.busqueda', [
                'q' => $q,
                'dependencias' => collect(),
                'indicadores'  => collect(),
                'formularios'  => collect(),
                'usuarios'     => collect(),
            ]);
        }

        // Limita para que sea rápido
        $limit = 10;

        $dependencias = Dependencia::query()
            ->where('nombre_depen', 'like', "%{$q}%")
            ->limit($limit)
            ->get();

        $indicadores = Indicador::query()
            ->where('nombre_ind', 'like', "%{$q}%")
            ->orWhere('definicion_ind', 'like', "%{$q}%")
            ->limit($limit)
            ->get();

        $formularios = Formulario::query()
            ->where('periodo_form', 'like', "%{$q}%")
            ->orWhere('boton_accion_form', 'like', "%{$q}%")
            ->orWhereHas('indicador', function ($qq) use ($q) {
                $qq->where('nombre_ind', 'like', "%{$q}%");
            })
            ->limit($limit)
            ->get();

        $usuarios = Usuario::query()
            ->where('usuario_usr', 'like', "%{$q}%")
            ->orWhere('nombre_usr', 'like', "%{$q}%")
            ->orWhere('apellido_paterno', 'like', "%{$q}%")
            ->orWhere('apellido_materno', 'like', "%{$q}%")
            ->limit($limit)
            ->get();

        return view('admin.busqueda', compact('q', 'dependencias', 'indicadores', 'formularios', 'usuarios'));
    }
}
