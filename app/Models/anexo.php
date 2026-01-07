<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anexo extends Model
{
	use HasFactory;
	
    public $timestamps = true;

    protected $table = 'anexos';
    protected $primaryKey = 'id_anexo';

    protected $fillable = ['nombre_anexo','tipo_anexo','peso_anexo','guia_anexo','fin_proposito_anexo','fecha_subida_anexo','ruta_archivo_anexo','id_form'];
	
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function formulario()
    {
        return $this->hasOne('App\Models\Formulario', 'id_form', 'id_form');
    }
    
    /**
     *  @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    //public function indicadores()
    //{
    //    return $this->hasMany('App\Models\Indicadore', 'id_anexo', 'id_anexo');
    //}
    
}
