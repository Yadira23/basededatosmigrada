<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapeoIndicador extends Model
{
    protected $table = 'mapeos_indicador';

    protected $primaryKey = 'id_mapeo';

    protected $fillable = [
        'id_ind', 'id_depen',
        'col_cve_mun', 'col_municipio', 'col_region',
        'map_campos',
    ];

    protected $casts = [
        'map_campos' => 'array',
    ];
}
