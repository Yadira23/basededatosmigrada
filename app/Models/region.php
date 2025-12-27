<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $table = 'regiones';
    protected $primaryKey = 'id_region';

    protected $fillable = [
        'clave_region',
        'nombre_region'
    ];

    // 🔗 Una región tiene muchos municipios
    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'id_region');
    }
}
