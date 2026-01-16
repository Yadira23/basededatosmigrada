<?php 

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class Usuario extends Authenticatable
{
	use HasFactory;
	use HasRoles;
    public $timestamps = true;
    
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    protected $guard_name = 'web';

    protected $fillable = ['usuario_usr','nombre_usr','apellido_paterno','apellido_materno','email_usr', 'password', 'id_depen','estado_usr','telefono_usr'];
	
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
        return $this->belongsTo('App\Models\Dependencia', 'id_depen', 'id_depen');
    }
    
    
   
    
}
