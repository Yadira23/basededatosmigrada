<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formulario extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'formularios';
    protected $primaryKey = 'id_form';

    protected $fillable = [
        'titulo_form',
        'fecha_creacion_form',
        'descripcion_form',
        'boton_accion_form',
        'secciones_form',
        'periodo_form',
        'id_ind',      // 🔴 SE AGREGA
        'id_depen'
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
        return $query->where('boton_accion_form', 'ver');
    }

    // Scope para filtrar por dependencia
    public function scopePorDependencia($query, $id_depen)
    {
        return $query->where('id_depen', $id_depen);
    }
}
