<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCarga extends Model
{
	use HasFactory;
	
    public $timestamps = true;

    protected $table = 'detallecargas';
    protected $primaryKey = 'id_detalle';

    protected $fillable = ['id_carga','id_ind','periodo_det','ejercicio_det','fecha_registro_det','fuente_det','valor_det'];
	
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function carga()
    {
        return $this->belongsTo('App\Models\Carga', 'id_carga', 'id_carga');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function indicador()
    {
        return $this->belongsTo('App\Models\Indicador', 'id_ind', 'id_ind');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    //public function municipio()
    //{
    //    return $this->hasOne('App\Models\Municipio', 'id_mun', 'id_mun');
    //}
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    //public function regione()
    //{
    //    return $this->hasOne('App\Models\Regione', 'id_region', 'id_region');
    //}
    
}
