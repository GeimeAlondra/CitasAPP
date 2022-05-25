<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    //Relacion de 1:N con citas
    public function citas(){
        return $this->hasMany(Cita::class,'id');
    }
}
