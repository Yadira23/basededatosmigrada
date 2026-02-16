<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Carga extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 'cargas';
    protected $primaryKey = 'id_carga';
    protected $fillable = [
        'folioUnico_carga',
        'fecha_carga',
        'periodo',
        'ejercicio',  // nuevo
        'fuente',     // nuevo
        'status_env',
        'descripcion_env',
        'observacion_env',
        'id_form',
        'id_meta',
        'meta_id',
        // ✅ nuevos (cargas personalizadas)
        'ambito_geo_carga',
        'metodo_captura',
        // 🆕 plantilla
        'plantilla_descargada_at',
        'plantilla_ambito',
    ];

    protected $casts = [
        'plantilla_descargada_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($carga) {
            $carga->folioUnico_carga = self::generateFolio();
        });
    }

    public static function generateFolio(): string
    {
        do {
            // 6 letras MAYÚSCULAS
            $folio = Str::upper(Str::random(6));
        } while (self::where('folioUnico_carga', $folio)->exists());

        return $folio;
    }

    /** * @return \Illuminate\Database\Eloquent\Relations\HasMany */
    public function bitacoras()
    {
        return $this->hasMany('App\Models\Bitacora', 'id_carga', 'id_carga');
    }

    /** * @return \Illuminate\Database\Eloquent\Relations\HasMany */
    public function detallecargas()
    {
        return $this->hasMany('App\Models\DetalleCarga', 'id_carga', 'id_carga');
    }

    // Primer detalle (para sacar indicador y ámbito de la carga)
    public function primerDetalle()
    {
        return $this->hasOne(\App\Models\DetalleCarga::class, 'id_carga', 'id_carga')
            ->oldestOfMany('id_detalle'); // el más antiguo (el primero insertado)
    }

    /** * @return \Illuminate\Database\Eloquent\Relations\HasOne */
    public function formulario()
    {
        return $this->belongsTo('App\Models\Formulario', 'id_form', 'id_form');
    }

    public function meta()
    {
        return $this->belongsTo(\App\Models\Meta::class, 'id_meta', 'id');
    }
}
