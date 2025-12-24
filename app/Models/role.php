<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class role extends Model
{
    protected $primaryKey = 'id_rol';
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_rol');
    }
    use HasFactory;
}
