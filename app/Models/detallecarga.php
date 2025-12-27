<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCarga extends Model
{
    use HasFactory;

    protected $table = 'detallecargas';
    protected $primaryKey = 'id_detalle';

    protected $fillable = [
        'id_carga',
        'id_ind',
        'id_region',
        'id_mun',
        'periodo_det',
        'ejercicio_det',
        'fecha_registro_det',
        'fuente_det',
        'valor_det'
    ];

    public function carga()
    {
        return $this->belongsTo(Carga::class, 'id_carga');
    }

    public function indicador()
    {
        return $this->belongsTo(Indicador::class, 'id_ind');
    }

    // 🔹 Detalle → Región
    public function region()
    {
        return $this->belongsTo(Region::class, 'id_region');
    }

    // 🔹 Detalle → Municipio
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_mun');
    }
}
