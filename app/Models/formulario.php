<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formulario extends Model
{
    use HasFactory;
    protected $table = 'formularios';
    protected $primaryKey = 'id_form';

    protected $fillable = [
        'titulo_form',
        'fecha_creacion_form',
        'descripcion_form',
        'boton_accion_form',
        'secciones_form',
        'periodo_form',
        'id_depen',
        'id_usr'
    ];

    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class, 'id_depen');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function cargas()
    {
        return $this->hasMany(Carga::class, 'id_form');
    }

    public function anexos()
    {
        return $this->hasMany(Anexo::class, 'id_form');
    }

    public function indicadores()
    {
        return $this->hasMany(Indicador::class, 'id_form');
    }
    
}
