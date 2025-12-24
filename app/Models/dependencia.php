<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dependencia extends Model
{
    protected $primaryKey = 'id_depen';
    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'id_dep');
    }

    public function formularios()
    {
        return $this->hasMany(Formulario::class, 'id_depen');
    }
    
    public function sector()
    {
        return $this->belongsTo(Sector::class, 'id_sector');
    }
    use HasFactory;
}
