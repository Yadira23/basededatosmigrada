<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function seguimientoGeneral()
    {
        $dependencias = \App\Models\Dependencia::with([
            'formularios.indicador.metas',
            'formularios.cargas',
            'formularios.ultimaCarga',
        ])
            ->orderBy('nombre_depen')
            ->get();

        $filas = $dependencias->map(function ($dependencia) {
            $formularios = $dependencia->formularios ?? collect();

            $totalUnidades = 0;
            $capturados = 0;
            $observados = 0;
            $aprobados = 0;
            $enviados = 0;

            foreach ($formularios as $formulario) {
                $indicador = $formulario->indicador;
                $metas = $indicador?->metas ?? collect();

                if ($metas->count() > 0) {
                    $totalUnidades += $metas->count();

                    foreach ($metas as $meta) {
                        $ultimaCargaMeta = $formulario->cargas
                            ->where('meta_id', $meta->id)
                            ->sortByDesc('created_at')
                            ->first();

                        if ($ultimaCargaMeta) {
                            $capturados++;

                            $status = strtoupper(trim((string) $ultimaCargaMeta->status_env));

                            if ($status === 'OBSERVADO') {
                                $observados++;
                            } elseif ($status === 'APROBADO') {
                                $aprobados++;
                            } else {
                                $enviados++;
                            }
                        }
                    }
                } else {
                    $totalUnidades += 1;

                    if ($formulario->ultimaCarga) {
                        $capturados++;

                        $status = strtoupper(trim((string) $formulario->ultimaCarga->status_env));

                        if ($status === 'OBSERVADO') {
                            $observados++;
                        } elseif ($status === 'APROBADO') {
                            $aprobados++;
                        } else {
                            $enviados++;
                        }
                    }
                }
            }

            $pendientes = max($totalUnidades - $capturados, 0);

            $avance = $totalUnidades > 0
                ? round(($capturados / $totalUnidades) * 100)
                : 0;

            return [
                'id_depen' => $dependencia->id_depen,
                'dependencia' => $dependencia->nombre_depen,
                'indicadores' => $totalUnidades,
                'capturados' => $capturados,
                'pendientes' => $pendientes,
                'observados' => $observados,
                'aprobados' => $aprobados,
                'enviados' => $enviados,
                'avance' => $avance,
            ];
        });

        $totalDependencias = $filas->count();
        $totalIndicadores = $filas->sum('indicadores');
        $totalCapturados = $filas->sum('capturados');
        $totalPendientes = $filas->sum('pendientes');
        $totalObservados = $filas->sum('observados');
        $totalAprobados = $filas->sum('aprobados');
        $totalEnviados = $filas->sum('enviados');

        $avanceGeneral = $totalIndicadores > 0
            ? round(($totalCapturados / $totalIndicadores) * 100)
            : 0;

        $dependenciaSeleccionada = null;
        $detalleDependencia = collect();

        $depId = request('dep');

        if ($depId) {
            $dependencia = \App\Models\Dependencia::with([
                'formularios.indicador.metas',
                'formularios.cargas',
                'formularios.ultimaCarga',
            ])->find($depId);

            if ($dependencia) {
                $dependenciaSeleccionada = $dependencia;

                $detalle = collect();

                foreach ($dependencia->formularios as $formulario) {
                    $indicador = $formulario->indicador;
                    if (!$indicador) {
                        continue;
                    }

                    $metas = $indicador->metas ?? collect();

                    // Indicador con metas
                    if ($metas->count() > 0) {
                        foreach ($metas as $meta) {
                            $ultimaCargaMeta = $formulario->cargas
                                ->where('meta_id', $meta->id)
                                ->sortByDesc('created_at')
                                ->first();

                            $status = $ultimaCargaMeta
                                ? strtoupper(trim((string) $ultimaCargaMeta->status_env))
                                : 'PENDIENTE';

                            $detalle->push([
                                'indicador' => $indicador->nombre_ind . ' - ' . ($meta->titulo ?? ('Meta ' . $meta->id)),
                                'estado' => $status ?: 'PENDIENTE',
                                'fecha' => $ultimaCargaMeta?->created_at
                                    ? $ultimaCargaMeta->created_at->format('d/m/Y')
                                    : '-',
                            ]);
                        }
                    }

                    // Indicador sin metas
                    else {
                        $ultimaCarga = $formulario->ultimaCarga;

                        $status = $ultimaCarga
                            ? strtoupper(trim((string) $ultimaCarga->status_env))
                            : 'PENDIENTE';

                        $detalle->push([
                            'indicador' => $indicador->nombre_ind,
                            'estado' => $status ?: 'PENDIENTE',
                            'fecha' => $ultimaCarga?->created_at
                                ? $ultimaCarga->created_at->format('d/m/Y')
                                : '-',
                        ]);
                    }
                }

                $detalleDependencia = $detalle;
            }
        }

        return view('admin.seguimiento-general', [
            'filas' => $filas,
            'totalDependencias' => $totalDependencias,
            'totalIndicadores' => $totalIndicadores,
            'totalPendientes' => $totalPendientes,
            'avanceGeneral' => $avanceGeneral,
            'totalCapturados' => $totalCapturados,
            'totalObservados' => $totalObservados,
            'totalAprobados' => $totalAprobados,
            'totalEnviados' => $totalEnviados,
            'dependenciaSeleccionada' => $dependenciaSeleccionada,
            'detalleDependencia' => $detalleDependencia,
        ]);
    }
}
