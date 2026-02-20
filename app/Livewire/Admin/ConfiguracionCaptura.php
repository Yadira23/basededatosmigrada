<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Configuracion;

class ConfiguracionCaptura extends Component
{
    public $modo_pruebas = false;
    public $dias_gracia = 0;

    public $dias_mensual = 60;
    public $dias_trimestral = 120;
    public $dias_semestral = 180;
    public $dias_anual = 365;

    public function mount()
    {
        $this->modo_pruebas  = config_bool('CAPTURA_MODO_PRUEBAS', false);
        $this->dias_gracia   = config_int('CAPTURA_DIAS_GRACIA', 0);

        $this->dias_mensual     = config_int('CAPTURA_DIAS_APERTURA_MENSUAL', 60);
        $this->dias_trimestral  = config_int('CAPTURA_DIAS_APERTURA_TRIMESTRAL', 120);
        $this->dias_semestral   = config_int('CAPTURA_DIAS_APERTURA_SEMESTRAL', 180);
        $this->dias_anual       = config_int('CAPTURA_DIAS_APERTURA_ANUAL', 365);
    }

    public function guardar()
    {
        $this->validate([
            'modo_pruebas' => 'boolean',
            'dias_gracia' => 'integer|min:0|max:3650',
            'dias_mensual' => 'integer|min:1|max:3650',
            'dias_trimestral' => 'integer|min:1|max:3650',
            'dias_semestral' => 'integer|min:1|max:3650',
            'dias_anual' => 'integer|min:1|max:3650',
        ]);

        $this->upsert('CAPTURA_MODO_PRUEBAS', $this->modo_pruebas ? '1' : '0', 'bool', 'Si = permite capturar aunque esté fuera de fechas.');
        $this->upsert('CAPTURA_DIAS_GRACIA', (string)$this->dias_gracia, 'int', 'Días extra después del cierre para permitir capturas.');

        $this->upsert('CAPTURA_DIAS_APERTURA_MENSUAL', (string)$this->dias_mensual, 'int', 'Días que permanece abierta la captura para periodos mensuales.');
        $this->upsert('CAPTURA_DIAS_APERTURA_TRIMESTRAL', (string)$this->dias_trimestral, 'int', 'Días que permanece abierta la captura para periodos trimestrales.');
        $this->upsert('CAPTURA_DIAS_APERTURA_SEMESTRAL', (string)$this->dias_semestral, 'int', 'Días que permanece abierta la captura para periodos semestrales.');
        $this->upsert('CAPTURA_DIAS_APERTURA_ANUAL', (string)$this->dias_anual, 'int', 'Días que permanece abierta la captura para periodos anuales.');

        session()->flash('success', 'Configuración de captura guardada.');
    }

    private function upsert($clave, $valor, $tipo, $descripcion)
    {
        Configuracion::updateOrCreate(
            ['clave' => $clave],
            ['valor' => $valor, 'tipo' => $tipo, 'descripcion' => $descripcion]
        );
    }

    public function render()
    {
        return view('livewire.admin.configuracion-captura')
            ->extends('layouts.app')->section('content');
    }
}
