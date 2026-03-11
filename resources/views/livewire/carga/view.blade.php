@section('title', __('Cargas')) <div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <h4><i class="bi-house-check-fill text-info"></i> Carga Listing </h4>
                        </div>
                        @if (session()->has('message'))
                            <div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;">
                                {{ session('message') }} </div>
                        @endif
                        <div class="d-flex align-items-center gap-2 justify-content-end">

                            {{-- Filtro Dependencia --}}
                            <select wire:model.live="filtroDependencia" class="form-select form-select-sm"
                                style="min-width:240px;">
                                <option value="">Todas las dependencias</option>
                                @foreach ($dependencias as $d)
                                    <option value="{{ $d->id_depen }}">{{ $d->nombre_depen }}</option>
                                @endforeach
                            </select>

                            {{-- Buscador --}}
                            <input wire:model.live="keyWord" type="text" class="form-control form-control-sm"
                                style="min-width:220px;" name="search" id="search"
                                placeholder="Buscar folio, periodo, estatus...">

                            {{-- Limpiar --}}
                            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="limpiarFiltros">
                                Limpiar
                            </button>

                        </div>
                        {{-- <div class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#DataModal"> <i
                                class="bi-plus-lg"></i> Add Cargas </div>
                    </div> --}}
                    </div>
                    <div class="card-body"> @include('livewire.carga.modals') @include('livewire.carga.observation-modal')<div
                            class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="thead">
                                    <tr>
                                        {{-- <td>#</td> --}}
                                        <th>Id Carga</th>
                                        <th>Foliounico Carga</th>
                                        <th>Fecha Carga</th>
                                        <th>Periodo</th>
                                        <th>Ejercicio</th>
                                        <th>Fuente</th>
                                        <th>Indicador</th>
                                        <th>Meta</th>
                                        <th>Ámbito</th>
                                        <th>Filas</th>
                                        <th>Status Env</th>
                                        <th>Descripcion Env</th>
                                        <th>Observacion Env</th>
                                        <th>Formulario</th>
                                        <td>ACTIONS</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cargas as $row)
                                        <tr>
                                            {{-- <td>{{ $loop->iteration }}</td> --}}
                                            <td>{{ $row->id_carga }}</td>
                                            <td>{{ $row->folioUnico_carga }}</td>
                                            <td>{{ $row->fecha_carga }}</td>
                                            <td>{{ $row->periodo }}</td>
                                            <td>{{ $row->ejercicio }}</td>
                                            <td>
                                                {{ $row->primerDetalle?->fuente_det ?? ($row->fuente ?? 'N/D') }}
                                            </td>
                                            <td>
                                                @php
                                                    $indNombre =
                                                        $row->primerDetalle?->indicador?->nombre_ind ??
                                                        ($row->meta?->indicador?->nombre_ind ??
                                                            ($row->formulario?->indicador?->nombre_ind ?? 'N/A'));
                                                @endphp

                                                {{ $indNombre }}
                                            </td>
                                            <td>
                                                @if ($row->meta)
                                                    <span class="badge bg-dark">#{{ $row->meta->id }}</span>
                                                    <small class="ms-1">{{ $row->meta->titulo }}</small>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>
                                                @php $amb = $row->primerDetalle?->ambito_geo ?? $row->ambito_geo_carga ?? null; @endphp

                                                @if ($amb === 'REGION')
                                                    <span class="badge bg-primary">REGIÓN</span>
                                                @elseif($amb === 'MUNICIPIO')
                                                    <span class="badge bg-warning text-dark">MUNICIPIO</span>
                                                @elseif($amb === 'SIN_AMBITO')
                                                    <span class="badge bg-secondary">ESTATAL</span>
                                                @else
                                                    <span class="badge bg-light text-dark">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info text-dark">
                                                    {{ $row->detallecargas_count }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    // Normaliza para que funcione aunque en BD venga "Enviado", "En Revisión", etc.
                                                    $st = mb_strtoupper(trim((string) ($row->status_env ?? '')));
                                                    // Quita acento si llega "REVISIÓN"
                                                    $st = str_replace('REVISIÓN', 'REVISION', $st);
                                                @endphp

                                                @if ($st === 'ENVIADO' || $st === 'REENVIADO')
                                                    <a href="{{ route('admin.cargas.revision', $row->id_carga) }}"
                                                        class="btn btn-sm btn-warning">
                                                        Revisar
                                                    </a>
                                                @elseif($st === 'EN REVISION' || $st === 'REENVIADO')
                                                    <button wire:click="aprobar({{ $row->id_carga }})"
                                                        class="btn btn-sm btn-success">Aprobar</button>

                                                    <button wire:click="openObservation({{ $row->id_carga }})"
                                                        class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                        data-bs-target="#ObservationModal">
                                                        Observar
                                                    </button>
                                                @elseif($st === 'OBSERVADO')
                                                    <button class="btn btn-sm btn-danger" disabled>OBSERVADO</button>
                                                @elseif($st === 'APROBADO')
                                                    <button class="btn btn-sm btn-success" disabled>APROBADO</button>
                                                @elseif($st === 'BORRADOR')
                                                    <span class="badge bg-secondary">BORRADOR</span>
                                                @else
                                                    <span class="text-muted">{{ $row->status_env ?? '—' }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $row->descripcion_env }}</td>

                                            <td>
                                                {{-- Texto de la observación --}}
                                                @if ($row->observacion_env)
                                                    <span class="text-success">
                                                        {{ $row->observacion_env }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">
                                                        Sin observación
                                                    </span>
                                                @endif

                                                <br>

                                                {{-- Botón dinámico --}}
                                                <button
                                                    class="btn btn-sm mt-1
                                            {{ $row->observacion_env ? 'btn-outline-warning' : 'btn-secondary' }}"
                                                    data-bs-toggle="modal" data-bs-target="#ObservationModal"
                                                    wire:click="openObservation({{ $row->id_carga }})">

                                                    {{ $row->observacion_env ? 'Editar observación' : 'Agregar observación' }}
                                                </button>
                                            </td>


                                            <td>{{ $row->formulario ? $row->formulario->titulo_form : 'N/A' }}</td>

                                            <td width="120">
                                                <div class="dropdown">
                                                    <a class="btn btn-sm btn-secondary dropdown-toggle" href="#"
                                                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        Actions
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                        {{--<li><a data-bs-toggle="modal" data-bs-target="#DataModal"
                                                                class="dropdown-item"
                                                                wire:click="edit({{ $row->id_carga }})"><i
                                                                    class="bi-pencil-square"></i> Edit </a></li>
                                                        <li>--}}
                                                            <a class="dropdown-item"
                                                                href="{{ route('detallecargas.index', ['id_carga' => $row->id_carga]) }}">
                                                                <i class="bi-list-ul"></i> Ver detalles
                                                            </a>
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                                onclick="confirm('Confirm Delete Carga id {{ $row->id_carga }}? \nDeleted Cargas cannot be recovered!')||event.stopImmediatePropagation()"
                                                                wire:click="destroy({{ $row->id_carga }})"><i
                                                                    class="bi-trash3-fill"></i> Delete </a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="100%">No data Found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="float-end">{{ $cargas->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {

            Livewire.on('closeObservationModal', () => {
                const modalEl = document.getElementById('ObservationModal');
                const modal = bootstrap.Modal.getInstance(modalEl) ??
                    new bootstrap.Modal(modalEl);

                modal.hide();
            });

        });
    </script>
