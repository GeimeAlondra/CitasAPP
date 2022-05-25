<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Horario;

class HorarioController extends Controller
{
    public function index()
    {
        return view('admin.horarios');
    }

    public function store(Request $request)
    {
        try{
            $horario = new Horario();
            $horario->hora = $request->hora;
            if($horario->save()>=1){
                return response()->json(['status'=>'ok', 'data'=>$horario],201);
            }
            $horario->save();
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    public function edit($id)
    {
        //
    }

    //Actualizar
    public function update(Request $request)
    {
        try{
            $horario = Horario::findOrFail($request->id);
            $horario->hora = $request->hora;
            if($horario->save()>=1){
                return response()->json(['status'=>'ok', 'data'=>$horario],202);
            }
            $horario->save();
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    //Eliminar
    public function destroy(Request $request)
    {
        try{
            $horario = Horario::findOrFail($request->id);
            $horario->delete();
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    public function show()
    {
        try{
            $horarios = Horario::orderBy('id','DESC')->get();
            return $horarios;
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }
}
