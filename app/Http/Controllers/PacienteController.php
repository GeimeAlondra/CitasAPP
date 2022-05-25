<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paciente;

class PacienteController extends Controller
{
    public function index()
    {
        return view('admin.pacientes');
    }

    public function store(Request $request)
    {
        try{
            $paciente = new Paciente();
            $paciente->nombre = $request->nombre;
            $paciente->fecha_nacimiento = $request->fecha_nacimiento;
            $paciente->correo = $request->correo;
            $paciente->telefono = $request->telefono;
            $paciente->passwd = $request->passwd;
            if($paciente->save()>=1){
                return response()->json(['status'=>'ok', 'data'=>$paciente],201);
            }
            $paciente->save();
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
            $paciente = Paciente::findOrFail($request->id);
            $paciente->nombre = $request->nombre;
            $paciente->fecha_nacimiento = $request->fecha_nacimiento;
            $paciente->correo = $request->correo;
            $paciente->telefono = $request->telefono;
            $paciente->passwd = $request->passwd;
            if($paciente->save()>=1){
                return response()->json(['status'=>'ok', 'data'=>$paciente],202);
            }
            $paciente->save();
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    //Eliminar
    public function destroy(Request $request)
    {
        try{
            $paciente = Paciente::findOrFail($request->id);
            $paciente->delete();
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    public function show()
    {
        try{
            $pacientes = Paciente::orderBy('id','DESC')->get();
            return $pacientes;
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }
}
