<?php

namespace App\Http\Controllers;

use App\Models\Carga;
use App\Models\Formulario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UsuarioEnvioController extends Controller
{
    /**
     * ✅ Ver el ÚLTIMO envío de un formulario (solo lectura)
     */
    public function show($id_form)
    {
        // 1) Validar que el formulario exista y sea de la dependencia del usuario
        $form = Formulario::publicados()
            ->porDependencia(auth()->user()->id_depen)
            ->with('indicador')
            ->where('id_form', $id_form)
            ->firstOrFail();

        // 2) Última carga (por id_form)
        $ultima = Carga::where('id_form', $id_form)
            ->orderByDesc('id_carga')
            ->first();

        // ✅ Preview MANUAL (solo lectura)
        $manualPreview = [
            'columns' => [],
            'rows' => [],
            'total' => 0,
        ];

        $metodo = strtolower((string) ($ultima->metodo_captura ?? ''));

        if ($ultima && in_array($metodo, ['manual', 'manual_dinamico'], true)) {

            $rows = DB::table('detallecargas')
                ->where('id_carga', $ultima->id_carga)
                ->orderBy('id_detalle')
                ->limit(30) // muestra 30 filas (ajustable)
                ->get(['id_detalle', 'fila_det', 'payload_det']);

            $total = DB::table('detallecargas')
                ->where('id_carga', $ultima->id_carga)
                ->count();

            $columns = [];
            $dataRows = [];

            foreach ($rows as $r) {
                $payload = json_decode($r->payload_det ?? '{}', true) ?: [];
                $campos = $payload['campos'] ?? [];

                // juntar columnas dinámicas
                foreach (array_keys($campos) as $k) {
                    if (! in_array($k, $columns, true)) {
                        $columns[] = $k;
                    }
                }

                $dataRows[] = [
                    '_fila' => $r->fila_det ?? null,
                    '_id_detalle' => $r->id_detalle,
                    '_campos' => $campos,
                ];
            }

            $manualPreview = [
                'columns' => $columns,
                'rows' => $dataRows,
                'total' => $total,
            ];
        }

        $archivoEvidencia = null;

        if ($ultima && in_array($metodo, ['archivo', 'plantilla'], true)) {
            $folio = $ultima->folioUnico_carga;
            $dir = storage_path('app/public/anexos/evidencias');

            if (\Illuminate\Support\Facades\File::isDirectory($dir)) {
                $files = collect(\Illuminate\Support\Facades\File::files($dir))
                    ->filter(fn ($f) => \Illuminate\Support\Str::contains($f->getFilename(), $folio))
                    ->sortByDesc(fn ($f) => $f->getMTime())
                    ->values();

                $archivoEvidencia = $files->first()?->getFilename();
            }
        }

        return view('usuario.envios.show', compact('form', 'ultima', 'archivoEvidencia', 'manualPreview'));
    }

    /**
     * ✅ Ver un envío específico (histórico)
     */
    public function showByCarga($id_carga)
    {
        // Buscar la carga
        $carga = Carga::where('id_carga', $id_carga)->firstOrFail();

        // Validar que el formulario pertenezca al usuario
        $form = Formulario::publicados()
            ->porDependencia(auth()->user()->id_depen)
            ->with('indicador')
            ->where('id_form', $carga->id_form)
            ->firstOrFail();

        $ultima = $carga;

        // ✅ Preview MANUAL (solo lectura)
        $manualPreview = [
            'columns' => [],
            'rows' => [],
            'total' => 0,
        ];

        $metodo = strtolower((string) ($ultima->metodo_captura ?? ''));

        if ($ultima && in_array($metodo, ['manual', 'manual_dinamico'], true)) {
            $rows = DB::table('detallecargas')
                ->where('id_carga', $ultima->id_carga)
                ->orderBy('id_detalle')
                ->limit(30)
                ->get(['id_detalle', 'fila_det', 'payload_det']);

            $total = DB::table('detallecargas')
                ->where('id_carga', $ultima->id_carga)
                ->count();

            $columns = [];
            $dataRows = [];

            foreach ($rows as $r) {
                $payload = json_decode($r->payload_det ?? '{}', true) ?: [];
                $campos = $payload['campos'] ?? [];

                foreach (array_keys($campos) as $k) {
                    if (! in_array($k, $columns, true)) {
                        $columns[] = $k;
                    }
                }

                $dataRows[] = [
                    '_fila' => $r->fila_det ?? null,
                    '_id_detalle' => $r->id_detalle,
                    '_campos' => $campos,
                ];
            }

            $manualPreview = [
                'columns' => $columns,
                'rows' => $dataRows,
                'total' => $total,
            ];
        }

        $archivoEvidencia = null;

        if ($ultima && in_array($metodo, ['archivo', 'plantilla'], true)) {
            $folio = $ultima->folioUnico_carga;
            $dir = storage_path('app/public/anexos/evidencias');

            if (\Illuminate\Support\Facades\File::isDirectory($dir)) {
                $files = collect(\Illuminate\Support\Facades\File::files($dir))
                    ->filter(fn ($f) => \Illuminate\Support\Str::contains($f->getFilename(), $folio))
                    ->sortByDesc(fn ($f) => $f->getMTime())
                    ->values();

                $archivoEvidencia = $files->first()?->getFilename();
            }
        }

        return view('usuario.envios.show', [
            'form' => $form,
            'ultima' => $ultima,
            'archivoEvidencia' => $archivoEvidencia,
            'manualPreview' => $manualPreview,
        ]);
    }

    /**
     * ✅ Ver historial de envíos de un formulario (lista)
     */
    public function history($id_form)
    {
        // Validar formulario del usuario
        $form = Formulario::publicados()
            ->porDependencia(auth()->user()->id_depen)
            ->with('indicador')
            ->where('id_form', $id_form)
            ->firstOrFail();

        // Historial de cargas (por formulario)
        $historial = Carga::where('id_form', $id_form)
            ->orderByDesc('id_carga')
            ->get();

        return view('usuario.envios.historial', compact('form', 'historial'));
    }

    public function downloadArchivo($id_carga)
    {
        $carga = Carga::where('id_carga', $id_carga)->firstOrFail();

        // Seguridad: validar que el form pertenezca a su dependencia
        Formulario::publicados()
            ->porDependencia(auth()->user()->id_depen)
            ->where('id_form', $carga->id_form)
            ->firstOrFail();

        $folio = $carga->folioUnico_carga;

        // ✅ Tu carpeta real (dentro de storage/app)
        $dir = storage_path('app/public/anexos/evidencias');

        if (! File::isDirectory($dir)) {
            abort(404, 'No existe la carpeta de evidencias.');
        }

        // Buscar archivos que contengan el folio en el nombre
        $files = collect(File::files($dir))
            ->filter(function ($f) use ($folio) {
                $name = $f->getFilename();

                return Str::contains($name, $folio);
            })
            // ordenar por más reciente
            ->sortByDesc(fn ($f) => $f->getMTime())
            ->values();

        if ($files->isEmpty()) {
            abort(404, "No se encontró evidencia para el folio {$folio}.");
        }

        $file = $files->first();

        return response()->download($file->getPathname(), $file->getFilename());
    }

    public function downloadLog($id_carga)
    {
        $carga = Carga::where('id_carga', $id_carga)->firstOrFail();

        // Seguridad por dependencia
        Formulario::publicados()
            ->porDependencia(auth()->user()->id_depen)
            ->where('id_form', $carga->id_form)
            ->firstOrFail();

        $folio = $carga->folioUnico_carga;

        $dirs = [
            storage_path('app/envios'),
            storage_path('app/public/envios'),
            storage_path('app/public/anexos'),
            storage_path('app/public/anexos/logs'),
            storage_path('app/public/anexos/evidencias'),
        ];

        $candidatos = collect();

        foreach ($dirs as $dir) {
            if (File::isDirectory($dir)) {
                $files = collect(File::files($dir))
                    ->filter(function ($f) use ($folio) {
                        $name = $f->getFilename();
                        $ext = strtolower($f->getExtension());

                        return Str::contains($name, $folio) && in_array($ext, ['log', 'txt'], true);
                    })
                    ->sortByDesc(fn ($f) => $f->getMTime());

                $candidatos = $candidatos->merge($files);
            }
        }

        if ($candidatos->isEmpty()) {
            abort(404, 'No se encontró el log de procesamiento para este folio.');
        }

        $file = $candidatos->first();

        return response()->download($file->getPathname(), $file->getFilename());
    }
}
