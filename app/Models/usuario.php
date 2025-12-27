<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'usuario_usr',
        'nombre_usr',
        'apellido_paterno',
        'apellido_materno',
        'email_usr',
        'password',
        'id_depen',
        'id_rol',
        'estado_usr',
        'telefono_usr'
    ];

    public function rol()
    {
        return $this->belongsTo(Role::class, 'id_rol');
    }

    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class, 'id_depen');
    }

    public function cargas()
    {
        return $this->belongsToMany(
        Carga::class,
        'bitacoras',
        'id_usuario',
        'id_carga'
    )->using(Bitacora::class)
     ->withPivot(['accion_bit', 'descripcion_bit', 'fecha_bit', 'ip_origen_bit'])
     ->withTimestamps();
    }
    use HasFactory;
}
