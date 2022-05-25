<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;

//Ruta para cargar la index de admin
Route::get('',[HomeController::class,'index']);