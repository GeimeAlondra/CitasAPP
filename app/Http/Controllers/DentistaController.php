<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dentista;

class DentistaController extends Controller
{
    public function index()
    {
        return view('admin.dentistas');
    }

    public function store(Request $request)
    {
        try{
            $dentista = new Dentista();
            $dentista->nombre = $request->nombre;
            $dentista->consultorio = $request->consultorio;
            if($dentista->save()>=1){
                return response()->json(['status'=>'ok', 'data'=>$dentista],201);
            }
            $dentista->save();
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
            $dentista = Dentista::findOrFail($request->id);
            $dentista->nombre = $request->nombre;
            $dentista->consultorio = $request->consultorio;
            if($dentista->save()>=1){
                return response()->json(['status'=>'ok', 'data'=>$dentista],202);
            }
            $dentista->save();
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    //Eliminar
    public function destroy(Request $request)
    {
        try{
            $dentista = Dentista::findOrFail($request->id);
            $dentista->delete();
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    public function show()
    {
        try{
            $dentistas = Dentista::orderBy('id','DESC')->get();
            return $dentistas;
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }
}
