<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\DetalleCargas;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Route Hooks - Do not delete//
	Route::view('cargas', 'livewire.cargas.index')->middleware('auth');
	Route::view('formularios', 'livewire.formularios.index')->middleware('auth');
	Route::view('usuarios', 'livewire.usuarios.index')->middleware('auth');
	Route::view('DetalleCargas', 'livewire.DetalleCargas.index')->middleware('auth');
	Route::view('anexos', 'livewire.anexos.index')->middleware('auth');
	Route::view('indicadores', 'livewire.indicadores.index')->middleware('auth');
	Route::view('dependencias', 'livewire.dependencias.index')->middleware('auth');