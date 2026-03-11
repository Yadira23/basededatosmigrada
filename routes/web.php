<?php

use App\Http\Controllers\Admin\PasswordController;
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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

/* ---------------- INICIO ---------------- */

Route::get('/', function () {
    return auth()->check()
        ? redirect('/redirect-por-rol')
        : redirect()->route('login');
});

/* ---------------- RUTAS GUEST (SIN SESIÓN) ---------------- */
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');

    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

/* ---------------- RUTAS AUTH (CON SESIÓN) ---------------- */
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/redirect-por-rol', function () {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('usuario')) {
            return redirect()->route('usuario.dashboard');
        }

        abort(403, 'Rol no autorizado');
    });

    /* -------- RUTAS COMPARTIDAS ENTRE USUARIOS AUTENTICADOS -------- */
    Route::get('/detallecargas/{id_carga}', DetalleCargas::class)
        ->name('detallecargas.index');

    Route::get('/anexos/plantilla/{id_form}/{id_ind}', [AnexoDownloadController::class, 'plantilla'])
        ->name('anexos.plantilla');

    Route::get('/anexos/descargar/{id_anexo}', [AnexoDownloadController::class, 'descargar'])
        ->name('anexos.descargar');

    Route::get('/indicadores/{id_ind}/campos', IndicadorCampos::class)
        ->name('indicadores.campos');
});

/* ---------------- RUTAS SOLO ADMIN ---------------- */
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');

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
    })->name('admin.perfil');

    Route::get('/admin/password', [PasswordController::class, 'edit'])
        ->name('admin.password');

    Route::post('/admin/password', [PasswordController::class, 'update'])
        ->name('admin.password.update');

    Route::get('/admin/indicadores/{id_ind}/metas', \App\Livewire\Admin\Metas\MetasIndicador::class)
        ->name('admin.indicadores.metas');

    Route::get('/admin/busqueda', [\App\Http\Controllers\Admin\BuscarController::class, 'index'])
        ->name('admin.buscar');

    Route::get('/formularios', Formularios::class)
        ->name('formularios.index');

    Route::view('/anexos', 'livewire.anexos.index');
    Route::view('/indicadores', 'livewire.indicadores.index');
});

/* ---------------- RUTAS SOLO USUARIO ---------------- */
Route::middleware(['auth', 'role:usuario'])->group(function () {

    Route::get('/usuario/dashboard', [UsuarioController::class, 'dashboard'])
        ->name('usuario.dashboard');

    Route::get('/formularios', function () {
        return redirect()->route('usuario.indicadores');
    });

    Route::get('/usuario/indicadores', function () {
        return view('usuario.formularios.index');
    })->name('usuario.indicadores');

    Route::get('/usuario/formularios', function () {
        return redirect()->route('usuario.indicadores');
    });

    Route::get('/usuario/anexos', [UsuarioAnexosController::class, 'index'])
        ->name('usuario.anexos');

    Route::get('/usuario/formulario/{id_form}/{id_ind}/{meta_id?}', App\Livewire\Usuario\FormularioCaptura::class)
        ->name('usuario.formulario.captura');

    Route::get('/usuario/indicadores/{id_form}/envio', [UsuarioEnvioController::class, 'show'])
        ->name('usuario.envio.ver');

    Route::get('/usuario/indicadores/{id_form}/historial', [UsuarioEnvioController::class, 'history'])
        ->name('usuario.envio.historial');

    Route::get('/usuario/envios/{id_carga}', [UsuarioEnvioController::class, 'showByCarga'])
        ->name('usuario.envio.ver.carga');

    Route::get('/usuario/envios/{id_carga}/archivo', [UsuarioEnvioController::class, 'downloadArchivo'])
        ->name('usuario.envio.descargar.archivo');

    Route::get('/usuario/envios/{id_carga}/log', [UsuarioEnvioController::class, 'downloadLog'])
        ->name('usuario.envio.descargar.log');

    Route::get('/usuario/indicadores/{id_ind}/metas', function ($id_ind) {
        $indicador = Indicador::with('metas')->findOrFail($id_ind);

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

            $urlCapturar = $meta->id_form
                ? route('usuario.formulario.captura', [
                    'id_form' => $meta->id_form,
                    'id_ind' => $id_ind,
                ]) . '?meta_id=' . $meta->id
                : '#';

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
