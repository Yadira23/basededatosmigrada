<div class="container-fluid">

    {{-- DATOS PARA GRÁFICAS --}}
    <div id="chart-data"
        data-formularios='@json($formulariosPorMes)'
        data-roles='@json($usuariosPorRol)'
        data-cargas='@json($cargasPorEstado)'>
    </div>

    {{-- RESUMEN --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Usuarios</h5>
                    <h2>{{ $totalUsuarios }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Formularios</h5>
                    <h2>{{ $totalFormularios }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>Cargas en revisión</h5>
                    <h2>{{ $cargasPendientes }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Nuevos hoy</h5>
                    <h2>{{ $nuevosRegistros }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- GRÁFICAS --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Formularios por mes</div>
                <div class="card-body">
                    <canvas id="formMes"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Usuarios por Rol</div>
                <div class="card-body">
                    <canvas id="rolesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Estado de Cargas</div>
                <div class="card-body">
                    <canvas id="cargasChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ACTIVIDAD --}}
    <div class="card">
        <div class="card-header">Últimos Formularios</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(DB::table('formularios')->latest()->limit(5)->get() as $f)
                    <tr>
                        <td>{{ $f->id_form }}</td>
                        <td>{{ $f->titulo_form }}</td>
                        <td>{{ \Carbon\Carbon::parse($f->created_at)->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const dataContainer = document.getElementById("chart-data");

    const formularios = JSON.parse(dataContainer.dataset.formularios);
    const roles = JSON.parse(dataContainer.dataset.roles);
    const cargas = JSON.parse(dataContainer.dataset.cargas);

    new Chart(document.getElementById('formMes'), {
        type: 'bar',
        data: {
            labels: Object.keys(formularios),
            datasets: [{
                label: 'Formularios',
                data: Object.values(formularios)
            }]
        }
    });

    new Chart(document.getElementById('rolesChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(roles),
            datasets: [{
                data: Object.values(roles)
            }]
        }
    });

    new Chart(document.getElementById('cargasChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(cargas),
            datasets: [{
                data: Object.values(cargas)
            }]
        }
    });

});
</script>
@endpush
