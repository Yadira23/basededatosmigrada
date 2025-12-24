<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class carga extends Model
{
    protected $primaryKey = 'id_carga';
    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'id_form');
    }

    public function usuarios()
    {
        return $this->belongsToMany(
        Usuario::class,
        'bitacora',
        'id_carga',
        'id_usuario'
    )->using(Bitacora::class)
     ->withPivot(['accion_bit', 'descripcion_bit', 'fecha_bit', 'ip_origen_bit'])
     ->withTimestamps();
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCarga::class, 'id_carga');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }
    use HasFactory;
}
