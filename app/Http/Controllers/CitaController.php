<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\DetalleServicio;
use Illuminate\Support\Facades\DB;

class CitaController extends Controller
{
    public function index()
    {
        return view('admin.citas');
    }

    public function store(Request $request)
    {
        try{
            $errors = 0;
            DB::beginTransaction();
            $cita = new Cita();
            $cita->user_id = $request->user['id'];
            $cita->fecha = $request->fecha;
            $cita->estado = 'R';
            $cita->horario_id = $request->horario['id'];
            $cita->dentista_id = $request->dentista['id'];
            if($cita->save()<=0){
                $errors++;
            }
            //Obtener el detalle
           $detalle = $request->servicios;
           //Recorriendo los elementos para guardar en la tabla detalle_servicios
           foreach($detalle as $key => $deta){
               //Creando el objeto para detalle
               $detalleServicio = new DetalleServicio();
               $detalleServicio->servicio_id = $deta['servicio']['id'];
               $detalleServicio->cita_id = $cita->id;
               //Guardando en la tabla detalle_servicios
               if($detalleServicio->save()<=0){
                $errors++;
            }
            }
            if($errors==0){
                DB::commit();
                return response()->json(['status'=>'ok','data'=>$cita],201);
            }else{
               DB::rollBack(); 
               return response()->json(['status'=>'fail','data'=>$cita],409);
            }
        }catch(\Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function edit($id)
    {
        //
    }

    //Cambiar estado
    public function changeState(Request $request){
        try{
            $cita = Cita::findOrFail($request->id);
            $cita->estado = $request->estado;
            $cita->save();
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    //Actualizar
    public function update(Request $request)
    {
        /*try{
            $cita = Cita::findOrFail($request->id);
            $cita->user_id = $request->user['id'];
            $cita->fecha = $request->fecha;
            $cita->estado = 'R';
            $cita->estado = $request->estado;
            $cita->horario_id = $request->horario['id'];
            $cita->dentista_id = $request->dentista['id'];
            $detalle = $request->detalleServicio;
            $cita->save();
        }catch(\Exception $e){
            return $e->getMessage();
        }*/
    }

    //Eliminar
    public function destroy(Request $request)
    {
        /*try{
            $cita = Cita::findOrFail($request->id);
            $cita->delete();
        }catch(\Exception $e){
            return $e->getMessage();
        }*/
    }

    public function show(Request $request)
    {
        try{
            $state = $request->state;
            $citas =  Cita::join('users','citas.user_id','=','users.id')
            ->join('horarios','citas.horario_id','=','horarios.id')
            ->join('dentistas','citas.dentista_id','=','dentistas.id')
            ->select('citas.id','users.name as user','citas.fecha','citas.estado',
            'horarios.hora as horario','dentistas.nombre as dentista')
            ->where('citas.estado', '=', $state)
            ->orderBy('citas.id','DESC')->get();
            return $citas;
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    public function viewByUser(){
        return view('reservas');
    }

    public function getByUser($id){
        $reservas = Cita::join('users','citas.user_id','=','users.id')
        ->join('horarios','citas.horario_id','=','horarios.id')
        ->join('dentistas','citas.dentista_id','=','dentistas.id')
        ->select('citas.id','users.name as user','citas.fecha','citas.estado',
        'horarios.hora as horario','dentistas.nombre as dentista')
        ->where('citas.estado', '=', 'R')
        ->where('citas.user_id', '=',$id)
        ->orderBy('citas.id','desc')->get();
        return $reservas;
    }
}
