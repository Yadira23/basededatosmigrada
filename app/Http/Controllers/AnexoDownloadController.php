<?php

namespace App\Http\Controllers;

use App\Models\Anexo;
use Illuminate\Support\Facades\Storage;

class AnexoDownloadController extends Controller
{
    public function plantilla($id_form, $id_ind)
    {
        $anexo = Anexo::where('id_form', $id_form)
            ->where('id_ind', $id_ind)
            ->where('tipo_anexo', 'plantilla')
            ->orderByDesc('fecha_subida_anexo')
            ->orderByDesc('id_anexo')
            ->firstOrFail();

        return $this->descargarArchivo($anexo);
    }

    public function descargar($id_anexo)
    {
        $anexo = Anexo::findOrFail($id_anexo);

        return $this->descargarArchivo($anexo);
    }

    /**
     * Descarga cualquier anexo con nombre correcto (incluye extensión).
     */
    private function descargarArchivo(Anexo $anexo)
    {
        $path = $anexo->ruta_archivo_anexo;

        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'Archivo no encontrado.');
        }

        // Nombre final de descarga:
        // - si nombre_anexo no trae extensión, se la ponemos con base en el archivo real
        $baseName = $anexo->nombre_anexo ?: basename($path);
        $extReal = pathinfo($path, PATHINFO_EXTENSION);

        if ($extReal && !preg_match('/\.[a-z0-9]+$/i', $baseName)) {
            $baseName .= '.' . $extReal;
        }

        return response()->download(
            storage_path('app/public/' . $path),
            $baseName
        );
    }
}
