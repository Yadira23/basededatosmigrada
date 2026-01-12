<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicador extends Model
{
    use HasFactory;
    protected $table = 'indicadores';
    protected $primaryKey = 'id_ind';

    protected $fillable = [
        'nombre_ind',
        'definicion_ind',
        'formula_ind',
        'tendencia_ind',
        'restriccion_ind',
        'formato_ind',
        'unidadmedida_ind',
        'meta_ind',
        'requerido_ind',
        'status_ind',
        'periodo_ind',
        'etiquetas_ind',
        'fuenteverificacion_ind',
        'id_form'
    ];
    
    // Constantes
    const FORMATOS = ['porcentaje', 'entero', 'decimal'];
    const PERIODOS = ['mensual', 'trimestral', 'semestral', 'anual'];
    const TENDENCIAS = ['ascendente', 'descendente'];

    public function formulario()
    {
        return $this->belongsTo(Formulario::class, 'id_form');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCarga::class, 'id_ind');
    }
    
    // Scope
    public function scopeActivos($query)
    {
        return $query->where('status_ind', true);
    }

    // Accesor
    public function getUnidadmedidaIndAttribute($value)
    {
        if ($value) return $value;

        return match ($this->formato_ind) {
            'porcentaje' => '%',
            'entero'     => 'entero',
            'decimal'    => 'decimal',
            default      => null,
        };
    }
}
