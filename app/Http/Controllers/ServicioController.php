<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servicio;

class ServicioController extends Controller
{
    public function index()
    {
        return view('admin.servicios');
    }

    public function store(Request $request)
    {
        try{
            $servicio = new Servicio();
            $servicio->descripcion = $request->descripcion;
            if($servicio->save()>=1){
                return response()->json(['status'=>'ok', 'data'=>$servicio],201);
            }
            $servicio->save();
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
            $servicio = Servicio::findOrFail($request->id);
            $servicio->descripcion = $request->descripcion;
            if($servicio->save()>=1){
                return response()->json(['status'=>'ok', 'data'=>$servicio],202);
            }
            $servicio->save();
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    //Eliminar
    public function destroy(Request $request)
    {
        try{
            $servicio = Servicio::findOrFail($request->id);
            $servicio->delete();
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    public function show()
    {
        try{
            $servicios = Servicio::orderBy('id','DESC')->get();
            return $servicios;
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }
}
