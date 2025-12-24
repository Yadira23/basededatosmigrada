<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class formulario extends Model
{
    protected $primaryKey = 'id_form';
    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class, 'id_depen');
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
    use HasFactory;
}
