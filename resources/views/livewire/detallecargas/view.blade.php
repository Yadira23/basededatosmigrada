@section('title', __('Detalle Cargas'))
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div style="display: flex; justify-content: space-between; align-items: center;">
						<div class="float-left">
							<h4><i class="bi-house-check-fill text-info"></i>
							Detalle Carga Listing </h4>
						</div>
						@if (session()->has('message'))
						<div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
						@endif
						<div>
							<input wire:model.live='keyWord' type="text" class="form-control" name="search" id="search" placeholder="Search Detalle Cargas">
						</div>
						<div class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#DataModal">
						<i class="bi-plus-lg"></i>  Add Detalle Cargas
						</div>
					</div>
				</div>
				
				<div class="card-body">
						@include('livewire.detalleCargas.modals')
				<div class="table-responsive">
					<table class="table table-bordered table-sm">
						<thead class="thead">
							<tr> 
								{{--<td>#</td>--}}
								<th>Id Detalle</th>
								<th>Id Carga</th>
								<th>Id Ind</th>
								<th>Id Region</th>
								<th>Id Mun</th>
								<th>Periodo Det</th>
								<th>Ejercicio Det</th>
								<th>Fecha Registro Det</th>
								<th>Fuente Det</th>
								<th>Valor Det</th>
								<td>ACTIONS</td>
							</tr>
						</thead>
						<tbody>
							@forelse($detalleCargas as $row)
							<tr>
								{{--<td>{{ $loop->iteration }}</td> --}}
								<td>{{ $row->id_detalle }}</td>
								<td>{{ $row->id_carga }}</td>
								<td>{{ $row->id_ind }}</td>
								<td>{{ $row->id_region }}</td>
								<td>{{ $row->id_mun }}</td>
								<td>{{ $row->periodo_det }}</td>
								<td>{{ $row->ejercicio_det }}</td>
								<td>{{ $row->fecha_registro_det }}</td>
								<td>{{ $row->fuente_det }}</td>
								<td>{{ $row->valor_det }}</td>
								<td width="90">
									<div class="dropdown">
										<a class="btn btn-sm btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
											Actions
										</a>
										<ul class="dropdown-menu">
											<li><a data-bs-toggle="modal" data-bs-target="#DataModal" class="dropdown-item" wire:click="edit({{$row->id_detalle}})"><i class="bi-pencil-square"></i> Edit </a></li>
											<li><a class="dropdown-item" onclick="confirm('Confirm Delete Detalle Carga id {{$row->id_detalle}}? \nDeleted Detalle Cargas cannot be recovered!')||event.stopImmediatePropagation()" wire:click="destroy({{$row->id_detalle}})"><i class="bi-trash3-fill"></i> Delete </a></li>  
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
					<div class="float-end">{{ $detalleCargas->links() }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>