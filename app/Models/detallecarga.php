<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class detallecarga extends Model
{
    protected $primaryKey = 'id_detalle';
    public function carga()
    {
        return $this->belongsTo(Carga::class, 'id_carga');
    }

    public function indicador()
    {
        return $this->belongsTo(Indicador::class, 'id_indicador');
    }
    use HasFactory;
}
