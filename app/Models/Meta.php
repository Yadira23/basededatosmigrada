<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    use HasFactory;

    protected $table = 'metas';

    protected $fillable = [
        'id_ind',
        'titulo',
        'ejercicio',     // ✅ nuevo
        'corte',         // ✅ nuevo
        'orden',
        'config_campos',
    ];

    protected $casts = [
        'config_campos' => 'array',
        'ejercicio' => 'integer',
        'corte' => 'integer',
        'orden' => 'integer',
    ];

    public function indicador()
    {
        return $this->belongsTo(Indicador::class, 'id_ind', 'id_ind');
    }

    public function detalleCargas()
    {
        return $this->hasMany(DetalleCarga::class, 'id_meta', 'id');
    }

    /**
     * Etiqueta del periodo para mostrar en tablas (ej: "2026 T1", "2026 S2", "2026 M03", "2026").
     * Depende del periodo del indicador.
     */
    public function getPeriodoLabelAttribute(): string
    {
        $anio = $this->ejercicio ?: (int) date('Y');
        $corte = $this->corte ?: 1;

        $p = strtolower((string) ($this->indicador->periodo_ind ?? ''));

        if (str_contains($p, 'mens')) {
            return $anio . ' M' . str_pad((string)$corte, 2, '0', STR_PAD_LEFT);
        }

        if (str_contains($p, 'trim')) {
            return $anio . ' T' . $corte;
        }

        if (str_contains($p, 'sem')) {
            return $anio . ' S' . $corte;
        }

        // anual o fallback
        return (string) $anio;
    }
}
