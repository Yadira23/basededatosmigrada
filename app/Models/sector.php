<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    protected $table = 'sectores';

    protected $primaryKey = 'id_sector';

    public $timestamps = false;

    protected $fillable = [
        'nombre_sector',
        'descripcion_sector',
    ];

    // 🔗 RELACIÓN
    // Un sector tiene muchas dependencias
    public function dependencias()
    {
        return $this->hasMany(Dependencia::class, 'id_sector');
    }
}
