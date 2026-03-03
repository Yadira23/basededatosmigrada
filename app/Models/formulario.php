<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formulario extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'formularios';

    protected $primaryKey = 'id_form';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'titulo_form',
        'fecha_creacion_form',
        'descripcion_form',
        'boton_accion_form',
        'secciones_form',
        'periodo_form',
        'id_ind',      // 🔴 SE AGREGA
        'id_depen',
    ];

    /**
     * Formulario -> Anexos (1:N)
     */
    public function anexos()
    {
        return $this->hasMany(Anexo::class, 'id_form', 'id_form');
    }

    /**
     * Formulario -> Cargas (1:N)
     */
    public function cargas()
    {
        return $this->hasMany(Carga::class, 'id_form', 'id_form');
    }

    // ✅ Laravel: última carga por formulario (la más reciente)
    public function ultimaCarga()
    {
        // si tu "orden" real es por created_at, déjalo así:
        return $this->hasOne(\App\Models\Carga::class, 'id_form', 'id_form')->latestOfMany('created_at');

        // si tu sistema garantiza que el id incrementa en el tiempo, también podría ser:
        // return $this->hasOne(\App\Models\Carga::class, 'id_form', 'id_form')->latestOfMany('id_carga');
    }

    /**
     * Formulario -> Dependencia (N:1)
     */
    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class, 'id_depen', 'id_depen');
    }

    /**
     * 🔴 Formulario -> Indicador (N:1)
     */
    public function indicador()
    {
        return $this->belongsTo(Indicador::class, 'id_ind', 'id_ind');
    }

    // Scope para obtener solo formularios publicados
    public function scopePublicados($query)
    {
        return $query->whereIn('boton_accion_form', ['Publicado', 'Ver']);
    }

    // Scope para filtrar por dependencia
    public function scopePorDependencia($query, $id_depen)
    {
        return $query->where('id_depen', $id_depen);
    }

    public function scopeHabilitadosHoy($q, $fecha = null)
    {
        $hoy = $fecha ? Carbon::parse($fecha) : Carbon::now();

        // Ventanas "actuales"
        $iniMes = $hoy->copy()->startOfMonth();
        $finMes = $hoy->copy()->endOfMonth();

        $mes = (int) $hoy->month;

        // Trimestre actual
        $qNum = (int) ceil($mes / 3);                 // 1..4
        $iniTri = Carbon::create($hoy->year, (($qNum - 1) * 3) + 1, 1)->startOfDay();
        $finTri = $iniTri->copy()->addMonths(3)->subDay()->endOfDay();

        // Semestre actual
        $iniSem = Carbon::create($hoy->year, ($mes <= 6 ? 1 : 7), 1)->startOfDay();
        $finSem = $iniSem->copy()->addMonths(6)->subDay()->endOfDay();

        // Año actual
        $iniAnio = $hoy->copy()->startOfYear();
        $finAnio = $hoy->copy()->endOfYear();

        return $q->where(function ($w) use ($iniMes, $finMes, $iniTri, $finTri, $iniSem, $finSem, $iniAnio, $finAnio) {

            // ✅ Mensual: formularios creados dentro del mes actual
            $w->where(function ($m) use ($iniMes, $finMes) {
                $m->where('periodo_form', 'MENSUAL')
                    ->whereBetween('fecha_creacion_form', [$iniMes->toDateString(), $finMes->toDateString()]);
            })

                // ✅ Trimestral: formularios creados dentro del trimestre actual
                ->orWhere(function ($t) use ($iniTri, $finTri) {
                    $t->where('periodo_form', 'TRIMESTRAL')
                        ->whereBetween('fecha_creacion_form', [$iniTri->toDateString(), $finTri->toDateString()]);
                })

                // ✅ Semestral: formularios creados dentro del semestre actual
                ->orWhere(function ($s) use ($iniSem, $finSem) {
                    $s->where('periodo_form', 'SEMESTRAL')
                        ->whereBetween('fecha_creacion_form', [$iniSem->toDateString(), $finSem->toDateString()]);
                })

                // ✅ Anual: formularios creados dentro del año actual
                ->orWhere(function ($a) use ($iniAnio, $finAnio) {
                    $a->where('periodo_form', 'ANUAL')
                        ->whereBetween('fecha_creacion_form', [$iniAnio->toDateString(), $finAnio->toDateString()]);
                });
        });
    }

    public static function periodoActualPermitido(string $periodicidad, $fecha = null): string
    {
        $hoy = $fecha ? Carbon::parse($fecha) : Carbon::now();

        $year = $hoy->year;
        $mes = $hoy->month;

        return match (mb_strtolower($periodicidad)) {
            'MENSUAL' => $hoy->format('Y-m'),                 // 2026-02
            'TRIMESTRAL' => $year.'-T'.(int) ceil($mes / 3), // 2026-T1
            'SEMESTRAL' => $year.'-S'.($mes <= 6 ? 1 : 2),  // 2026-S1
            'ANUAL' => (string) $year,                      // 2026
            default => $hoy->format('Y-m'),
        };
    }

    public static function periodoYMActualPermitido(string $periodicidad, $fecha = null): string
    {
        $hoy = $fecha ? Carbon::parse($fecha) : Carbon::now();

        $y = (int) $hoy->year;
        $m = (int) $hoy->month;

        $mesInicio = match (mb_strtolower(trim($periodicidad))) {
            'MENSUAL' => $m,
            'TRIMESTRAL' => (int) (floor(($m - 1) / 3) * 3 + 1), // 1,4,7,10
            'SEMESTRAL' => $m <= 6 ? 1 : 7,                   // 1 o 7
            'ANUAL' => 1,
            default => $m,
        };

        return sprintf('%04d-%02d', $y, $mesInicio);
    }

    public function getRouteKeyName()
    {
        return 'id_form';
    }
}
