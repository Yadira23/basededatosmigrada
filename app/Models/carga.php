<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carga extends Model
{
	use HasFactory;
	
    public $timestamps = true;

    protected $table = 'cargas';
    protected $primaryKey = 'id_carga';

    protected $fillable = ['folioUnico_carga','fecha_carga','periodo','status_env','descripcion_env','observacion_env','id_form'];
	
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bitacoras()
    {
        return $this->hasMany('App\Models\Bitacora', 'id_carga', 'id_carga');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detallecargas()
    {
        return $this->hasMany('App\Models\Detallecarga', 'id_carga', 'id_carga');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function formulario()
    {
        return $this->belongsTo('App\Models\Formulario', 'id_form', 'id_form');
    }
    
}
