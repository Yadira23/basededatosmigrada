@section('title', __('Indicadores'))

<div class="container-fluid">

    @role('admin')
        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <div class="float-left">
                                <h4>
                                    <i class="bi-house-check-fill text-info"></i>
                                    Indicadore Listing
                                </h4>
                            </div>

                            @if (session()->has('message'))
                                <div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;">
                                    {{ session('message') }}
                                </div>
                            @endif

                            <div>
                                <input wire:model.live="keyWord" type="text" class="form-control" name="search"
                                    id="search" placeholder="Search Indicadores">
                            </div>

                            <div class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#DataModal">
                                <i class="bi-plus-lg"></i>
                                Add Indicadores
                            </div>

                        </div>
                    </div>

                    <div class="card-body">

                        @include('livewire.indicadores.modals')

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="thead">
                                    <tr>
                                        {{-- <td>#</td> --}}
                                        <th>Id Ind</th>
                                        <th>Nombre Ind</th>
                                        <th>Etiquetas Ind</th>
                                        {{-- <th>Definicion Ind</th>
                                    <th>Formula Ind</th> --}}
                                        <th>Tendencia Ind</th>
                                        {{-- <th>Restriccion Ind</th> --}}
                                        <th>Formato Ind</th>
                                        <th>Unidadmedida Ind</th>
                                        <th>Meta Ind</th>
                                        <th>Requerido Ind</th>
                                        <th>Status Ind</th>
                                        <th>Periodo Ind</th>
                                        <th>Fuenteverificacion Ind</th>
                                        <th>Metas</th>
                                        <th>ACTIONS</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($indicadores as $row)
                                        <tr>
                                            {{-- <td>{{ $loop->iteration }}</td> --}}
                                            <td>{{ $row->id_ind }}</td>

                                            <td>
                                                {{ $row->nombre_ind }}

                                                @php $n = $row->metas_count ?? 0; @endphp

                                                @if ($n > 0)
                                                    <span class="badge bg-info text-dark ms-1">
                                                        Gestionado por metas
                                                    </span>
                                                @else
                                                    @if (empty($row->config_campos))
                                                        <span class="badge bg-warning text-dark ms-1">
                                                            sin campos
                                                        </span>
                                                    @else
                                                        <span class="badge bg-success ms-1">
                                                            campos OK
                                                        </span>
                                                    @endif
                                                @endif
                                            </td>

                                            <td>{{ $row->etiquetas_ind }}</td>

                                            {{--
                                        <td>{{ $row->definicion_ind }}</td>
                                        <td>
                                            @if ($row->formula_ind)
                                                {{ $row->formula_ind }}
                                            @else
                                                <span class="text-muted">Sin fórmula</span>
                                            @endif
                                        </td>
                                        --}}

                                            <td>{{ $row->tendencia_ind }}</td>
                                            <td>{{ $row->formato_ind }}</td>
                                            <td>{{ $row->unidadmedida_ind }}</td>
                                            <td>{{ $row->meta_ind ?? '—' }}</td>

                                            <td>
                                                @if ($row->requerido_ind)
                                                    <span class="badge bg-danger">Obligatorio</span>
                                                @else
                                                    <span class="badge bg-secondary">Opcional</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if ($row->status_ind)
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-warning">Inactivo</span>
                                                @endif
                                            </td>

                                            <td>{{ $row->periodo_ind }}</td>
                                            <td>{{ $row->fuenteverificacion_ind }}</td>

                                            <td>
                                                @php $n = $row->metas_count ?? 0; @endphp

                                                @if ($n > 0)
                                                    <span class="badge bg-success">{{ $n }}</span>
                                                @else
                                                    <span class="badge bg-secondary">0</span>
                                                @endif
                                            </td>

                                            <td width="90">
                                                <div class="dropdown">
                                                    <a class="btn btn-sm btn-secondary dropdown-toggle" href="#"
                                                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        Actions
                                                    </a>

                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('formularios.index', ['id_ind' => $row->id_ind]) }}">
                                                                <i class="bi-send-check"></i>
                                                                Asignar a dependencia
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>

                                                        @php $n = $row->metas_count ?? 0; @endphp

                                                        @if ($n == 0)
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('indicadores.campos', ['id_ind' => $row->id_ind]) }}">
                                                                    <i class="bi-ui-checks-grid"></i>
                                                                    Configurar campos
                                                                </a>
                                                            </li>
                                                        @else
                                                            <li>
                                                                <span class="dropdown-item text-muted"
                                                                    style="cursor:not-allowed;">
                                                                    <i class="bi-ui-checks-grid"></i>
                                                                    Configurar campos (solo si no tiene metas)
                                                                </span>
                                                            </li>
                                                        @endif

                                                        <li>
                                                            <a class="dropdown-item" data-bs-toggle="modal"
                                                                data-bs-target="#DataModal"
                                                                wire:click="edit({{ $row->id_ind }})">
                                                                <i class="bi-pencil-square"></i>
                                                                Edit
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a class="dropdown-item"
                                                                onclick="confirm('Confirm Delete Indicadore id {{ $row->id_ind }}?\nDeleted Indicadores cannot be recovered!')
                                                           || event.stopImmediatePropagation()"
                                                                wire:click="destroy({{ $row->id_ind }})">
                                                                <i class="bi-trash3-fill"></i>
                                                                Delete
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.indicadores.metas', ['id_ind' => $row->id_ind]) }}">
                                                                <i class="bi-diagram-3"></i>
                                                                Metas
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="100%">
                                                No data Found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <div class="float-end">
                                {{ $indicadores->links() }}
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    @endrole


    @role('usuario')
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm border-left-info">
                    <div class="card-body text-center" style="padding:60px;">
                        <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
                        <h4>Módulo de Indicadores</h4>
                        <p class="text-muted">
                            Los indicadores asignados aparecerán aquí cuando un formulario sea publicado.
                        </p>
                        <span class="badge bg-warning text-dark">
                            Sin acceso de edición
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endrole

</div>
