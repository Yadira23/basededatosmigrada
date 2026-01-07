@section('title', __('Dependencias'))
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div style="display: flex; justify-content: space-between; align-items: center;">
						<div class="float-left">
							<h4><i class="bi-house-check-fill text-info"></i>
							Dependencia Listing </h4>
						</div>
						@if (session()->has('message'))
						<div wire:poll.4s class="btn btn-sm btn-success" style="margin-top:0px; margin-bottom:0px;"> {{ session('message') }} </div>
						@endif
						<div>
							<input wire:model.live='keyWord' type="text" class="form-control" name="search" id="search" placeholder="Search Dependencias">
						</div>
						<div class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#DataModal">
						<i class="bi-plus-lg"></i>  Add Dependencias
						</div>
					</div>
				</div>
				
				<div class="card-body">
						@include('livewire.dependencias.modals')
				<div class="table-responsive">
					<table class="table table-bordered table-sm">
						<thead class="thead">
							<tr> 
								<td>#</td> 
								<th>Id Depen</th>
								<th>Nombre Depen</th>
								<th>Id Sector</th>
								<th>Email Depen</th>
								<th>Extension Depen</th>
								<th>Telefono Depen</th>
								<th>Calle Depen</th>
								<th>Numero Calle Depen</th>
								<th>Colonia Depen</th>
								<th>Cp Depen</th>
								<td>ACTIONS</td>
							</tr>
						</thead>
						<tbody>
							@forelse($dependencias as $row)
							<tr>
								<td>{{ $loop->iteration }}</td> 
								<td>{{ $row->id_depen }}</td>
								<td>{{ $row->nombre_depen }}</td>
								<td>{{ $row->sectore->nombre_sector ?? 'N/A' }}</td>
								<td>{{ $row->email_depen }}</td>
								<td>{{ $row->extension_depen }}</td>
								<td>{{ $row->telefono_depen }}</td>
								<td>{{ $row->calle_depen }}</td>
								<td>{{ $row->numerocalle_depen }}</td>
								<td>{{ $row->colonia_depen }}</td>
								<td>{{ $row->cp_depen }}</td>
								<td width="90">
									<div class="dropdown">
										<a class="btn btn-sm btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
											Actions
										</a>
										<ul class="dropdown-menu">
											<li><a data-bs-toggle="modal" data-bs-target="#DataModal" class="dropdown-item" wire:click="edit({{ $row->id_depen }})"><i class="bi-pencil-square"></i> Edit </a></li>
											<li><a class="dropdown-item" onclick="confirm('Confirm Delete Dependencia id {{$row->id_depen}}? \nDeleted Dependencias cannot be recovered!')||event.stopImmediatePropagation()" wire:click="destroy({{ $row->id_depen }})"><i class="bi-trash3-fill"></i> Delete </a></li>  
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
					<div class="float-end">{{ $dependencias->links() }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>