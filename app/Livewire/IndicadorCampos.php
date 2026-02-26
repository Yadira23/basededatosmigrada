<?php

namespace App\Livewire;

use App\Models\Indicador;
use Livewire\Component;

class IndicadorCampos extends Component
{
    public $id_ind;

    public Indicador $indicador;

    public array $campos = [];

    public function mount($id_ind)
    {
        $this->id_ind = $id_ind;
        $this->indicador = Indicador::findOrFail($id_ind);

        $raw = $this->indicador->config_campos ?? [];
        if (is_string($raw)) {
            $raw = json_decode($raw, true) ?: [];
        }

        $this->campos = is_array($raw) ? $raw : [];

        // Si está vacío, mete 1 campo por defecto para que se vea
        if (empty($this->campos)) {
            $this->agregarCampo();
        }
    }

    public function agregarCampo()
    {
        $this->campos[] = [
            'slug' => '',
            'label' => '',
            'type' => 'number',   // number | text | porcentaje
            'required' => false,
            'min' => null,
            'max' => null,
        ];
    }

    public function eliminarCampo($index)
    {
        unset($this->campos[$index]);
        $this->campos = array_values($this->campos);
    }

    public function guardar()
    {
        // validar: slug y label obligatorios, slug único, slug “bonito”
        $slugs = [];

        foreach ($this->campos as $i => $c) {
            $min = $c['min'] ?? null;
            $max = $c['max'] ?? null;

            if ($min !== null && $min !== '' && ! is_numeric($min)) {
                session()->flash('error', 'Min inválido en fila '.($i + 1));

                return;
            }
            if ($max !== null && $max !== '' && ! is_numeric($max)) {
                session()->flash('error', 'Max inválido en fila '.($i + 1));

                return;
            }

            $min = ($min !== null && $min !== '') ? (float) $min : null;
            $max = ($max !== null && $max !== '') ? (float) $max : null;

            $type = strtolower(trim((string) ($c['type'] ?? 'text')));
            $label = trim((string) ($c['label'] ?? ''));

            // ✅ regla: si es number/cantidad, NO permitir min negativo
            // number y porcentaje NO pueden tener min negativo (según tu regla)
            // ✅ Regla: solo porcentaje no permite min negativo
            if ($type === 'porcentaje' && $min !== null && $min < 0) {
                session()->flash('error', "Min no puede ser negativo en '{$label}' (fila ".($i + 1).').');

                return;
            }

            // ✅ si min y max están, min no puede ser mayor a max
            if ($min !== null && $max !== null && $min > $max) {
                session()->flash('error', "Min no puede ser mayor que Max en '{$c['label']}' (fila ".($i + 1).').');

                return;
            }

            // (opcional) regresa los valores normalizados al array para guardar bien
            $this->campos[$i]['min'] = $min;
            $this->campos[$i]['max'] = $max;

            $slug = trim((string) ($c['slug'] ?? ''));
            $label = trim((string) ($c['label'] ?? ''));
            $type = $c['type'] ?? 'text';

            if ($slug === '' || $label === '') {
                session()->flash('error', 'Hay campos vacíos (fila '.($i + 1).').');

                return;
            }

            // slug solo letras/números/guion_bajo
            if (! preg_match('/^[a-z0-9_]+$/', $slug)) {
                session()->flash('error', "Slug inválido '{$slug}'. Usa solo a-z, 0-9 y _");

                return;
            }

            if (in_array($slug, $slugs, true)) {
                session()->flash('error', "Slug duplicado: {$slug}");

                return;
            }
            $slugs[] = $slug;

            if (! in_array($type, ['number', 'text', 'porcentaje'], true)) {
                session()->flash('error', "Tipo inválido en {$slug}");

                return;
            }
        }

        $this->indicador->config_campos = $this->campos;
        $this->indicador->save();

        session()->flash('success', 'Campos guardados correctamente.');
    }

    public function render()
    {
        return view('livewire.admin.indicador-campos')->extends('layouts.app')->section('content');
    }
}
