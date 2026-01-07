@section('title', __('Anexos'))
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div style="display: flex; justify-content: space-between; align-items: center;">
						<div class="float-left">
							<h4><i class="bi-house-check-fill text-info"></i>
							Anexo Listing </h4>
						</div>
						@if (session()->has('message'))
						<div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
						@endif
						<div>
							<input wire:model.live='keyWord' type="text" class="form-control" name="search" id="search" placeholder="Search Anexos">
						</div>
						<div class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#DataModal">
						<i class="bi-plus-lg"></i>  Add Anexos
						</div>
					</div>
				</div>
				
				<div class="card-body">
						@include('livewire.anexos.modals')
				<div class="table-responsive">
					<table class="table table-bordered table-sm">
						<thead class="thead">
							<tr> 
								<td>#</td> 
								<th>Id Anexo</th>
								<th>Nombre Anexo</th>
								<th>Tipo Anexo</th>
								<th>Peso Anexo</th>
								<th>Guia Anexo</th>
								<th>Fin Proposito Anexo</th>
								<th>Fecha Subida Anexo</th>
								<th>Ruta Archivo Anexo</th>
								<th>Id Form</th>
								<td>ACTIONS</td>
							</tr>
						</thead>
						<tbody>
							@forelse($anexos as $row)
							<tr>
								<td>{{ $loop->iteration }}</td> 
								<td>{{ $row->id_anexo }}</td>
								<td>{{ $row->nombre_anexo }}</td>
								<td>{{ $row->tipo_anexo }}</td>
								<td>{{ $row->peso_anexo }}</td>
								<td>{{ $row->guia_anexo }}</td>
								<td>{{ $row->fin_proposito_anexo }}</td>
								<td>{{ $row->fecha_subida_anexo }}</td>
								<td>{{ $row->ruta_archivo_anexo }}</td>
								<td>{{ $row->id_form }}</td>
								<td width="90">
									<div class="dropdown">
										<a class="btn btn-sm btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
											Actions
										</a>
										<ul class="dropdown-menu">
											<li><a data-bs-toggle="modal" data-bs-target="#DataModal" class="dropdown-item" wire:click="edit({{$row->id_anexo}})"><i class="bi-pencil-square"></i> Edit </a></li>
											<li><a class="dropdown-item" onclick="confirm('Confirm Delete Anexo id {{$row->id_anexo}}? \nDeleted Anexos cannot be recovered!')||event.stopImmediatePropagation()" wire:click="destroy({{$row->id_anexo}})"><i class="bi-trash3-fill"></i> Delete </a></li>  
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
					<div class="float-end">{{ $anexos->links() }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>