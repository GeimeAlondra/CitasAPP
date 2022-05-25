<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\DentistaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Retorna una vista
Route::get('/',HomeController::class)->name("home");

Auth::routes();

Route::get('/admin', [HomeController::class, 'index'])->name("admin-index")->middleware('auth.admin');

//Rutas de horarios
Route::get('/horarios',[HorarioController::class,'index'])-> name('horarios');
Route::get('/horarios/all',[HorarioController::class,'show']);
Route::post('/horarios/save',[HorarioController::class,'store']);
Route::put('/horarios/update',[HorarioController::class,'update']);
Route::post('/horarios/delete',[HorarioController::class,'destroy']);

//Rutas de servicios
Route::get('/servicios',[ServicioController::class,'index'])-> name('servicios');
Route::get('/servicios/all',[ServicioController::class,'show']);
Route::post('/servicios/save',[ServicioController::class,'store']);
Route::put('/servicios/update',[ServicioController::class,'update']);
Route::post('/servicios/delete',[ServicioController::class,'destroy']);

//Rutas de dentistas
Route::get('/dentistas',[DentistaController::class,'index'])-> name('dentistas');
Route::get('/dentistas/all',[DentistaController::class,'show']);
Route::post('/dentistas/save',[DentistaController::class,'store']);
Route::put('/dentistas/update',[DentistaController::class,'update']);
Route::post('/dentistas/delete',[DentistaController::class,'destroy']);

//Rutas de pacientes
Route::get('/pacientes',[PacienteController::class,'index'])-> name('pacientes');
Route::get('/pacientes/all',[PacienteController::class,'show']);
Route::post('/pacientes/save',[PacienteController::class,'store']);
Route::put('/pacientes/update',[PacienteController::class,'update']);
Route::post('/pacientes/delete',[PacienteController::class,'destroy']);

//Rutas de citas
Route::get('/citas',[CitaController::class,'index'])-> name('citas');
Route::get('/citas/state',[CitaController::class,'show']);
Route::post('/citas/save',[CitaController::class,'store']);
//Route::put('/citas/update',[CitaController::class,'update']);
Route::put('/citas/change',[CitaController::class,'changeState']);
//Route::post('/citas/delete',[CitaController::class,'destroy']);
Route::get('/reservas/user',[CitaController::class,'viewByUser'])->name('reservas.user');

