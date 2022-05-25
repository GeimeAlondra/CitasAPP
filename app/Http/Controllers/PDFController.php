<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;
//use \Barryvdh\DomPDF\Facade;
use PDF;

class PDFController extends Controller
{
    //
    public function PDF(){
        $pdf = \PDF::loadView('test');
        return $pdf->stream('test.pdf');
    }

    public function PDFCita(){
        $citas = Cita::select(
            'citas.id',
            'users.name as user',
            'citas.fecha',
            'horarios.hora as horario',
            'dentistas.nombre as dentista')
        ->orderBy('citas.id','DESC')->get();
        $pdf = \PDF::loadView('admin.listCitasPDF',compact('citas'));
        return $pdf->stream('cita.pdf');
    }
}
