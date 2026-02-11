<?php

namespace App\Livewire\Usuario;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificacionesUsuario extends Component
{
    public $notificaciones = [];
    public $borradoresUsuario = [];
    public $soloNoLeidas = true;

    public function mount()
    {
        $this->cargar();
    }

    public function updatedSoloNoLeidas()
    {
        $this->cargar();
    }

    public function cargar()
    {
        $user = Auth::user();

        if (!$user) {
            $this->notificaciones = [];
            return;
        }

        $q = DB::table('notificaciones')
            ->where('id_usuario', $user->id_usuario)
            ->orderByDesc('created_at');

        if ($this->soloNoLeidas) {
            $q->where('leida', 0);
        }

        $this->notificaciones = $q->limit(20)->get()->toArray();

        // ✅ BORRADORES DEL USUARIO (máximo 5)
        $this->borradoresUsuario = DB::table('cargas')
            ->where('status_env', 'BORRADOR')
            ->whereIn('id_form', function ($q) use ($user) {
                $q->select('id_form')
                    ->from('formularios')
                    ->where('id_depen', $user->id_depen);
            })
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function marcarComoLeida($id_notificacion)
    {
        $user = Auth::user();
        if (!$user) return;

        DB::table('notificaciones')
            ->where('id_notificacion', $id_notificacion)
            ->where('id_usuario', $user->id_usuario)
            ->update([
                'leida' => 1,
                'updated_at' => now(),
            ]);

        $this->cargar();
        session()->flash('message_notif', 'Notificación marcada como leída.');
    }

    public function marcarTodas()
    {
        $user = Auth::user();
        if (!$user) return;

        DB::table('notificaciones')
            ->where('id_usuario', $user->id_usuario)
            ->where('leida', 0)
            ->update([
                'leida' => 1,
                'updated_at' => now(),
            ]);

        $this->cargar();
        session()->flash('message_notif', 'Todas las notificaciones fueron marcadas como leídas.');
    }

    public function render()
    {
        $user = Auth::user();

        $noLeidas = 0;
        if ($user) {
            $noLeidas = DB::table('notificaciones')
                ->where('id_usuario', $user->id_usuario)
                ->where('leida', 0)
                ->count();
        }

        return view('livewire.usuarios.notificaciones-usuario', [
            'noLeidas' => $noLeidas,
        ]);
    }
}
