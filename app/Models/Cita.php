<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    //Relacion inversa con usuario
    public function user(){
        return $this->belongsTo(Users::class,'user_id');
    }

    //Relacion inversa con horario
    public function horarios(){
        return $this->belongsTo(Horario::class,'horario_id');
    }

    //Relacion inversa con dentista
    public function dentistas(){
        return $this->belongsTo(Dentista::class,'dentista_id');
    }

    //Relacion de 1:N con DetalleServicio
    public function detalle_servicios(){
        return $this->hasMany(DetalleServicio::class,'id');
    }
}
