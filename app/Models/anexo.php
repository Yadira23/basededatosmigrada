<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class anexo extends Model
{
    protected $primaryKey = 'id_anexo';
    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'id_form');
    }

    public function indicadores()
    {
        return $this->hasMany(Indicador::class, 'id_anexo');
    }
    use HasFactory;
}
