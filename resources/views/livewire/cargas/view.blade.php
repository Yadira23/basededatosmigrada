@section('title', __('Cargas'))
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div style="display: flex; justify-content: space-between; align-items: center;">
						<div class="float-left">
							<h4><i class="bi-house-check-fill text-info"></i>
							Carga Listing </h4>
						</div>
						@if (session()->has('message'))
						<div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
						@endif
						<div>
							<input wire:model.live='keyWord' type="text" class="form-control" name="search" id="search" placeholder="Search Cargas">
						</div>
						<div class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#DataModal">
						<i class="bi-plus-lg"></i>  Add Cargas
						</div>
					</div>
				</div>
				
				<div class="card-body">
						@include('livewire.cargas.modals')
				<div class="table-responsive">
					<table class="table table-bordered table-sm">
						<thead class="thead">
							<tr> 
								<td>#</td> 
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
								<td>{{ $loop->iteration }}</td> 
								<td>{{ $row->id_carga }}</td>
								<td>{{ $row->folioUnico_carga }}</td>
								<td>{{ $row->fecha_carga }}</td>
								<td>{{ $row->periodo }}</td>
								<td>{{ $row->status_env }}</td>
								<td>{{ $row->descripcion_env }}</td>
								<td>{{ $row->observacion_env }}</td>
								<td>{{ $row->id_form }}</td>
								<td width="90">
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
								<td class="text-center" colspan="100%">No data Found </td>
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