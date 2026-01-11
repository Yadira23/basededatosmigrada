@section('title', __('Usuarios'))
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div style="display: flex; justify-content: space-between; align-items: center;">
						<div class="float-left">
							<h4><i class="bi-house-check-fill text-info"></i>
							Usuario Listing </h4>
						</div>
						@if (session()->has('message'))
						<div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
						@endif
						<div>
							<input wire:model.live='keyWord' type="text" class="form-control" name="search" id="search" placeholder="Search Usuarios">
						</div>
						<div class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#DataModal">
						<i class="bi-plus-lg"></i>  Add Usuarios
						</div>
					</div>
				</div>
				
				<div class="card-body">
						@include('livewire.usuarios.modals', [
    'adminExiste' => $this->adminExiste
])
				<div class="table-responsive">
					<table class="table table-bordered table-sm">
						<thead class="thead">
							<tr> 
								<th>Id Usuario</th>
								<th>Usuario Usr</th>
								<th>Nombre Usr</th>
								<th>Apellido Paterno</th>
								<th>Apellido Materno</th>
								<th>Email Usr</th>
								<th>Id Depen</th>
								<th>Id Rol</th>
								<th>Estado Usr</th>
								<th>Telefono Usr</th>
								<td>ACTIONS</td>
							</tr>
						</thead>
						<tbody>
							@forelse($usuarios as $row)
							<tr>
								<td>{{ $row->id_usuario }}</td>
								<td>{{ $row->usuario_usr }}</td>
								<td>{{ $row->nombre_usr }}</td>
								<td>{{ $row->apellido_paterno }}</td>
								<td>{{ $row->apellido_materno }}</td>
								<td>{{ $row->email_usr }}</td>
								<td>
    @if($row->id_depen)
        {{ $row->dependencia->nombre_depen ?? '—' }}
    @else
        <span class="badge bg-secondary">Administrador</span>
    @endif
</td>
<td>{{ $row->role->nombre_rol ?? '—' }}</td>
								<td>{{ $row->estado_usr }}</td>
								<td>{{ $row->telefono_usr }}</td>
								<td width="90">
									<div class="dropdown">
										<a class="btn btn-sm btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
											Actions
										</a>
										<ul class="dropdown-menu">
											<li>
    <a data-bs-toggle="modal" data-bs-target="#DataModal" class="dropdown-item" wire:click="edit({{$row->id_usuario}})">
        <i class="bi-pencil-square"></i> Edit
    </a>
</li>
<li>
    <a class="dropdown-item" onclick="confirm('Confirm Delete Usuario id {{$row->id_usuario}}? \nDeleted Usuarios cannot be recovered!')||event.stopImmediatePropagation()" 
       wire:click="destroy({{$row->id_usuario}})">
        <i class="bi-trash3-fill"></i> Delete
    </a>
</li>
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
					<div class="float-end">{{ $usuarios->links() }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>