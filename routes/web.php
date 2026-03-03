<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnexoDownloadController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Usuario\IndicadorController;
use App\Http\Controllers\UsuarioAnexosController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\UsuarioEnvioController;
use App\Livewire\Admin\ConfiguracionCaptura;
use App\Livewire\Carga\CargaCreate;
use App\Livewire\DetalleCargas;
use App\Livewire\Formularios;
use App\Livewire\IndicadorCampos;
use App\Models\Indicador;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/* ---------------- AUTH ---------------- */
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.update');
/* ---------------- REDIRECCIÓN POR ROL ---------------- */
Route::middleware('auth')->get('/redirect-por-rol', function () {

    $user = auth()->user();

    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('usuario')) {
        return redirect()->route('usuario.dashboard');
    }

    abort(403, 'Rol no autorizado');
});

/* ---------------- DASHBOARDS ---------------- */
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');
});

Route::middleware(['auth', 'role:usuario'])->group(function () {
    Route::get('/usuario/dashboard', [UsuarioController::class, 'dashboard'])
        ->name('usuario.dashboard');
});

/* ---------------- RUTAS SOLO ADMIN ---------------- */
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::view('/usuarios', 'livewire.usuarios.index');
    Route::view('/dependencias', 'livewire.dependencias.index');
    Route::view('/cargas', 'livewire.carga.index');
    Route::view('/DetalleCargas', 'livewire.DetalleCargas.index');

    Route::get('/carga/crear', CargaCreate::class)->name('carga.create');

    Route::get('/admin/cargas/{id_carga}/revision', \App\Livewire\Admin\Cargas\CargaRevision::class)
        ->name('admin.cargas.revision');

    Route::get('/admin/configuracion-captura', ConfiguracionCaptura::class)
        ->name('admin.configuracion.captura');

    Route::get('/admin/perfil', function () {
        return view('admin.perfil');
    })
        ->name('admin.perfil');

    Route::get('/admin/password', [PasswordController::class, 'edit'])
        ->name('admin.password');

    Route::post('/admin/password', [PasswordController::class, 'update'])
        ->name('admin.password.update');
});

/* ---------------- RUTAS COMPARTIDAS (ADMIN + USUARIO) ---------------- */
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::view('/formularios', 'livewire.formularios.index')->name('formularios.index');
    Route::view('/anexos', 'livewire.anexos.index');
    Route::view('/indicadores', 'livewire.indicadores.index');
});

Route::middleware(['auth', 'role:usuario'])->get('/formularios', function () {
    return redirect()->route('usuario.indicadores');
});

Route::get('/admin/indicadores/{id_ind}/metas', \App\Livewire\Admin\Metas\MetasIndicador::class)
    ->name('admin.indicadores.metas');

Route::middleware(['auth', 'role:usuario'])->group(function () {
    // Route::get('/usuario/anexos', function () {
    //    return view('usuario.anexos.index');
    // })->name('usuario.anexos');

    Route::get('/usuario/anexos', [UsuarioAnexosController::class, 'index'])
        ->name('usuario.anexos');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/usuario/indicadores', function () {
        return view('usuario.formularios.index');
    })->name('usuario.indicadores');

    Route::get('/usuario/formularios', function () {
        return redirect()->route('usuario.indicadores');
    });

    Route::get('usuario/formulario/{id_form}/{id_ind}/{meta_id?}', App\Livewire\Usuario\FormularioCaptura::class)
        ->name('usuario.formulario.captura');

    Route::get('/detallecargas/{id_carga}', DetalleCargas::class)
        ->name('detallecargas.index');

    Route::get('/anexos/plantilla/{id_form}/{id_ind}', [AnexoDownloadController::class, 'plantilla'])
        ->name('anexos.plantilla');

    Route::get('/anexos/descargar/{id_anexo}', [AnexoDownloadController::class, 'descargar'])
        ->name('anexos.descargar');

    Route::get('/indicadores/{id_ind}/campos', IndicadorCampos::class)
        ->name('indicadores.campos');

    Route::get('/formularios', Formularios::class)->name('formularios.index');

    // ✅ VER ÚLTIMO ENVÍO (solo lectura)
    Route::get('/usuario/indicadores/{id_form}/envio', [UsuarioEnvioController::class, 'show'])
        ->name('usuario.envio.ver');

    // ✅ VER HISTORIAL DE ENVÍOS (lista)
    Route::get('/usuario/indicadores/{id_form}/historial', [UsuarioEnvioController::class, 'history'])
        ->name('usuario.envio.historial');

    // ✅ VER ENVÍO ESPECÍFICO (por id_carga)
    Route::get('/usuario/envios/{id_carga}', [UsuarioEnvioController::class, 'showByCarga'])
        ->name('usuario.envio.ver.carga');

    // Descargar ARCHIVO enviado
    Route::get('/usuario/envios/{id_carga}/archivo', [UsuarioEnvioController::class, 'downloadArchivo'])
        ->name('usuario.envio.descargar.archivo');

    // Descargar LOG del procesamiento
    Route::get('/usuario/envios/{id_carga}/log', [UsuarioEnvioController::class, 'downloadLog'])
        ->name('usuario.envio.descargar.log');

    Route::get('/usuario/indicadores/{id_ind}/metas', function ($id_ind) {

        $indicador = Indicador::with('metas')->findOrFail($id_ind);

        // ✅ Última carga por META (usando detallecargas.meta_id + cargas.status_env)
        $ultimaCargaPorMeta = DB::table('detallecargas as d')
            ->join('cargas as c', 'c.id_carga', '=', 'd.id_carga')
            ->select('d.meta_id', 'c.status_env', 'c.created_at', 'c.id_carga', 'c.id_form')
            ->where('d.id_ind', $id_ind)
            ->whereNotNull('d.meta_id')
            ->orderByDesc('c.created_at')
            ->get()
            ->groupBy('meta_id');

        $metas = $indicador->metas->map(function ($meta) use ($ultimaCargaPorMeta, $id_ind) {

            $ultima = optional($ultimaCargaPorMeta->get($meta->id))->first();

            $estado = $ultima
                ? strtoupper($ultima->status_env ?? 'ENVIADO')
                : 'SIN_CAPTURA';

            // ✅ Capturar: manda meta_id
            $urlCapturar = $meta->id_form
                ? route('usuario.formulario.captura', [
                    'id_form' => $meta->id_form,
                    'id_ind' => $id_ind,
                ]) . '?meta_id=' . $meta->id
                : '#';

            // ✅ Ver: si hay carga real, ver esa carga; si no, cae a "ver último envío" del formulario
            $urlVer = $ultima
                ? route('usuario.envio.ver.carga', ['id_carga' => $ultima->id_carga])
                : ($meta->id_form ? route('usuario.envio.ver', ['id_form' => $meta->id_form]) : '#');

            return [
                'meta' => $meta,
                'estado' => $estado,
                'url_capturar' => $urlCapturar,
                'url_ver' => $urlVer,
            ];
        });

        return view('usuario.indicadores.metas', [
            'indicador' => $indicador,
            'metas' => $metas,
        ]);
    })->name('usuario.indicadores.metas');

    Route::get('/usuario/indicadores/{formulario}', [IndicadorController::class, 'show'])
        ->name('usuario.indicadores.show');
});
