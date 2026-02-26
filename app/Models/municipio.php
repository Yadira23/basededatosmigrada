<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    protected $table = 'municipios';

    protected $primaryKey = 'id_mun';

    protected $fillable = [
        'clave_municipio',
        'nombre_municipio',
        'id_region',
    ];

    // 🔗 Un municipio pertenece a una región
    public function region()
    {
        return $this->belongsTo(Region::class, 'id_region');
    }
}
