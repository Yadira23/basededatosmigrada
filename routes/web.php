<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Carga\CargaCreate;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UsuarioController;
use App\Livewire\AdminDashboard;
use App\Livewire\Usuario\FormularioCaptura;
use App\Livewire\DetalleCargas;
use App\Http\Controllers\AnexoDownloadController;
use App\Livewire\Formularios;
use App\Livewire\IndicadorCampos;
use App\Http\Controllers\UsuarioEnvioController;
use App\Http\Controllers\UsuarioAnexosController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

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

Route::middleware(['auth', 'role:usuario'])->group(function () {
    //Route::get('/usuario/anexos', function () {
    //    return view('usuario.anexos.index');
    //})->name('usuario.anexos');

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

    Route::get('/usuario/formulario/{id_form}/{id_ind}', FormularioCaptura::class)
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
});
