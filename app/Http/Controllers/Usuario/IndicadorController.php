<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;

class IndicadorController extends Controller
{
    public function show(\App\Models\Formulario $formulario)
    {
        if ((int) $formulario->id_depen !== (int) auth()->user()->id_depen) {
            abort(403);
        }

        $formulario->load('indicador');

        // ==========================
        // Variables (siempre existen)
        // ==========================
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

        // ==========================
        // ✅ CON METAS
        // ==========================
        if ($tieneMetas) {

            $metas = \App\Models\Meta::where('id_ind', $formulario->id_ind)
                ->orderBy('orden')
                ->with(['detalleCargas' => function ($q) use ($formulario) {
                    $q->whereHas('carga', function ($qq) use ($formulario) {
                        $qq->where('id_form', $formulario->id_form);
                    });
                }])
                ->get();

            // Gráfica 1 (barras por meta)
            foreach ($metas as $m) {
                $ultimaCargaMeta = \App\Models\Carga::where('id_form', $formulario->id_form)
                    ->where('meta_id', $m->id)
                    ->orderByDesc('id_carga')
                    ->first();

                $st = $ultimaCargaMeta?->status_env
                    ? mb_strtoupper(trim((string) $ultimaCargaMeta->status_env))
                    : 'SIN CAPTURA';
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

            // Gráfica 2 (dona conteo por estado en metas)
            foreach ($metas as $m) {
                $ultimaCargaMeta = \App\Models\Carga::where('id_form', $formulario->id_form)
                    ->where('meta_id', $m->id)
                    ->orderByDesc('id_carga')
                    ->first();

                $st = $ultimaCargaMeta?->status_env
                    ? mb_strtoupper(trim((string) $ultimaCargaMeta->status_env))
                    : 'SIN CAPTURA';
                $st = str_replace('REVISIÓN', 'REVISION', $st);

                if ($st === 'APROBADO') {
                    $chartEstadosValues[4]++;
                } elseif ($st === 'OBSERVADO') {
                    $chartEstadosValues[3]++;
                } elseif (in_array($st, ['ENVIADO', 'EN REVISION', 'REENVIADO'], true)) {
                    $chartEstadosValues[2]++;
                } elseif ($st === 'BORRADOR') {
                    $chartEstadosValues[1]++;
                } else {
                    $chartEstadosValues[0]++;
                }
            }

            // (Opcional) histórico (si lo usas en metas)
            $ultimasCargas = \App\Models\Carga::where('id_form', $formulario->id_form)
                ->orderByDesc('id_carga')
                ->limit(12)
                ->get()
                ->reverse();

            foreach ($ultimasCargas as $c) {
                $st = $c->status_env ? mb_strtoupper(trim((string) $c->status_env)) : 'SIN CAPTURA';
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
        } else {
            // ==========================
            // ✅ SIN METAS (AQUÍ ESTABA EL PROBLEMA)
            // ==========================

            // Gráfica 1: Histórico (últimas cargas)
            $ultimasCargas = \App\Models\Carga::where('id_form', $formulario->id_form)
                ->whereNull('meta_id') // ✅ solo cargas SIN meta
                ->orderByDesc('id_carga')
                ->limit(12)
                ->get()
                ->reverse();

            foreach ($ultimasCargas as $c) {
                $st = $c->status_env ? mb_strtoupper(trim((string) $c->status_env)) : 'SIN CAPTURA';
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

            // Gráfica 2: Dona por estado (todas las cargas del formulario)
            $cargas = \App\Models\Carga::where('id_form', $formulario->id_form)
                ->whereNull('meta_id') // ✅ solo cargas SIN meta
                ->get();

            foreach ($cargas as $c) {
                $st = $c->status_env ? mb_strtoupper(trim((string) $c->status_env)) : 'SIN CAPTURA';
                $st = str_replace('REVISIÓN', 'REVISION', $st);

                if ($st === 'APROBADO') {
                    $chartEstadosSinMetasValues[4]++;
                } elseif ($st === 'OBSERVADO') {
                    $chartEstadosSinMetasValues[3]++;
                } elseif (in_array($st, ['ENVIADO', 'EN REVISION', 'REENVIADO'], true)) {
                    $chartEstadosSinMetasValues[2]++;
                } elseif ($st === 'BORRADOR') {
                    $chartEstadosSinMetasValues[1]++;
                } else {
                    $chartEstadosSinMetasValues[0]++;
                }
            }

            // ✅ si TODO es 0, dibuja un segmento "SIN CAPTURA" en 1 para que se vea algo
            if (array_sum($chartEstadosSinMetasValues) === 0) {
                $chartEstadosSinMetasValues = [1, 0, 0, 0, 0];
            }

            // ✅ si no hay histórico, mete un punto "Sin datos"
            if (count($chartHistLabels) === 0) {
                $chartHistLabels = ['Sin datos'];
                $chartHistValues = [0];
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
