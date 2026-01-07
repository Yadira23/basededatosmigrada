@section('title', __('Formularios'))
<div class="container-fluid">
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
						<div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
						@endif
						<div>
							<input wire:model.live='keyWord' type="text" class="form-control" name="search" id="search" placeholder="Search Formularios">
						</div>
						<div class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#DataModal">
						<i class="bi-plus-lg"></i>  Add Formularios
						</div>
					</div>
				</div>
				
				<div class="card-body">
						@include('livewire.formularios.modals')
				<div class="table-responsive">
					<table class="table table-bordered table-sm">
						<thead class="thead">
							<tr> 
								<td>#</td> 
								<th>Id Form</th>
								<th>Titulo Form</th>
								<th>Fecha Creacion Form</th>
								<th>Descripcion Form</th>
								<th>Boton Accion Form</th>
								<th>Secciones Form</th>
								<th>Periodo Form</th>
								<th>Id Depen</th>
								<th>Id Usr</th>
								<td>ACTIONS</td>
							</tr>
						</thead>
						<tbody>
							@forelse($formularios as $row)
							<tr>
								<td>{{ $loop->iteration }}</td> 
								<td>{{ $row->id_form }}</td>
								<td>{{ $row->titulo_form }}</td>
								<td>{{ $row->fecha_creacion_form }}</td>
								<td>{{ $row->descripcion_form }}</td>
								<td>{{ $row->boton_accion_form }}</td>
								<td>{{ $row->secciones_form }}</td>
								<td>{{ $row->periodo_form }}</td>
								<td>{{ $row->id_depen }}</td>
								<td>{{ $row->id_usr }}</td>
								<td width="90">
									<div class="dropdown">
										<a class="btn btn-sm btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
											Actions
										</a>
										<ul class="dropdown-menu">
											<li><a data-bs-toggle="modal" data-bs-target="#DataModal" class="dropdown-item" wire:click="edit({{$row->id_form}})"><i class="bi-pencil-square"></i> Edit </a></li>
											<li><a class="dropdown-item" onclick="confirm('Confirm Delete Formulario id {{$row->id_form}}? \nDeleted Formularios cannot be recovered!')||event.stopImmediatePropagation()" wire:click="destroy({{$row->id_form}})"><i class="bi-trash3-fill"></i> Delete </a></li>  
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
</div>