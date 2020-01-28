<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{

    // pagina inicio
    public function index(){
        return view('frontend.index');
    }

    // pagina informacion de quienes somos
    public function verSomos(){
        return view('frontend.paginas.somos');
    }

    // pagina de preguntas frecuentes
    public function verFAQ(){
        return view('frontend.paginas.faq');
    }


    function reportillo($idservicio, $fecha1, $fecha2){

        $date1 = Carbon::parse($fecha1)->format('Y-m-d');
        $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d');

       

        $orden = DB::table('ordenes AS o')
        ->join('ordenes_descripcion AS od', 'od.ordenes_id', '=', 'o.id')
        ->join('producto AS p', 'p.id', '=', 'od.producto_id')
        ->select('o.id', 'o.servicios_id', 'p.nombre', 'p.id AS idproducto', 'od.cantidad', 'od.precio', 'o.fecha_orden')
        ->where('o.servicios_id', $idservicio)        
        ->whereBetween('o.fecha_orden', array($date1, $date2))
        ->orderBy('o.id', 'ASC')  
        ->get(); 

        //return $orden;

        $datos = array();
        
        foreach($orden as $fororden){

            $idp = $fororden->idproducto;
            $nombre = "";
            $cantidad = 0;
            $dinero = 0;

            foreach($orden as $for2){

               if($for2->idproducto == $idp){
                    $nombre = $for2->nombre;
                    $cantidad = $cantidad + $for2->cantidad;
                    $dinero = $dinero + $for2->precio;
               } 
            }

            $seguro = true;
            //antes de agregar verificar, que id producto no exista el mismo            
            for($i = 0; $i < count($datos); $i++) {
                
                if($idp == $datos[$i]['idproducto']){
                    $seguro = false; 
                }
            }            

            if($seguro == true){
                $total = number_format((float)$dinero, 2, '.', '');
                $datos[] = array('idproducto' => $idp, 'nombre' => $nombre, 'cantidad' => $cantidad, 'total' => $total);
            }
            
        }

        return [$datos];

        $data = Servicios::where('id', $idservicio)->first();
        $nombre = $data->nombre;

        $totalDinero = number_format((float)$dinero, 2, '.', '');

        $comision = $data->comision;

        $suma = $totalDinero * $comision;

        $pagarFinal = $totalDinero - $suma;

        $pagar = number_format((float)$pagarFinal, 2, '.', '');
  
        /*$view =  \View::make('backend.paginas.reportes.reportepagoservicio', compact(['orden', 'totalDinero', 'nombre', 'pagar', 'comision', 'f1', 'f2']))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('carta', 'portrait');
 
        return $pdf->stream();*/
    }

}
