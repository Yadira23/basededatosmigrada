<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Formulario;
use Illuminate\Support\Facades\Auth;

class IndicadorController extends Controller
{
    public function show(\App\Models\Formulario $formulario)
    {
        if ((int)$formulario->id_depen !== (int)auth()->user()->id_depen) {
            abort(403);
        }

        $formulario->load('indicador');

        $chartMetasLabels = [];
        $chartMetasValues = [];

        $chartEstadosLabels = ['SIN CAPTURA', 'BORRADOR', 'ENVIADO/REVISION', 'OBSERVADO', 'APROBADO'];
        $chartEstadosValues = [0, 0, 0, 0, 0];

        $chartHistLabels = [];
        $chartHistValues = [];

        $chartEstadosSinMetasLabels = ['SIN CAPTURA', 'BORRADOR', 'ENVIADO/REVISION', 'OBSERVADO', 'APROBADO'];
        $chartEstadosSinMetasValues = [0, 0, 0, 0, 0];

        $tieneMetas = \App\Models\Meta::where('id_ind', $formulario->id_ind)->exists();

        $metas = collect();

        if ($tieneMetas) {
            $metas = \App\Models\Meta::where('id_ind', $formulario->id_ind)
                ->orderBy('orden')
                ->with(['detalleCargas' => function ($q) use ($formulario) {
                    $q->whereHas('carga', function ($qq) use ($formulario) {
                        $qq->where('id_form', $formulario->id_form);
                    });
                }])
                ->get();

            $chartMetasLabels = [];
            $chartMetasValues = [];

            if ($tieneMetas) {
                foreach ($metas as $m) {
                    $det = $m->detalleCargas->first(); // ya viene filtrado por id_form
                    $st = $det?->estado ? mb_strtoupper(trim((string)$det->estado)) : 'SIN CAPTURA';
                    $st = str_replace('REVISIÓN', 'REVISION', $st);

                    $pct = match ($st) {
                        'APROBADO' => 100,
                        'ENVIADO', 'EN REVISION', 'REENVIADO' => 80,
                        'BORRADOR', 'OBSERVADO' => 50,
                        default => 0,
                    };

                    $chartMetasLabels[] = 'Meta ' . ($m->orden ?? $m->id);
                    $chartMetasValues[] = $pct;
                }
            }

            $chartEstadosLabels = ['SIN CAPTURA', 'BORRADOR', 'ENVIADO/REVISION', 'OBSERVADO', 'APROBADO'];
            $chartEstadosValues = [0, 0, 0, 0, 0];

            if ($tieneMetas) {
                foreach ($metas as $m) {
                    $det = $m->detalleCargas->first();
                    $st = $det?->estado ? mb_strtoupper(trim((string)$det->estado)) : 'SIN CAPTURA';
                    $st = str_replace('REVISIÓN', 'REVISION', $st);

                    if ($st === 'APROBADO') $chartEstadosValues[4]++;
                    elseif ($st === 'OBSERVADO') $chartEstadosValues[3]++;
                    elseif (in_array($st, ['ENVIADO', 'EN REVISION', 'REENVIADO'], true)) $chartEstadosValues[2]++;
                    elseif ($st === 'BORRADOR') $chartEstadosValues[1]++;
                    else $chartEstadosValues[0]++;
                }
            }

            $chartHistLabels = [];
            $chartHistValues = [];

            $ultimasCargas = \App\Models\Carga::where('id_form', $formulario->id_form)
                ->orderByDesc('id_carga')
                ->limit(12)
                ->get()
                ->reverse(); // para que se vean del más viejo al más nuevo

            foreach ($ultimasCargas as $c) {
                $st = $c->status_env ? mb_strtoupper(trim((string)$c->status_env)) : 'SIN CAPTURA';
                $st = str_replace('REVISIÓN', 'REVISION', $st);

                $pct = match ($st) {
                    'APROBADO' => 100,
                    'ENVIADO', 'EN REVISION', 'REENVIADO' => 80,
                    'BORRADOR', 'OBSERVADO' => 50,
                    default => 0,
                };

                $chartHistLabels[] = \Carbon\Carbon::parse($c->updated_at)->format('d M');
                $chartHistValues[] = $pct;
            }

            $chartEstadosSinMetasLabels = ['SIN CAPTURA', 'BORRADOR', 'ENVIADO/REVISION', 'OBSERVADO', 'APROBADO'];
            $chartEstadosSinMetasValues = [0, 0, 0, 0, 0];

            if (!$tieneMetas) {
                $cargas = \App\Models\Carga::where('id_form', $formulario->id_form)->get();

                foreach ($cargas as $c) {
                    $st = $c->status_env ? mb_strtoupper(trim((string)$c->status_env)) : 'SIN CAPTURA';
                    $st = str_replace('REVISIÓN', 'REVISION', $st);

                    if ($st === 'APROBADO') $chartEstadosSinMetasValues[4]++;
                    elseif ($st === 'OBSERVADO') $chartEstadosSinMetasValues[3]++;
                    elseif (in_array($st, ['ENVIADO', 'EN REVISION', 'REENVIADO'], true)) $chartEstadosSinMetasValues[2]++;
                    elseif ($st === 'BORRADOR') $chartEstadosSinMetasValues[1]++;
                    else $chartEstadosSinMetasValues[0]++;
                }
            }
        }

        return view('usuario.indicadores.show', compact(
            'formulario',
            'tieneMetas',
            'metas',
            'chartMetasLabels',
            'chartMetasValues',
            'chartEstadosLabels',
            'chartEstadosValues',
            'chartHistLabels',
            'chartHistValues',
            'chartEstadosSinMetasLabels',
            'chartEstadosSinMetasValues',
        ));
    }
}
