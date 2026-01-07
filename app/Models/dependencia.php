<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dependencia extends Model
{
	use HasFactory;
	
    public $timestamps = true;
    protected $table = 'dependencias';
    protected $primaryKey = 'id_depen';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['nombre_depen','id_sector','email_depen','extension_depen','telefono_depen','calle_depen','numerocalle_depen','colonia_depen','cp_depen'];
	
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function formularios()
    {
        return $this->hasMany('App\Models\Formulario', 'id_depen', 'id_depen');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sectore()
    {
        return $this->hasOne('App\Models\Sector', 'id_sector', 'id_sector');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function usuario()
    {
        return $this->hasOne('App\Models\Usuario', 'id_depen', 'id_depen');
    }
    
}
