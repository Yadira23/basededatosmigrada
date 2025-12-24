<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class usuario extends Model
{
    protected $primaryKey = 'id_usuario';
    public function rol()
    {
        return $this->belongsTo(Role::class, 'id_rol');
    }

    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class, 'id_dep');
    }

    public function cargas()
    {
        return $this->belongsToMany(
        Carga::class,
        'bitacora',
        'id_usuario',
        'id_carga'
    )->using(Bitacora::class)
     ->withPivot(['accion_bit', 'descripcion_bit', 'fecha_bit', 'ip_origen_bit'])
     ->withTimestamps();
    }
    use HasFactory;
}
