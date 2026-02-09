<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UsuarioAnexosController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1) PLANTILLAS disponibles:
        // Basado en tu esquema: formularios tiene id_depen e id_ind
        // (y asumo que existe tabla indicadores con nombre_ind)
        $plantillas = DB::table('formularios as f')
            ->join('indicadores as i', 'i.id_ind', '=', 'f.id_ind')
            ->select('f.id_form', 'f.id_ind', 'f.titulo_form', 'i.nombre_ind', 'i.periodo_ind', 'i.unidadmedida_ind')
            ->when(Schema::hasColumn('formularios', 'id_depen') && isset($user->id_depen), function ($q) use ($user) {
                $q->where('f.id_depen', $user->id_depen);
            })
            ->orderBy('f.id_form', 'desc')
            ->get();

        // 2) MIS ARCHIVOS / ENVÍOS:
        // Usamos cargas + detallecargas (si no hay archivo en JSON, igual mostramos el folio/estatus)
        $envios = DB::table('cargas as c')
            ->join('formularios as f', 'f.id_form', '=', 'c.id_form') // ✅ clave
            ->leftJoin('detallecargas as d', 'd.id_carga', '=', 'c.id_carga')
            ->where('f.id_depen', auth()->user()->id_depen) // ✅ filtro real por dependencia
            ->select(
                'c.id_carga',
                'c.folioUnico_carga',
                'c.status_env',
                'c.created_at',
                'c.id_form',
                'c.observacion_env',
                DB::raw("MAX(JSON_UNQUOTE(JSON_EXTRACT(d.payload_det, '$.archivo.nombre'))) as archivo_nombre"),
                DB::raw("MAX(JSON_UNQUOTE(JSON_EXTRACT(d.payload_det, '$.archivo.tipo'))) as archivo_tipo"),
                DB::raw("MAX(JSON_UNQUOTE(JSON_EXTRACT(d.payload_det, '$.origen'))) as origen")
            )
            ->groupBy('c.id_carga', 'c.folioUnico_carga', 'c.status_env', 'c.created_at', 'c.id_form', 'c.observacion_env')
            ->orderBy('c.created_at', 'desc')
            ->limit(100)
            ->get();


        return view('usuario.anexos.index', compact('plantillas', 'envios'));
    }
}
