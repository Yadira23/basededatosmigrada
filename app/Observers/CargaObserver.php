<?php

namespace App\Observers;

use App\Models\Carga;

class CargaObserver
{
    /**
     * Handle the Carga "created" event.
     */
    public function created(Carga $carga): void {}

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
