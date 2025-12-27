<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;


class Bitacora extends Pivot
{
    use HasFactory;
    protected $table = 'bitacoras';
    protected $primaryKey = 'id_bitacora';

    protected $fillable = [
        'id_usuario',
        'id_carga',
        'accion_bit',
        'descripcion_bit',
        'fecha_bit',
        'ip_origen_bit'
    ];
    public $timestamps = true;
}
