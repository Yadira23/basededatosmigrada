<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formulario extends Model
{
	use HasFactory;
	
    public $timestamps = true;

    protected $table = 'formularios';
    protected $primaryKey = 'id_form';

    protected $fillable = ['titulo_form','fecha_creacion_form','descripcion_form','boton_accion_form','secciones_form','periodo_form','id_depen','id_usr'];
	
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function anexos()
    {
        return $this->hasMany('App\Models\Anexo', 'id_form', 'id_form');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cargas()
    {
        return $this->hasMany('App\Models\Carga', 'id_form', 'id_form');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    // Formulario -> Dependencia (N:1)
    public function dependencia()
    {
        return $this->belongsTo(Dependencia::class, 'id_depen', 'id_depen');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function indicadores()
    {
        return $this->hasMany('App\Models\Indicadore', 'id_form', 'id_form');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    // Formulario -> Usuario (N:1)
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usr', 'id_usuario');
    }
    
}
