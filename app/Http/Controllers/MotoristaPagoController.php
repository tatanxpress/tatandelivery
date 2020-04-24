<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Motoristas;
use App\Servicios;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon; 
use App\MotoristaPago;
use App\OrdenRevisada;
use App\Revisador;
use App\ServicioPago;

class MotoristaPagoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
 
    // lista pago a motorista
    public function index(){

        $moto = Motoristas::all();
        return view('backend.paginas.motopago.listamotopago', compact('moto'));
    } 

    // tabla de pagos a motorista
    public function tablapago(){ 
         
        $moto = DB::table('motorista_pago AS mp')
        ->join('motoristas AS m', 'm.id', '=', 'mp.motorista_id')
        ->select('mp.id', 'm.identificador', 'mp.descripcion', 'm.nombre', 'mp.pago', 'mp.fecha', 'mp.fecha1', 'mp.fecha2')
        ->get();

        foreach($moto as $o){
            $fecha1 = $o->fecha1;
            $f1 = date("d-m-Y", strtotime($fecha1));
            $o->fecha1 = $f1;

            $fecha2 = $o->fecha2;
            $f2 = date("d-m-Y", strtotime($fecha2));
            $o->fecha2 = $f2;

            $fecha = $o->fecha;
            $f3 = date("d-m-Y", strtotime($fecha));
            $o->fecha = $f3;
        }

        return view('backend.paginas.motopago.tablas.tablamotopago', compact('moto'));
    } 
 
    // nuevo registro de pago a motorista
    public function nuevo(Request $request){
        if($request->isMethod('post')){

            $regla = array(
                'id' => 'required',
                'fecha1' => 'required',
                'fecha2' => 'required',
                'pago' => 'required',
            );

            $mensaje = array(
                'id.required' => 'id es requerido',
                'fecha1.required' => 'fecha desde es requerido',
                'fecha2.required' => 'fecha hasta requerida',
                'pago.required' => 'pago es requerido',                
                );

                
            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }   

            $fecha = Carbon::now('America/El_Salvador');

            $m = new MotoristaPago();
            $m->motorista_id = $request->id;
            $m->fecha1 = $request->fecha1;
            $m->fecha2 = $request->fecha2;
            $m->fecha = $fecha;
            $m->pago = $request->pago;
            $m->descripcion = $request->descripcion;
            
            if($m->save()){
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }                    
        }        
    } 

    // ver cuanto se le ha pagado a un servicio en total
    public function totalpagadomotorista(Request $request){
        if($request->isMethod('post')){

            $regla = array(
                'id' => 'required'               
            );

            $mensaje = array(
                'id.required' => 'id es requerido'                          
                );
 
                
            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }  

           if(Motoristas::where('id', $request->id)->first()){

            $motorista = DB::table('motorista_pago')
            ->where('motorista_id', $request->id)
            ->get();
    
            $dinero = 0;
            foreach($motorista as $o){
                $dinero = $dinero + $o->pago;
            }

            $total = number_format((float)$dinero, 2, '.', '');

            return ['success' => 1, 'total' => $total];
           }else{
               return ['success' => 2, 'total' => $total];
           }              
        }        
    }

 
    //** PAGO A SERVICIOS */
    public function index2(){ 

        $servicios = Servicios::all();
        return view('backend.paginas.pagoservicio.listapagoservicio', compact('servicios'));
    }  

    // buscador de ordenes completas de un servicio
    public function buscador($idservicio, $fecha1, $fecha2){
    
        if(Servicios::where('id', $idservicio)->first()){
 
            $date1 = Carbon::parse($fecha1)->format('Y-m-d');
            $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d'); 
             
            $orden = DB::table('ordenes AS o')
            ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
            ->select('o.id AS idorden', 'o.precio_total', 'o.fecha_orden', 
            's.identificador AS identiservicio', 'o.estado_7', 'o.estado_8', 'o.estado_5')
            ->where('s.id', $idservicio)
            ->where('o.estado_5', 1) // ordenes completadas
            ->where('o.estado_8', 0) // no canceladas
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get();  

            foreach($orden as $o){
                $fechaOrden = $o->fecha_orden;
                $hora1 = date("h:i A", strtotime($fechaOrden));
                $fecha1 = date("d-m-Y", strtotime($fechaOrden));
                $o->fecha_orden = $fecha1 . " " . $hora1;  
            }          
  
            return view('backend.paginas.pagoservicio.tablas.tablalistapagoservicio', compact('orden'));
        }else{
            return ['success' => 2];
        }
    }
         
    // reporte de ordenes completadas para pagar a servicios
    function reporte($idservicio, $fecha1, $fecha2){

        $date1 = Carbon::parse($fecha1)->format('Y-m-d');
        $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d');

        $f1 = Carbon::parse($fecha1)->format('d-m-Y');
        $f2 = Carbon::parse($fecha2)->format('d-m-Y');

        $orden = DB::table('ordenes AS o')
            ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
            ->select('o.id AS idorden', 'o.precio_total', 'o.fecha_orden', 
            's.identificador AS identiservicio', 'o.estado_7', 'o.estado_8', 'o.estado_5')
            ->where('s.id', $idservicio)
            ->where('o.estado_5', 1) // ordenes completadas
            ->where('o.estado_8', 0) // no canceladas
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get(); 

        $dinero = 0;
        foreach($orden as $o){
 
            //sumar
            $dinero = $dinero + $o->precio_total;

            $fechaOrden = $o->fecha_orden;
            $hora1 = date("h:i A", strtotime($fechaOrden));
            $fecha1 = date("d-m-Y", strtotime($fechaOrden));
            $o->fecha_orden = $fecha1 . " " . $hora1;  
        }

        $data = Servicios::where('id', $idservicio)->first();
        $nombre = $data->nombre;

        $totalDinero = number_format((float)$dinero, 2, '.', '');

        $comision = $data->comision; // 10 

        $suma = ($totalDinero * $comision) / 100;   // 5.47

        $redondear = round($comision);

        $pagarFinal = $totalDinero - $suma;

        $pagar = number_format((float)$pagarFinal, 2, '.', ''); 

        $suma2 = number_format((float)$suma, 2, '.', ''); 
  
        $view =  \View::make('backend.paginas.reportes.reportepagoservicio', compact(['orden', 'redondear', 'suma2', 'totalDinero', 'nombre', 'pagar', 'comision', 'f1', 'f2']))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('carta', 'portrait');
 
        return $pdf->stream();
    }

    // utilizo min de compra para envio gratis el servicio
    function reporteUtilizoMinEnvioGratis($idservicio, $fecha1, $fecha2){

        $date1 = Carbon::parse($fecha1)->format('Y-m-d');
        $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d');

        $f1 = Carbon::parse($fecha1)->format('d-m-Y');
        $f2 = Carbon::parse($fecha2)->format('d-m-Y');

        $orden = DB::table('ordenes AS o')
            ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
            ->select('o.id AS idorden', 'o.precio_total', 'o.fecha_orden', 
            's.identificador AS identiservicio', 'o.estado_7', 'o.estado_8', 'o.estado_5', 'o.supero_envio_gratis')
            ->where('s.id', $idservicio)
            ->where('o.estado_5', 1) // ordenes completadas
            ->where('o.estado_8', 0) // no canceladas
            ->where('o.supero_envio_gratis', 1)
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get(); 
      
            $dinero = 0;
        foreach($orden as $o){
            
            $fechaOrden = $o->fecha_orden;
            $hora1 = date("h:i A", strtotime($fechaOrden));
            $fecha1 = date("d-m-Y", strtotime($fechaOrden));
            $o->fecha_orden = $fecha1 . " " . $hora1;  

            // obtener nombre de la zona por cada orden
            $data = DB::table('ordenes_direcciones AS o')
            ->join('zonas AS z', 'z.id', '=', 'o.zonas_id')   
            ->select('o.copia_envio', 'z.nombre')                 
            ->where('o.ordenes_id', $o->idorden) // ordenes completadas 
            ->first(); 

            $o->nombrezona = $data->nombre;
            $o->copiaenvio = $data->copia_envio;

            $dinero = $dinero + $data->copia_envio;
        }

        $total = number_format((float)$dinero, 2, '.', '');

        $data = Servicios::where('id', $idservicio)->first();
        $nombre = $data->nombre;
  
        $view =  \View::make('backend.paginas.reportes.reporteusoenviogratis', compact(['orden', 'total', 'nombre', 'f1', 'f2']))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('carta', 'portrait');
 
        return $pdf->stream();
    }
 
     // reporte para ordenes canceladas
     function reporteordencancelada($idservicio, $fecha1, $fecha2){

        $date1 = Carbon::parse($fecha1)->format('Y-m-d');
        $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d');

        $f1 = Carbon::parse($fecha1)->format('d-m-Y');
        $f2 = Carbon::parse($fecha2)->format('d-m-Y');

        $orden = DB::table('ordenes AS o')
        ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
        ->select('o.id AS idorden', 'o.precio_total', 'o.fecha_orden', 
        's.identificador AS identiservicio', 'o.estado_7', 'o.estado_8', 'o.estado_5', 'o.cancelado_cliente', 'o.cancelado_propietario')
        ->where('s.id', $idservicio)
        ->where('o.estado_8', 1) //canceladas
        ->whereBetween('o.fecha_orden', array($date1, $date2))          
        ->get();  

        $conteo = 0;
        $dinero = 0;
        foreach($orden as $o){
            $conteo = $conteo + 1;
            //sumar
            $dinero = $dinero + $o->precio_total;

            $o->precioorden = number_format((float)$o->precio_total, 2, '.', '');

            $fechaOrden = $o->fecha_orden;
            $hora1 = date("h:i A", strtotime($fechaOrden));
            $fecha1 = date("d-m-Y", strtotime($fechaOrden));
            $o->fecha_orden = $fecha1 ;  
        }

        $data = Servicios::where('id', $idservicio)->first();
        $nombre = $data->nombre;

        $totalDinero = number_format((float)$dinero, 2, '.', '');

        $view =  \View::make('backend.paginas.reportes.reporteordencancelada', compact(['orden', 'totalDinero', 'conteo', 'nombre', 'f1', 'f2']))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('carta', 'portrait');
 
        return $pdf->stream();
    }

    //** REVISAR ORDENES REVISADAS POR RECOLECTORES DE DINERO */

    public function index3(){

        $revisador = Revisador::all();
        $motorista = Motoristas::all();
       
        return view('backend.paginas.revisador.listaordenrevisada', compact('revisador', 'motorista')); 
    }   

    // buscar ordenes de los revisadores
    public function buscarOrdenRevisada($id, $fecha1, $fecha2){

        $date1 = Carbon::parse($fecha1)->format('Y-m-d');
        $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d');
        
        $orden = DB::table('ordenes_revisadas AS or')
        ->join('ordenes AS o', 'o.id', '=', 'or.ordenes_id')
        ->join('revisador AS r', 'r.id', '=', 'or.revisador_id')
        ->select('or.ordenes_id', 'or.revisador_id', 'o.precio_total', 'o.precio_envio', 'r.nombre', 'r.identificador', 'o.fecha_orden',  'or.fecha')
        ->where('or.revisador_id', $id)
        ->whereBetween('or.fecha', array($date1, $date2))
        ->get();
 
        $sum = 0.0;
        foreach($orden as $o){
            $fechaOrden = $o->fecha_orden;
            $hora1 = date("h:i A", strtotime($fechaOrden));
            $fecha1 = date("d-m-Y", strtotime($fechaOrden));
            $o->fecha_orden = $fecha1 . " " . $hora1;  
 
            $fecha = $o->fecha;
            $hora = date("h:i A", strtotime($fecha));
            $fecha = date("d-m-Y", strtotime($fecha));
            $o->fecha = $fecha . " " . $hora; 

            // sumar precio
            $precio = $o->precio_total + $o->precio_envio;
            $o->precio = number_format((float)$precio, 2, '.', '');

            $sum = $sum + $precio;
        }

        $suma = number_format((float)$sum, 2, '.', '');

        return view('backend.paginas.revisador.tablas.tablaordenrevisada', compact('orden', 'suma'));
    }

    // filtro de ordenes de motorista que aun no han pagado
    public function buscarOrdenRevisada2($id){

        // sacar todos los id de ordenes revisadas
        $revisada = DB::table('ordenes_revisadas')
        ->get();

        $pilaOrdenid = array();
        foreach($revisada as $p){
            array_push($pilaOrdenid, $p->ordenes_id);
        }
             
        $ordenid = DB::table('motorista_ordenes AS mo')
        ->join('ordenes AS o', 'o.id', '=', 'mo.ordenes_id')
        ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')
        ->select('mo.motoristas_id', 'mo.ordenes_id', 'mo.fecha_agarrada',
         'o.estado_5', 'm.identificador', 'o.precio_total', 'o.precio_envio', 'mo.fecha_agarrada')
        ->where('mo.motoristas_id', $id)
        ->where('o.estado_5', 1) // orden preparada
        ->where('o.estado_8', 0) 
        ->whereNotIn('mo.ordenes_id', $pilaOrdenid)
        ->get(); 

        $sincompletar = 0;
        $sum = 0.0;
        foreach($ordenid as $o){
            $fechaagarrada = $o->fecha_agarrada;
            $hora = date("h:i A", strtotime($fechaagarrada)); 
            $fecha = date("d-m-Y", strtotime($fechaagarrada));
            $o->fecha_agarrada = $fecha . " " . $hora;  
 
            // sumar precio
            $precio = $o->precio_total + $o->precio_envio;
            $o->total = number_format((float)$precio, 2, '.', '');

            $sincompletar = $sincompletar + 1;
            $sum = $sum + $precio;
        }   

        $suma = number_format((float)$sum, 2, '.', '');

        return view('backend.paginas.revisador.tablas.tablaordenrevisada2', compact('ordenid', 'sincompletar', 'suma'));
    }

    // reporte para ordenes revisadas
    public function reporteordenrevisada($id, $fecha1, $fecha2){

        $date1 = Carbon::parse($fecha1)->format('Y-m-d');
        $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d'); 

        $f1 = Carbon::parse($fecha1)->format('d-m-Y');
        $f2 = Carbon::parse($fecha2)->format('d-m-Y'); 
        
        $orden = DB::table('ordenes_revisadas AS or')
        ->join('ordenes AS o', 'o.id', '=', 'or.ordenes_id')
        ->join('revisador AS r', 'r.id', '=', 'or.revisador_id')
        ->select('or.ordenes_id', 'or.revisador_id', 'o.precio_total', 'o.precio_envio', 'r.nombre', 'r.identificador', 'o.fecha_orden',  'or.fecha')
        ->where('or.revisador_id', $id)
        ->whereBetween('or.fecha', array($date1, $date2))
        ->get();
 
        $sum = 0.0;
        $conteo = 0;
        foreach($orden as $o){
            $fechaOrden = $o->fecha_orden;
            $hora1 = date("h:i A", strtotime($fechaOrden));
            $fecha1 = date("d-m-Y", strtotime($fechaOrden));
            $o->fecha_orden = $fecha1 . " " . $hora1;  
 
            $fecha = $o->fecha;
            $hora = date("h:i A", strtotime($fecha));
            $fecha = date("d-m-Y", strtotime($fecha));
            $o->fecha = $fecha . " " . $hora; 

            // sumar precio
            $precio = $o->precio_total + $o->precio_envio;
            $o->precio = number_format((float)$precio, 2, '.', '');

            $sum = $sum + $precio;
            $conteo = $conteo + 1;
        }
 
        $suma = number_format((float)$sum, 2, '.', '');

        $nombre = Revisador::where('id', $id)->pluck('nombre')->first();
        
        $view =  \View::make('backend.paginas.reportes.reporteordenrevisada', compact(['orden', 'conteo', 'suma', 'nombre', 'f1', 'f2']))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('carta', 'portrait');
 
        return $pdf->stream();
    }


    // reporte de productos vendidos
    public function reporteproductovendido($id, $fecha1, $fecha2){
        $date1 = Carbon::parse($fecha1)->format('Y-m-d');
        $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d');
       
        $f1 = Carbon::parse($fecha1)->format('d-m-Y');
        $f2 = Carbon::parse($fecha2)->format('d-m-Y');

        $orden = DB::table('ordenes AS o')
        ->join('ordenes_descripcion AS od', 'od.ordenes_id', '=', 'o.id')
        ->join('producto AS p', 'p.id', '=', 'od.producto_id')
        ->select('o.id', 'o.servicios_id', 'o.estado_5', 'p.nombre', 'p.precio', 'p.id AS idproducto', 'od.cantidad', 'od.precio', 'o.fecha_orden')
        ->where('o.servicios_id', $id)
        ->where('o.estado_5', 1)        
        ->whereBetween('o.fecha_orden', array($date1, $date2))
        ->orderBy('o.id', 'ASC')  
        ->get(); 
      
        $datos = array();
        $totaldinero = 0;
        $conteo = 0;
        
        foreach($orden as $fororden){

            $idp = $fororden->idproducto;
            $nombre = "";
            $cantidad = 0;
            $dinero = 0;
            $precio = 0;

            foreach($orden as $for2){

               if($for2->idproducto == $idp){
                    $nombre = $for2->nombre;
                    $cantidad = $cantidad + $for2->cantidad;
                    $dinero = $dinero + $for2->precio;
                    $precio = $for2->precio;
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

                // multiplicar 
                $multi = $precio * $cantidad;
                $conteo = $conteo + 1;

                $total = number_format((float)$multi, 2, '.', '');
                $totaldinero = $totaldinero + $total;
                $datos[] = array('idproducto' => $idp, 'nombre' => $nombre, 'conteo' => $conteo, 'cantidad' => $cantidad, 'precio' => $precio, 'total' => $total);
            }            
        } 

        $data = Servicios::where('id', $id)->first();
        $nombre = $data->nombre;

        $totalDinero = number_format((float)$totaldinero, 2, '.', ''); 

        $view =  \View::make('backend.paginas.reportes.reporteproductovendido', compact(['datos', 'totalDinero', 'nombre', 'f1', 'f2']))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('carta', 'portrait');
 
        return $pdf->stream();
    }


    /** REGISTRO DE PAGOS A SERVICIOS */

    public function index4(){

        $servicio = Servicios::all();
        return view('backend.paginas.pagoservicio.listaregistroserviciopago', compact('servicio'));
    } 

    // tabla lista registro pago a servicios
    public function tablapagoservicio(){ 
         
        $servicio = DB::table('servicio_pago AS sp')
        ->join('servicios AS s', 's.id', '=', 'sp.servicios_id')
        ->select('sp.id', 's.identificador', 's.nombre', 'sp.pago', 'sp.fecha1', 'sp.descripcion', 'sp.fecha2', 'sp.fecha')
        ->get();

        foreach($servicio as $o){
            $fecha1 = $o->fecha1;
            $f1 = date("d-m-Y", strtotime($fecha1));
            $o->fecha1 = $f1;

            $fecha2 = $o->fecha2;
            $f2 = date("d-m-Y", strtotime($fecha2));
            $o->fecha2 = $f2;  

            $fecha = $o->fecha;
            $f3 = date("d-m-Y", strtotime($fecha));
            $o->fecha = $f3;  
        }

        return view('backend.paginas.pagoservicio.tablas.tablalistaregistroserviciopago', compact('servicio'));
    } 
  
    // nuevo registro de pago a servicio
    public function nuevopagoservicio(Request $request){
        if($request->isMethod('post')){

            $regla = array(
                'id' => 'required',
                'fecha1' => 'required',
                'fecha2' => 'required',
                'pago' => 'required',
            );

            $mensaje = array(
                'id.required' => 'id es requerido',
                'fecha1.required' => 'fecha desde es requerido',
                'fecha2.required' => 'fecha hasta requerida',
                'pago.required' => 'pago es requerido',                
                );

                
            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }  

            $fecha = Carbon::now('America/El_Salvador');

            $m = new ServicioPago();
            $m->servicios_id = $request->id;
            $m->fecha1 = $request->fecha1;
            $m->fecha2 = $request->fecha2;
            $m->fecha = $fecha;
            $m->pago = $request->pago;
            $m->descripcion = $request->descripcion;
            
            if($m->save()){
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }                    
        }        
    }

    // ver cuanto se le ha pagado a un servicio en total
    public function totalpagadoservicio(Request $request){
        if($request->isMethod('post')){

            $regla = array(
                'id' => 'required'               
            );

            $mensaje = array(
                'id.required' => 'id es requerido'                          
                );
 
                
            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }  

           if(Servicios::where('id', $request->id)->first()){

            $servicio = DB::table('servicio_pago')
            ->where('servicios_id', $request->id)
            ->get();
    
            $dinero = 0;
            foreach($servicio as $o){
                $dinero = $dinero + $o->pago;                             
            }

            $total = number_format((float)$dinero, 2, '.', '');

            return ['success' => 1, 'total' => $total];
           }else{
               return ['success' => 2, 'total' => $total];
           }              
        }        
    }
   
} 
  