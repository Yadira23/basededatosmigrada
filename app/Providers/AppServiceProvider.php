<?php

namespace App\Providers;

use App\Livewire\UsuarioFormularios;
use App\Models\Carga;
use App\Observers\CargaObserver;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carga::observe(CargaObserver::class);
        Livewire::component('usuario-formularios', UsuarioFormularios::class);

        \Livewire\Livewire::component(
            'usuarios.notificaciones-usuario',
            \App\Livewire\Usuario\NotificacionesUsuario::class
        );
    }
}
