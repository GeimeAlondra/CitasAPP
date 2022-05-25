<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    //Relacion de 1:N con DetalleServicio
    public function detalle_servicios(){
        return $this->hasMany(DetalleServicio::class,'id');
    }
}
