<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class indicador extends Model
{
    protected $primaryKey = 'id_ind';
    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'id_form');
    }

    public function anexo()
    {
        return $this->belongsTo(Anexo::class, 'id_anexo');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCarga::class, 'id_indicador');
    }
    use HasFactory;
}
