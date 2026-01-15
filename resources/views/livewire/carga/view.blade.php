@section('title', __('Cargas')) <div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <h4><i class="bi-house-check-fill text-info"></i> Carga Listing </h4>
                        </div> @if (session()->has('message')) <div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div> @endif <div> <input wire:model.live='keyWord' type="text" class="form-control" name="search" id="search" placeholder="Search Cargas"> </div>
                        <div class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#DataModal"> <i class="bi-plus-lg"></i> Add Cargas </div>
                    </div>
                </div>
                <div class="card-body"> @include('livewire.carga.modals') @include('livewire.carga.observation-modal')<div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead">
                                <tr>
                                    {{--<td>#</td>--}}
                                    <th>Id Carga</th>
                                    <th>Foliounico Carga</th>
                                    <th>Fecha Carga</th>
                                    <th>Periodo</th>
                                    <th>Status Env</th>
                                    <th>Descripcion Env</th>
                                    <th>Observacion Env</th>
                                    <th>Id Form</th>
                                    <td>ACTIONS</td>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cargas as $row)
                                <tr>
                                    {{--<td>{{ $loop->iteration }}</td>--}}
                                    <td>{{ $row->id_carga }}</td>
                                    <td>{{ $row->folioUnico_carga }}</td>
                                    <td>{{ $row->fecha_carga }}</td>
                                    <td>{{ $row->periodo }}</td>
                                    <td>
                                        {{-- Botones de estado dinámicos --}}
                                        @if($row->status_env == 'Enviado')
                                        <button wire:click="changeStatus({{ $row->id_carga }}, 'Pendiente')" class="btn btn-sm btn-primary">Enviado</button>
                                        @elseif($row->status_env == 'Pendiente')
                                        <button wire:click="changeStatus({{ $row->id_carga }}, 'En Revisión')" class="btn btn-sm btn-warning">Pendiente</button>
                                        @elseif($row->status_env == 'En Revisión')
                                        <button class="btn btn-sm btn-info" disabled>En Revisión</button>
                                        @elseif($row->status_env == 'Aprobado')
                                        <button class="btn btn-sm btn-success" disabled>Aprobado</button>
                                        @elseif($row->status_env == 'Rechazado')
                                        <button class="btn btn-sm btn-danger" disabled>Rechazado</button>
                                        @endif
                                    </td>
                                    <td>{{ $row->descripcion_env }}</td>

                                    <td>
                                        {{-- Texto de la observación --}}
                                        @if($row->observacion_env)
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
                                        <button class="btn btn-sm mt-1
        {{ $row->observacion_env ? 'btn-outline-warning' : 'btn-secondary' }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#ObservationModal"
                                            wire:click="openObservation({{ $row->id_carga }})">

                                            {{ $row->observacion_env ? 'Editar observación' : 'Agregar observación' }}
                                        </button>
                                    </td>


                                    <td>{{ $row->formulario ? $row->formulario->titulo_form : 'N/A' }}</td>

                                    <td width="120">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li><a data-bs-toggle="modal" data-bs-target="#DataModal" class="dropdown-item" wire:click="edit({{$row->id_carga}})"><i class="bi-pencil-square"></i> Edit </a></li>
                                                <li><a class="dropdown-item" onclick="confirm('Confirm Delete Carga id {{$row->id_carga}}? \nDeleted Cargas cannot be recovered!')||event.stopImmediatePropagation()" wire:click="destroy({{$row->id_carga}})"><i class="bi-trash3-fill"></i> Delete </a></li>
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