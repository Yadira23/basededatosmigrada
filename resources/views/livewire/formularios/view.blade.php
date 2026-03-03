@section('title', __('Formularios'))
<div class="container-fluid">
    @role('admin')
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div class="float-left">
                                <h4><i class="bi-house-check-fill text-info"></i>
                                    Formulario Listing </h4>
                            </div>
                            @if (session()->has('message'))
                                <div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;">
                                    {{ session('message') }} </div>
                            @endif
                            <div>
                                <input wire:model.live='keyWord' type="text" class="form-control" name="search"
                                    id="search" placeholder="Buscar Formularios">
                            </div>
                            <button type="button" class="btn btn-seie-accent btn-sm shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#DataModal">
                                <i class="bi-plus-lg"></i> Add Formularios
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        @include('livewire.formularios.modals', [
                            'indicadores' => $this->indicadores,
                            'dependencias' => $dependencias,
                        ])
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="thead">
                                    <tr>
                                        {{-- <th>#</th> --}}
                                        <th>Id Form</th>
                                        <th>Titulo Form</th>
                                        {{-- <th>Fecha Creacion Form</th> --}}
                                        <th>Descripcion Form</th>
                                        <th>Publicación</th>
                                        {{-- <th>Secciones Form</th> --}}
                                        <th>Periodo Form</th>
                                        <th>id_Ind</th>
                                        <th>Id Depen</th>
                                        <td>ACTIONS</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($formularios as $row)
                                        <tr>
                                            {{-- <td>{{ $loop->iteration }}</td> --}}
                                            <td>{{ $row->id_form }}</td>
                                            <td>{{ $row->titulo_form }}</td>
                                            {{-- <td>{{ $row->fecha_creacion_form }}</td> --}}
                                            <td>{{ $row->descripcion_form }}</td>
                                            <td width="150">
                                                @if ($row->boton_accion_form === 'Publicar')
                                                    <button wire:click="publicar({{ $row->id_form }})"
                                                        class="btn btn-success btn-sm" wire:loading.attr="disabled"
                                                        wire:target="publicar({{ $row->id_form }})">
                                                        Publicar
                                                    </button>
                                                @else
                                                @endif
                                            </td>
                                            {{-- <td>{{ $row->secciones_form }}</td> --}}
                                            <td>{{ $row->periodo_form }}</td>
                                            <td>{{ $row->indicador->nombre_ind ?? 'Sin indicador' }}</td>
                                            <td>{{ $row->dependencia->nombre_depen ?? 'Sin dependencia' }}</td>
                                            <td width="90">
                                                <div class="dropdown">
                                                    <a class="btn btn-sm btn-secondary dropdown-toggle" href="#"
                                                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        Actions
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                        <li><a data-bs-toggle="modal" data-bs-target="#DataModal"
                                                                class="dropdown-item"
                                                                wire:click="edit({{ $row->id_form }})"><i
                                                                    class="bi-pencil-square"></i> Edit </a></li>
                                                        <li><a class="dropdown-item"
                                                                onclick="confirm('Confirm Delete Formulario id {{ $row->id_form }}? \nDeleted Formularios cannot be recovered!')||event.stopImmediatePropagation()"
                                                                wire:click="destroy({{ $row->id_form }})"><i
                                                                    class="bi-trash3-fill"></i> Delete </a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="100%">No data Found </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="float-end">{{ $formularios->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endrole

    @role('usuario')
        {{-- <div class="row"> --}}
        {{-- <div class="col-md-12"> --}}
        {{-- <div class="card shadow-sm border-left-primary"> --}}
        {{-- <div class="card-body text-center" style="padding:60px;"> --}}
        {{-- <i class="fas fa-file-alt fa-3x text-primary mb-3"></i> --}}
        {{-- <h4 class="text-gray-700">Módulo de Formularios</h4> --}}
        {{-- <p class="text-muted">Aquí aparecerán los formularios disponibles para responder.</p> --}}
        {{-- <span class="badge bg-warning text-dark">Próximamente</span> --}}
        {{-- </div> --}}
        {{-- </div> --}}
        {{-- </div> --}}
        {{-- </div> --}}
        <div class="row">
            <div class="col-md-12">
                @livewire('usuario-formularios')
            </div>
        </div>
    </div>
@endrole
<script>
    document.addEventListener('livewire:init', () => {

        Livewire.on('show-modal', () => {
            const el = document.getElementById('DataModal');
            if (!el) return;
            const modal = new bootstrap.Modal(el);
            modal.show();
        });

        Livewire.on('close-modal', () => {
            const el = document.getElementById('DataModal');
            if (!el) return;
            const modal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
            modal.hide();
        });

        // ✅ SweetAlert (compatible con payload directo o [payload])
        Livewire.on('swal', (payload) => {
            const data = Array.isArray(payload) ? payload[0] : payload;

            Swal.fire({
                icon: data?.icon ?? 'success',
                title: data?.title ?? 'Listo',
                text: data?.text ?? '',
                confirmButtonColor: '#3085d6'
            });
        });

    });
</script>
