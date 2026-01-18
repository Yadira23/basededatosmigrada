<?php

namespace App\Observers;

use App\Models\Carga;
use App\Models\DetalleCarga;
use App\Models\Indicador;

class CargaObserver
{
    /**
     * Handle the Carga "created" event.
     */
    public function created(Carga $carga): void
    {
        foreach(Indicador::all() as $indicador) {
            DetalleCarga::create([
                'id_carga' => $carga->id_carga,
                'id_ind' => $indicador->id_ind,
                'periodo_det' => $carga->periodo,
                'ejercicio_det' => $carga->ejercicio,
                'fecha_registro_det' => now(),
                'fuente_det' => $carga->fuente,
                'valor_det' => 0  // valor inicial
            ]);
        }
    }

    /**
     * Handle the Carga "updated" event.
     */
    public function updated(Carga $carga): void
    {
        //
    }

    /**
     * Handle the Carga "deleted" event.
     */
    public function deleted(Carga $carga): void
    {
        //
    }

    /**
     * Handle the Carga "restored" event.
     */
    public function restored(Carga $carga): void
    {
        //
    }

    /**
     * Handle the Carga "force deleted" event.
     */
    public function forceDeleted(Carga $carga): void
    {
        //
    }
}
