<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*use App\Http\Controllers\HorarioController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\DentistaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\CitaController;*/

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
//Rutas de horarios
Route::get('/horarios',[HorarioController::class,'index'])-> name('horarios');
Route::get('/horarios/all',[HorarioController::class,'show']);
Route::post('/horarios/save',[HorarioController::class,'store']);
Route::put('/horarios/update',[HorarioController::class,'update']);
Route::delete('/horarios/delete',[HorarioController::class,'destroy']);

//Rutas de servicios
Route::get('/servicios',[ServicioController::class,'index'])-> name('servicios');
Route::get('/servicios/all',[ServicioController::class,'show']);
Route::post('/servicios/save',[ServicioController::class,'store']);
Route::put('/servicios/update',[ServicioController::class,'update']);
Route::delete('/servicios/delete',[ServicioController::class,'destroy']);

//Rutas de dentistas
Route::get('/dentistas',[DentistaController::class,'index'])-> name('dentistas');
Route::get('/dentistas/all',[DentistaController::class,'show']);
Route::post('/dentistas/save',[DentistaController::class,'store']);
Route::put('/dentistas/update',[DentistaController::class,'update']);
Route::delete('/dentistas/delete',[DentistaController::class,'destroy']);

//Rutas de pacientes
Route::get('/pacientes',[PacienteController::class,'index'])-> name('pacientes');
Route::get('/pacientes/all',[PacienteController::class,'show']);
Route::post('/pacientes/save',[PacienteController::class,'store']);
Route::put('/pacientes/update',[PacienteController::class,'update']);
Route::delete('/pacientes/delete',[PacienteController::class,'destroy']);

//Rutas de citas
Route::get('/citas',[CitaController::class,'index'])-> name('citas');
Route::get('/citas/state',[CitaController::class,'show']);
Route::post('/citas/save',[CitaController::class,'store']);
//Route::put('/citas/update',[CitaController::class,'update']);
Route::post('/citas/change',[CitaController::class,'changeState']);*/
