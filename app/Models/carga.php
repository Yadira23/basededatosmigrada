<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carga extends Model
{
    use HasFactory;
    protected $table = 'cargas';
    protected $primaryKey = 'id_carga';

    protected $fillable = [
        'folioUnico_carga',
        'fecha_carga',
        'periodo',
        'status_env',
        'descripcion_env',
        'observacion_env',
        'id_form'
    ];

    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'id_form');
    }

    public function usuarios()
    {
        return $this->belongsToMany(
        Usuario::class,
        'bitacoras',
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

}
