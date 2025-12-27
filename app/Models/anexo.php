<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anexo extends Model
{
    use HasFactory;

    protected $table = 'anexos';
    protected $primaryKey = 'id_anexo';

    protected $fillable = [
        'nombre_anexo',
        'tipo_anexo',
        'peso_anexo',
        'guia_anexo',
        'fin_proposito_anexo',
        'fecha_subida_anexo',
        'ruta_archivo_anexo',
        'id_form'
    ];
    
    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'id_form');
    }

    public function indicadores()
    {
        return $this->hasMany(Indicador::class, 'id_anexo');
    }
}
