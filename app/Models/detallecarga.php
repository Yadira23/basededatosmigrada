<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCarga extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'detallecargas';

    protected $primaryKey = 'id_detalle';

    protected $fillable = ['id_carga', 'id_ind', 'ambito_geo', 'id_region', 'id_mun', 'fila_det', 'periodo_det', 'ejercicio_det', 'fecha_registro_det', 'fuente_det', 'valor_det', 'payload_det'];

    protected $casts = [
        'payload_det' => 'array',
        'fecha_registro_det' => 'date',
        'ejercicio_det' => 'integer',
        'valor_det' => 'decimal:4',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function carga()
    {
        return $this->belongsTo('App\Models\Carga', 'id_carga', 'id_carga');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function indicador()
    {
        return $this->belongsTo('App\Models\Indicador', 'id_ind', 'id_ind');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function municipio()
    {
        return $this->belongsTo('App\Models\Municipio', 'id_mun', 'id_mun');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function region()
    {
        return $this->belongsTo('App\Models\Region', 'id_region', 'id_region');
    }
}
