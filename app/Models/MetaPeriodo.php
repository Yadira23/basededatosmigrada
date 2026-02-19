<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaPeriodo extends Model
{
    use HasFactory;

    protected $table = 'metas_periodo';

    protected $fillable = [
        'id_ind',
        'ejercicio',
        'periodicidad',
        'segmento',
        'meta',
    ];

    public function indicador()
    {
        return $this->belongsTo(Indicador::class, 'id_ind', 'id_ind');
    }
}
