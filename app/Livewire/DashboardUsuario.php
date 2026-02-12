<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Formulario;
use App\Models\Carga;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardUsuario extends Component
{
    // KPI existentes
    public int $formulariosDisponibles = 0;
    public int $cargasRealizadas = 0;

    // NUEVOS (los usa tu blade)
    public ?string $dependenciaNombre = null;
    public int $pendientes = 0;
    public int $observaciones = 0;
    public $ultimasCargas = [];
    public $metaIndicadores = 0;
    public $indicadoresCompletados = 0;
    public $porcentajeAvance = 0;

    public function mount()
    {
        $user = Auth::user();
        $idDepen = $user->id_depen;

        // Nombre de dependencia
        $this->dependenciaNombre = $user->dependencia->nombre_depen ?? null;

        // Formularios disponibles
        $this->formulariosDisponibles = Formulario::whereIn('boton_accion_form', ['Publicado', 'Ver'])
            ->where('id_depen', $idDepen)
            ->count();

        // Base: cargas de la dependencia
        $base = Carga::whereHas('formulario', function ($q) use ($idDepen) {
            $q->where('id_depen', $idDepen);
        });

        $this->cargasRealizadas = (clone $base)->count();

        // Últimas cargas
        $this->ultimasCargas = (clone $base)
            ->leftJoin('detallecargas as dc', 'dc.id_carga', '=', 'cargas.id_carga')
            ->select('cargas.*', DB::raw('MIN(dc.id_ind) as id_ind'))
            ->groupBy('cargas.id_carga')
            ->latest('cargas.created_at')
            ->take(5)
            ->get();

        /* =========================================================
       ✅ AVANCE vs META (por formularios)
    ========================================================= */

        // 1) META
        $this->metaIndicadores = Formulario::publicados()
            ->where('id_depen', $idDepen)
            ->distinct('id_form')
            ->count('id_form');

        // 2) COMPLETADOS (sin contar BORRADOR)
        $estatusValidos = ['ENVIADO', 'EN REVISION', 'REENVIADO', 'APROBADO'];

        $this->indicadoresCompletados = (clone $base)
            ->whereIn('cargas.status_env', $estatusValidos)
            ->whereNotNull('cargas.id_form') // por seguridad
            ->distinct('cargas.id_form')
            ->count('cargas.id_form');

        // 3) %
        $this->porcentajeAvance = $this->metaIndicadores > 0
            ? (int) round(($this->indicadoresCompletados / $this->metaIndicadores) * 100)
            : 0;

        /* =========================================================
       ✅ Pendientes / Observaciones (ya con meta/completados listos)
    ========================================================= */

        // ✅ Pendientes reales = meta - completados
        $this->pendientes = max(0, (int)$this->metaIndicadores - (int)$this->indicadoresCompletados);

        // ⚠️ Ajusta estos status a los reales (en tu UI usas OBSERVADO)
        $this->observaciones = (clone $base)->whereIn('status_env', ['OBSERVADO', 'RECHAZADO'])->count();
    }

    public function render()
    {
        return view('livewire.dashboard-usuario');
    }

    // Helper para color del badge (lo usamos en la vista)
    public function badgeClass($status): string
    {
        $st = strtolower((string) $status);

        if (str_contains($st, 'rech')) return 'danger';
        if (str_contains($st, 'obs'))  return 'warning';
        if (str_contains($st, 'pend') || str_contains($st, 'borr')) return 'secondary';
        if (str_contains($st, 'env'))  return 'primary';
        if (str_contains($st, 'aprob') || str_contains($st, 'final')) return 'success';

        return 'primary';
    }
}
