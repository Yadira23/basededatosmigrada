<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
	use HasFactory;
	
    public $timestamps = true;
    
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = ['usuario_usr','nombre_usr','apellido_paterno','apellido_materno','email_usr', 'password', 'id_depen','id_rol','estado_usr','telefono_usr'];
	
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bitacora()
    {
        return $this->hasOne('App\Models\Bitacora', 'id_usuario', 'id_usuario');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function dependencia()
    {
        return $this->hasOne('App\Models\Dependencia', 'id_depen', 'id_depen');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function formularios()
    {
        return $this->hasMany('App\Models\Formulario', 'id_usr', 'id_usuario');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function role()
    {
        return $this->hasOne('App\Models\Role', 'id_rol', 'id_rol');
    }
    
}
