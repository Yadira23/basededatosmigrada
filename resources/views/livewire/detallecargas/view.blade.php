@section('title', __('Detalle Cargas'))
<div class="container-fluid">
	<div class="row justify-content-center">
		<div class="col-md-12">
			@php
			$primera = $detalleCargas->first();
			@endphp

			@if($primera)
			<div class="alert alert-info mb-3">
				<strong>Detalles de la Carga:</strong>
				{{ $primera->carga->folioUnico_carga ?? 'N/A' }}
				&nbsp;|&nbsp;
				<strong>Fuente:</strong>
				{{ $primera->fuente_det ?? 'N/A' }}
				&nbsp;|&nbsp;
				<strong>Periodo:</strong>
				{{ $primera->periodo_det }}
				&nbsp;|&nbsp;
				<strong>Ejercicio:</strong>
				{{ $primera->ejercicio_det }}
				&nbsp;|&nbsp;
				<strong>Filas:</strong>
				{{ $detalleCargas->total() }}
			</div>
			@endif
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
							<input wire:model.live='keyWord' type="text" class="form-control" name="search" id="search" placeholder="Buscar Detalle Cargas">
						</div>
						{{--<div class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#DataModal">
							<i class="bi-plus-lg"></i> Add Detalle Cargas
						</div> --}}
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
									<th>Periodo Det</th>
									<th>Ejercicio Det</th>
									<th>Fecha Registro Det</th>
									<th>Fuente Det</th>
									<th>Ámbito</th>
									<th>Región / Municipio</th>
									<th>Descripción</th>
									<th>Valor Det</th>
									<td>ACTIONS</td>
								</tr>
							</thead>
							<tbody>
								@forelse($detalleCargas as $row)
								<tr>
									{{--<td>{{ $loop->iteration }}</td> --}}
									<td>{{ $row->id_detalle }}</td>
									<td>{{ $row->carga->folioUnico_carga ?? $row->id_carga }}</td>
									<td>{{ $row->indicador->nombre_ind ?? $row->id_ind }}</td>
									<td>{{ $row->periodo_det }}</td>
									<td>{{ $row->ejercicio_det }}</td>
									<td>{{ optional($row->fecha_registro_det)->format('Y-m-d') }}</td>
									<td>{{ $row->fuente_det }}</td>
									<td>
										@switch($row->ambito_geo)
										@case('REGION') REGIÓN @break
										@case('MUNICIPIO') MUNICIPIO @break
										@default ESTATAL
										@endswitch
									</td>
									<td>
										@if($row->ambito_geo === 'REGION')
										{{ $row->region->nombre_region ?? 'N/A' }}
										@elseif($row->ambito_geo === 'MUNICIPIO')
										{{ $row->municipio->nombre_municipio ?? 'N/A' }}
										@else
										ESTATAL
										@endif
									</td>
									<td style="min-width:260px;">
										@php
										$payload = $row->payload_det ?? [];
										$origen = data_get($payload, 'origen', '');
										$campos = data_get($payload, 'campos', []);
										$raw = data_get($payload, 'raw', '');
										$csvInfo = data_get($payload, 'csv', []);
										@endphp

										{{-- Origen --}}
										@if($origen)
										<span class="badge bg-info text-dark">{{ $origen }}</span>
										@else
										<span class="badge bg-secondary">sin_origen</span>
										@endif

										{{-- Si viene de CSV mapeado, muestra municipio/region del archivo --}}
										@if(!empty($csvInfo))
										<div class="text-muted" style="font-size:12px; margin-top:4px;">
											@if(!empty($csvInfo['municipio'])) Mun: {{ $csvInfo['municipio'] }} @endif
											@if(!empty($csvInfo['region'])) | Reg: {{ $csvInfo['region'] }} @endif
											@if(!empty($csvInfo['cve_mun'])) | Cve: {{ $csvInfo['cve_mun'] }} @endif
										</div>
										@endif

										{{-- Campos dinámicos --}}
										@if(is_array($campos) && count($campos))
										<div style="margin-top:6px;">
											@foreach($campos as $k => $v)
											<div style="line-height:1.2;">
												<b>{{ $k }}</b>: {{ is_array($v) ? json_encode($v) : $v }}
											</div>
											@endforeach
										</div>
										@endif

										{{-- Raw (solo cuando exista: excel libre / texto) --}}
										@if($raw !== '')
										<div class="text-muted" style="margin-top:6px; font-size:12px;">
											<b>raw:</b> {{ $raw }}
										</div>
										@endif

										{{-- Si no hay nada, muestra guion --}}
										@if((!is_array($campos) || !count($campos)) && $raw === '')
										<span class="text-muted">—</span>
										@endif
									</td>
									<td>
										@php
										$val = $row->valor_det;
										@endphp

										@if($val === null || $val === '' || (string)$val === '0.0000')
										<span class="text-muted">—</span>
										@else
										{{ $val }}
										@endif
									</td>
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