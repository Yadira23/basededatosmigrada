<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anexo extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'anexos';

    protected $primaryKey = 'id_anexo';

    const TIPO_PLANTILLA = 'plantilla';

    const TIPO_GUIA = 'guia';

    protected $fillable = ['nombre_anexo', 'tipo_anexo', 'peso_anexo', 'guia_anexo', 'fin_proposito_anexo', 'fecha_subida_anexo', 'ruta_archivo_anexo', 'id_form', 'id_ind'];

    protected $casts = [
        'fecha_subida_anexo' => 'date',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function formulario()
    {
        return $this->belongsTo('App\Models\Formulario', 'id_form', 'id_form');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function indicador()
    {
        return $this->belongsTo('App\Models\Indicador', 'id_ind', 'id_ind');
    }
}
