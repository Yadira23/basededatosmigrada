@section('title', __('Indicadores'))
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div style="display: flex; justify-content: space-between; align-items: center;">
						<div class="float-left">
							<h4><i class="bi-house-check-fill text-info"></i>
							Indicadore Listing </h4>
						</div>
						@if (session()->has('message'))
						<div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
						@endif
						<div>
							<input wire:model.live='keyWord' type="text" class="form-control" name="search" id="search" placeholder="Search Indicadores">
						</div>
						<div class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#DataModal">
						<i class="bi-plus-lg"></i>  Add Indicadores
						</div>
					</div>
				</div>
				
				<div class="card-body">
						@include('livewire.indicadores.modals')
				<div class="table-responsive">
					<table class="table table-bordered table-sm">
						<thead class="thead">
							<tr> 
								<td>#</td> 
								<th>Id Ind</th>
								<th>Nombre Ind</th>
								<th>Definicion Ind</th>
								<th>Formula Ind</th>
								<th>Tendencia Ind</th>
								<th>Restriccion Ind</th>
								<th>Formato Ind</th>
								<th>Unidadmedida Ind</th>
								<th>Meta Ind</th>
								<th>Requerido Ind</th>
								<th>Status Ind</th>
								<th>Periodo Ind</th>
								<th>Etiquetas Ind</th>
								<th>Fuenteverificacion Ind</th>
								<th>Id Form</th>
								<td>ACTIONS</td>
							</tr>
						</thead>
						<tbody>
							@forelse($indicadores as $row)
							<tr>
								<td>{{ $loop->iteration }}</td> 
								<td>{{ $row->id_ind }}</td>
								<td>{{ $row->nombre_ind }}</td>
								<td>{{ $row->definicion_ind }}</td>
								<td>{{ $row->formula_ind }}</td>
								<td>{{ $row->tendencia_ind }}</td>
								<td>{{ $row->restriccion_ind }}</td>
								<td>{{ $row->formato_ind }}</td>
								<td>{{ $row->unidadmedida_ind }}</td>
								<td>{{ $row->meta_ind }}</td>
								<td>{{ $row->requerido_ind }}</td>
								<td>{{ $row->status_ind }}</td>
								<td>{{ $row->periodo_ind }}</td>
								<td>{{ $row->etiquetas_ind }}</td>
								<td>{{ $row->fuenteverificacion_ind }}</td>
								<td>{{ $row->id_form }}</td>
								<td width="90">
									<div class="dropdown">
										<a class="btn btn-sm btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
											Actions
										</a>
										<ul class="dropdown-menu">
											<li><a data-bs-toggle="modal" data-bs-target="#DataModal" class="dropdown-item" wire:click="edit({{$row->id_ind}})"><i class="bi-pencil-square"></i> Edit </a></li>
											<li><a class="dropdown-item" onclick="confirm('Confirm Delete Indicadore id {{$row->id_ind}}? \nDeleted Indicadores cannot be recovered!')||event.stopImmediatePropagation()" wire:click="destroy({{$row->id_ind}})"><i class="bi-trash3-fill"></i> Delete </a></li>  
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
					<div class="float-end">{{ $indicadores->links() }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>