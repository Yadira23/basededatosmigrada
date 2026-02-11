<div>
    @if (session()->has('message_notif'))
    <div class="alert alert-info mb-3">
        {{ session('message_notif') }}
    </div>
    @endif

    <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="d-flex align-items-center">
            <h5 class="mb-0 mr-2">Notificaciones</h5>
            @if($noLeidas > 0)
            <span class="badge badge-danger">{{ $noLeidas }}</span>
            @endif
        </div>

        <div class="d-flex align-items-center">
            <div class="custom-control custom-switch mr-2">
                <input type="checkbox" class="custom-control-input" id="soloNoLeidas"
                    wire:model="soloNoLeidas">
                <label class="custom-control-label" for="soloNoLeidas">Solo no leídas</label>
            </div>

            @if($noLeidas > 0)
            <button class="btn btn-sm btn-outline-secondary" wire:click="marcarTodas">
                <i class="fas fa-check mr-1"></i> Marcar todas
            </button>
            @endif
        </div>
    </div>

    @if(empty($notificaciones))
    <div class="card shadow-sm">
        <div class="card-body text-muted">
            No tienes notificaciones {{ $soloNoLeidas ? 'pendientes' : '' }}.
        </div>
    </div>
    @else
    <div class="list-group shadow-sm">
        @foreach($notificaciones as $n)
        <div class="list-group-item">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="font-weight-bold text-gray-800">
                        <i class="fas fa-bell mr-1"></i> {{ $n->titulo }}
                    </div>
                    <div class="text-muted small mt-1">
                        {{ \Carbon\Carbon::parse($n->created_at)->format('d/m/Y H:i') }}
                    </div>
                </div>

                <div class="text-right">
                    @if(!$n->leida)
                    <button class="btn btn-sm btn-outline-primary"
                        wire:click="marcarComoLeida({{ (int)$n->id_notificacion }})">
                        <i class="fas fa-check mr-1"></i> Marcar leída
                    </button>
                    @else
                    <span class="badge badge-light">Leída</span>
                    @endif
                </div>
            </div>

            <div class="mt-2">
                {{ $n->mensaje }}
            </div>

            @if(strtoupper($n->tipo ?? '') === 'RECORDATORIO' && !empty($borradoresUsuario))
            <div class="mt-3">
                <div class="text-muted small mb-2">
                    <i class="fas fa-list mr-1"></i> Borradores pendientes (mostrando {{ count($borradoresUsuario) }})
                </div>

                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Folio</th>
                                <th>Periodo</th>
                                <th>Actualización</th>
                                <th style="width:140px;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($borradoresUsuario as $b)
                            @php
                            // necesitamos id_ind para armar "Continuar"
                            $id_ind = \App\Models\Formulario::where('id_form', $b->id_form)->value('id_ind');
                            @endphp
                            <tr>
                                <td class="font-weight-bold">{{ $b->folioUnico_carga ?? $b->id_carga }}</td>
                                <td>{{ $b->periodo ?? '—' }} / {{ $b->ejercicio ?? '—' }}</td>
                                <td class="text-muted">{{ optional($b->updated_at)->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a class="btn btn-sm btn-success"
                                        href="{{ route('usuario.formulario.captura', ['id_form' => $b->id_form, 'id_ind' => $id_ind]) }}?id_carga={{ $b->id_carga }}">
                                        <i class="fas fa-play mr-1"></i> Continuar
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-muted small mt-2">
                    Tip: entra a “Actividad reciente” para ver más detalles si no aparece aquí.
                </div>
            </div>
            @endif

        </div>
        @endforeach
    </div>
    @endif
</div>