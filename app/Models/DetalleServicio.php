<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleServicio extends Model
{
    use HasFactory;
    protected $table = "detalle_servicios";

    //Relacion inversa con servicio
    public function servicios(){
        return $this->belongsTo(Servicio::class,'servicio_id');
    }

     //Relacion inversa con cita
     public function citas(){
        return $this->belongsTo(Cita::class,'cita_id');
    }
}
