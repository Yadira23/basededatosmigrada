<?php

namespace App\Livewire\Admin\Cargas;

use App\Events\DashboardUpdated;
use App\Models\Carga;
use Livewire\Component;

class CargaRevision extends Component
{
    public $id_carga;

    public $carga;

    public $showObsModal = false;

    public $observacion = '';

    public $returnUrl;

    public $filas = [];

    public $headers = [];

    public $rows = [];

    public function mount($id_carga)
    {
        $this->id_carga = $id_carga;
        $this->returnUrl = url()->previous();
        $this->carga = Carga::findOrFail($id_carga);

        $detalles = $this->carga->detallecargas()
            ->orderBy('fila_det')
            ->get();

        $headers = [];
        $rows = [];

        foreach ($detalles as $d) {

            $campos = $this->getCamposFromDetalle($d);

            // headers: solo los campos reales del indicador
            $headers = array_unique(array_merge($headers, array_keys($campos)));

            $rows[] = [
                'num' => $d->fila_det ?? null,   // #
                'campos' => $campos,               // valores
                'fuente' => $d->fuente_det ?? null, // para mostrar método
            ];
        }

        sort($headers);

        $this->headers = $headers;
        $this->rows = $rows;
    }

    private function getCamposFromDetalle($detalle): array
    {
        // 1) payload_det puede ser JSON string o array
        $payload = $detalle->payload_det ?? null;

        if (is_string($payload)) {
            $payload = json_decode($payload, true);
        }

        if (is_array($payload) && isset($payload['campos']) && is_array($payload['campos'])) {
            return $payload['campos']; // ✅ lo que quieres mostrar
        }

        // 2) fallback: campos directo
        $campos = $detalle->campos ?? null;

        if (is_string($campos)) {
            $campos = json_decode($campos, true);
        }

        return is_array($campos) ? $campos : [];
    }

    private function normalizeCampos($campos): array
    {
        // Si viene como string JSON
        if (is_string($campos)) {
            $campos = json_decode($campos, true);
        }

        if (! is_array($campos)) {
            return [];
        }

        // Caso 1: Asociativo directo: ["pobreza_porcentaje"=>8, "pobreza_extrema_porcentaje"=>45]
        $isAssoc = array_keys($campos) !== range(0, count($campos) - 1);
        if ($isAssoc) {
            return $campos;
        }

        // Caso 2: Lista de pares/objetos: [ ["slug"=>"pobreza_porcentaje","valor"=>8], ... ]
        $out = [];
        foreach ($campos as $item) {
            if (! is_array($item)) {
                continue;
            }

            // slug / valor
            if (isset($item['slug']) && array_key_exists('valor', $item)) {
                $out[$item['slug']] = $item['valor'];

                continue;
            }

            // campo / valor
            if (isset($item['campo']) && array_key_exists('valor', $item)) {
                $out[$item['campo']] = $item['valor'];

                continue;
            }

            // Si viene como {"pobreza_porcentaje":8} dentro de la lista
            $keys = array_keys($item);
            if (count($keys) === 1) {
                $k = $keys[0];
                $out[$k] = $item[$k];
            }
        }

        return $out;
    }

    public function aprobar()
    {
        $this->carga->status_env = 'APROBADO';
        $this->carga->observacion_env = null;
        $this->carga->save();

        event(new DashboardUpdated($this->carga->formulario->id_depen));

        session()->flash('message', 'Carga aprobada.');

        return redirect()->to($this->returnUrl ?: url('/cargas')); // 👈 vuelve al listado
    }

    public function abrirObservacion()
    {
        $this->observacion = (string) ($this->carga->observacion_env ?? '');
        $this->showObsModal = true;
    }

    public function guardarObservacion()
    {
        $obs = trim((string) $this->observacion);

        $this->carga->status_env = 'OBSERVADO'; // o "CON OBSERVACIONES" si tú lo usas así
        $this->carga->observacion_env = $obs;
        $this->carga->save();

        event(new DashboardUpdated($this->carga->formulario->id_depen));

        session()->flash('message', 'Observación guardada y enviada.');

        return redirect()->to($this->returnUrl ?: url('/cargas')); // 👈 vuelve al listado
    }

    public function render()
    {
        return view('livewire.admin.cargas.carga-revision')
            ->extends('layouts.app')->section('content');
    }
}
