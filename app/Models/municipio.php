<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class municipio extends Model
{
    protected $primaryKey = 'id_municipio';
    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'id_region');
    }
    use HasFactory;
}
