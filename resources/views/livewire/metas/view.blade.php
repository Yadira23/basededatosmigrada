@section('title', __('Metas'))

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;">

                        <div class="float-left">
                            <h4 class="mb-0">
                                <i class="bi-diagram-3 text-info"></i>
                                Metas del indicador
                            </h4>
                            <small class="text-muted">
                                Indicador: <strong>{{ $indicador->nombre_ind }}</strong> · Periodo:
                                {{ $indicador->periodo_ind }}
                            </small>
                        </div>

                        @if (session()->has('message'))
                            <div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px;margin-bottom:0px;">
                                {{ session('message') }}
                            </div>
                        @endif

                        <div>
                            <input wire:model.live="keyWord" type="text" class="form-control"
                                placeholder="Buscar metas...">
                        </div>

                        <div class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#MetaModal"
                            wire:click="create">
                            <i class="bi-plus-lg"></i>
                            Nueva Meta
                        </div>

                    </div>
                </div>

                <div class="card-body">

                    @include('livewire.metas.modals')

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead">
                                <tr>
                                    <th>Id Meta</th>
                                    <th>Orden</th>
                                    <th>Título</th>
                                    <th>Periodo</th>
                                    <th>Campos</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($metas as $row)
                                    @php
                                        $campos = $row->config_campos;
                                        if (is_string($campos)) {
                                            $campos = json_decode($campos, true) ?: [];
                                        }
                                        $numCampos = is_array($campos) ? count($campos) : 0;
                                    @endphp

                                    <tr>
                                        <td>{{ $row->id }}</td>
                                        <td>{{ $row->orden }}</td>
                                        <td>{{ $row->titulo }}</td>
                                        <td>{{ $row->periodo_label }}</td>

                                        <td>
                                            @if ($numCampos <= 0)
                                                <span class="badge bg-warning text-dark">sin campos</span>
                                            @else
                                                <span class="badge bg-success"> {{ $numCampos }} campos </span>
                                            @endif
                                        </td>

                                        <td width="120">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-secondary dropdown-toggle" href="#"
                                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                </a>

                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" data-bs-toggle="modal"
                                                            data-bs-target="#CamposModal"
                                                            wire:click="openCampos({{ $row->id }})">
                                                            <i class="bi-ui-checks-grid"></i>
                                                            Configurar campos
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item" data-bs-toggle="modal"
                                                            data-bs-target="#MetaModal"
                                                            wire:click="edit({{ $row->id }})">
                                                            <i class="bi-pencil-square"></i>
                                                            Editar
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item"
                                                            onclick="confirm('¿Eliminar Meta ID {{ $row->id }}?') || event.stopImmediatePropagation()"
                                                            wire:click="destroy({{ $row->id }})">
                                                            <i class="bi-trash3-fill"></i>
                                                            Eliminar
                                                        </a>
                                                    </li>

                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="100%">No hay metas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="float-end">
                            {{ $metas->links() }}
                        </div>

                        <div class="mt-3">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ url('/indicadores') }}">
                                ← Volver a Indicadores
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
