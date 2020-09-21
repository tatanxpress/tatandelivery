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
use App\OrdenesCupones;
use App\Cupones;
use App\AplicaCuponUno;
use App\AplicaCuponDos;
use App\AplicaCuponTres;
use App\AplicaCuponCuatro;
use App\AplicaCuponCinco;
use App\OrdenesDirecciones;
use App\EncargoAsignadoServicio;
use App\Encargos;
use App\OrdenesEncargo;
use App\OrdenesEncargoRevisadas;
use App\Instituciones;
use App\AreasPermitidas;
use App\User;


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

        $servicios = Servicios::orderBy('nombre', 'ASC')->get();
        return view('backend.paginas.pagoservicio.listapagoservicio', compact('servicios'));
    }   

    // buscador de ordenes completas de un servicio, y si utiliza cupon
    public function buscador($idservicio, $fecha1, $fecha2, $cupon){
    
        if(Servicios::where('id', $idservicio)->first()){
 
            $date1 = Carbon::parse($fecha1)->format('Y-m-d');
            $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d'); 

            if($cupon == 0){ // Ninguno

                // obtener todas las ordenes id para evitar agarrar esas
                $todas = DB::table('ordenes_cupones AS oc')
                ->join('ordenes AS o', 'o.id', '=', 'oc.ordenes_id')
                ->whereBetween('o.fecha_orden', array($date1, $date2)) 
                ->get();

                $pilaOrden = array();
                foreach($todas as $p){
                    array_push($pilaOrden, $p->ordenes_id);
                }

                $orden = DB::table('ordenes AS o')
                ->join('servicios AS s', 's.id', '=', 'o.servicios_id')                
                ->select('o.id AS idorden', 'o.precio_total', 'o.fecha_orden', 
                's.identificador AS identiservicio', 'o.estado_7', 'o.estado_8', 'o.estado_5')
                ->where('s.id', $idservicio)
                ->where('o.estado_5', 1) // ordenes completadas
                ->where('o.estado_8', 0) // no canceladas, no afecta cuando se cancela por panel de control
                ->whereBetween('o.fecha_orden', array($date1, $date2))    
                ->whereNotIn('o.id', $pilaOrden)
                ->get();
    
                    foreach($orden as $o){                       
                        $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));
                    }
      
                return view('backend.paginas.pagoservicio.tablas.tablalistapagoservicio', compact('orden'));
            }else if($cupon == 1){ // Revueltos

                $orden = DB::table('ordenes AS o')
                ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
                ->select('o.id AS idorden', 'o.precio_total', 'o.fecha_orden', 
                's.identificador AS identiservicio', 'o.estado_7', 'o.estado_8', 'o.estado_5')
                ->where('s.id', $idservicio)
                ->where('o.estado_5', 1) // ordenes completadas
                ->where('o.estado_8', 0) // no canceladas, no afecta cuando se cancela por panel de control
                ->whereBetween('o.fecha_orden', array($date1, $date2))          
                ->get();
 
                // conocer que tipo de cupon es
                foreach($orden as $o){

                    // buscar si esta orden esta registrada en cupones
                    if($cc = OrdenesCupones::where('ordenes_id', $o->idorden)->first()){

                        $c = Cupones::where('id', $cc->cupones_id)->first();
                       
                        if($c->tipo_cupon_id == 1){
                            $o->tipocupon = "Envio Gratis"; 
                        }else if($c->tipo_cupon_id == 2){
                            $info = AplicaCuponDos::where('ordenes_id', $o->idorden)->first();
                            if($info->aplico_envio_gratis == 1){
                                $o->tipocupon = "Descuento Dinero Con Envio Gratis";
                            }else{
                                $o->tipocupon = "Descuento Dinero Sin Envio Gratis";
                            }
                            
                        }else if($c->tipo_cupon_id == 3){
                            $o->tipocupon = "Descuento Porcentaje";
                        }else if($c->tipo_cupon_id == 4){
                            $o->tipocupon = "Producto Gratis";
                        }else if($c->tipo_cupon_id == 5){
                            $o->tipocupon = "Donacion";
                        }
                        else{
                            $o->tipocupon = "Ninguno";
                        }                       
                    }else{
                        $o->tipocupon = "Ninguno";
                    } 
                    
                } 
              
                return view('backend.paginas.pagoservicio.tablas.tablalistapagoserviciocuponmixto', compact('orden'));
                
            }else if($cupon == 2){ // Envio gratis
                $orden = DB::table('ordenes AS o')
                ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
                ->join('ordenes_cupones AS oc', 'oc.ordenes_id', '=', 'o.id')
                ->join('cupones AS c', 'c.id', '=', 'oc.cupones_id')
                ->select('o.id AS idorden', 'o.precio_total', 'o.fecha_orden', 
                's.identificador AS identiservicio', 'oc.cupones_id', 'o.estado_7', 
                'o.estado_8', 'o.estado_5', 'c.tipo_cupon_id')
                ->where('s.id', $idservicio)
                ->where('o.estado_5', 1) // ordenes completadas
                ->where('o.estado_8', 0) // no canceladas, no afecta cuando se cancela por panel de control
                ->where('c.tipo_cupon_id', 1) // cupon envio gratis
                ->whereBetween('o.fecha_orden', array($date1, $date2))          
                ->get();
               
                return view('backend.paginas.pagoservicio.tablas.tablalistapagoservicio', compact('orden'));
                
            }else if($cupon == 3){ // Descuento dinero
                $orden = DB::table('ordenes AS o')
                ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
                ->join('ordenes_cupones AS oc', 'oc.ordenes_id', '=', 'o.id')
                ->join('cupones AS c', 'c.id', '=', 'oc.cupones_id')
                ->select('o.id AS idorden', 'o.precio_total', 'o.fecha_orden', 
                's.identificador AS identiservicio', 'oc.cupones_id', 'o.estado_7', 
                'o.estado_8', 'o.estado_5', 'c.tipo_cupon_id')
                ->where('s.id', $idservicio)
                ->where('o.estado_5', 1) // ordenes completadas
                ->where('o.estado_8', 0) // no canceladas, no afecta cuando se cancela por panel de control
                ->where('c.tipo_cupon_id', 2) // cupon descuento
                ->whereBetween('o.fecha_orden', array($date1, $date2))          
                ->get();

                // conocer que tipo de cupon es
                foreach($orden as $o){
                    $info = AplicaCuponDos::where('ordenes_id', $o->idorden)->first();
                                      
                    $descuento = $o->precio_total - $info->dinero;
                    if($descuento <= 0){ 
                        $descuento = 0;
                    }
                   
                    $o->descuento = $info->dinero;
                    if($info->aplico_envio_gratis == 1){
                        $o->aplico = "Si Envio Gratis";
                    }else{
                        $o->aplico = "No Envio Gratis";
                    }
                    
                    $o->total = number_format((float)$descuento, 2, '.', '');
                }

                return view('backend.paginas.pagoservicio.tablas.tablalistapagoserviciocupondescuentod', compact('orden'));
                
            }else if($cupon == 4){ // Descuento porcentaje
                $orden = DB::table('ordenes AS o')
                ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
                ->join('ordenes_cupones AS oc', 'oc.ordenes_id', '=', 'o.id')
                ->join('cupones AS c', 'c.id', '=', 'oc.cupones_id')
                ->select('o.id AS idorden', 'o.precio_total', 'o.fecha_orden', 
                's.identificador AS identiservicio', 'oc.cupones_id', 'o.estado_7', 
                'o.estado_8', 'o.estado_5', 'c.tipo_cupon_id')
                ->where('s.id', $idservicio)
                ->where('o.estado_5', 1) // ordenes completadas
                ->where('o.estado_8', 0) // no canceladas, no afecta cuando se cancela por panel de control
                ->where('c.tipo_cupon_id', 3) // cupon porcentaje
                ->whereBetween('o.fecha_orden', array($date1, $date2))          
                ->get();

                // conocer que tipo de cupon es
                foreach($orden as $o){
                    $info = AplicaCuponTres::where('ordenes_id', $o->idorden)->first();                                      
                    $o->porcentaje = $info->porcentaje;

                    $resta = $o->precio_total * ($info->porcentaje / 100);
                    $total = $o->precio_total - $resta;

                    if($total <= 0){
                        $total = 0;
                    }
                   
                    $o->total = number_format((float)$total, 2, '.', '');
                }
 
                return view('backend.paginas.pagoservicio.tablas.tablalistapagoserviciocupondescuentop', compact('orden'));
                
            }else if($cupon == 5){ // Producto Gratis
                $orden = DB::table('ordenes AS o')
                ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
                ->join('ordenes_cupones AS oc', 'oc.ordenes_id', '=', 'o.id')
                ->join('cupones AS c', 'c.id', '=', 'oc.cupones_id')
                ->select('o.id AS idorden', 'o.precio_total', 'o.fecha_orden', 
                's.identificador AS identiservicio', 'oc.cupones_id', 'o.estado_7', 
                'o.estado_8', 'o.estado_5', 'c.tipo_cupon_id')
                ->where('s.id', $idservicio)
                ->where('o.estado_5', 1) // ordenes completadas
                ->where('o.estado_8', 0) // no canceladas, no afecta cuando se cancela por panel de control
                ->where('c.tipo_cupon_id', 4) // cupon producto gratis
                ->whereBetween('o.fecha_orden', array($date1, $date2))          
                ->get();

                // conocer que tipo de cupon es
                foreach($orden as $o){
                    $info = AplicaCuponCuatro::where('ordenes_id', $o->idorden)->first();                                      
                    $o->producto = $info->producto;
                }

                return view('backend.paginas.pagoservicio.tablas.tablalistapagoserviciocuponproducto', compact('orden'));
            
            }else if($cupon == 6){ // Donacion
                $orden = DB::table('ordenes AS o')
                ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
                ->join('ordenes_cupones AS oc', 'oc.ordenes_id', '=', 'o.id')
                ->join('cupones AS c', 'c.id', '=', 'oc.cupones_id')
                ->select('o.id AS idorden', 'o.precio_total', 'o.fecha_orden', 
                's.identificador AS identiservicio', 'oc.cupones_id', 'o.estado_7', 
                'o.estado_8', 'o.estado_5', 'c.tipo_cupon_id', 'o.precio_envio')
                ->where('s.id', $idservicio)
                ->where('o.estado_5', 1) // ordenes completadas
                ->where('o.estado_8', 0) // no canceladas, no afecta cuando se cancela por panel de control
                ->where('c.tipo_cupon_id', 5) // cupon donacion
                ->whereBetween('o.fecha_orden', array($date1, $date2))          
                ->get();

                // conocer que tipo de cupon es
                foreach($orden as $o){
                    $info = AplicaCuponCinco::where('ordenes_id', $o->idorden)->first();                                      
                    $o->donacion = $info->dinero;

                    $total = $o->precio_total + $o->precio_envio + $info->dinero;

                    $o->total = number_format((float)$total, 2, '.', '');
                } 

                return view('backend.paginas.pagoservicio.tablas.tablalistapagoserviciocupondonacion', compact('orden'));
            } 
           
        }else{
            return ['success' => 2];
        }
    }
         
    // reporte de ordenes completadas para pagar a servicios
    function reporte($idservicio, $fecha1, $fecha2, $cupon){

        $date1 = Carbon::parse($fecha1)->format('Y-m-d');
        $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d');

        $f1 = Carbon::parse($fecha1)->format('d-m-Y');
        $f2 = Carbon::parse($fecha2)->format('d-m-Y');

        if($cupon == 0){ // Todos mesclados, sin cupon, con cupon, pagadas a propietarios y no pagadas a propietarios

            $orden = DB::table('ordenes')
            ->select('id', 'precio_total', 'fecha_orden', 'pago_a_propi', 'tipo_pago', 'users_id')
            ->where('servicios_id', $idservicio) // ordenes de este servicio
            ->where('estado_5', 1) // ordenes completadas
            ->where('estado_8', 0) // no canceladas
            ->whereBetween('fecha_orden', array($date1, $date2)) // unicamente esta fecha
            ->get(); 
         
            $totalDinero = 0;
            foreach($orden as $o){
                //sumar 
                $totalDinero = $totalDinero + $o->precio_total;
                $o->fecha_orden = date("d-m-Y h:i A", strtotime($o->fecha_orden));

                $cupon = "";
                if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()){
                    $cc = Cupones::where('id', $oc->cupones_id)->first();
                    if($cc->tipo_cupon_id == 1){
                        $cupon = "envio gratis";
                    }else if($cc->tipo_cupon_id == 2){
                        $data = AplicaCuponDos::where('ordenes_id', $o->id)->first();
                        $dinero = number_format((float)$data->dinero, 2, '.', '');
                        if($data->aplico_envio_gratis == 1){                            
                            $cupon = "Descuento de dinero de: $" . $dinero . " Y envio gratis"; 
                        }else{
                            $cupon = "Descuento de dinero de: $" . $dinero;
                        }

                    }else if($cc->tipo_cupon_id == 3){
                        $data = AplicaCuponTres::where('ordenes_id', $o->id)->first();

                        $cupon = "Descuento porcentaje de: " . $data->porcentaje . "%";
                       

                    }else if($cc->tipo_cupon_id == 4){
                        $data = AplicaCuponCuatro::where('ordenes_id', $o->id)->first();
                        $cupon = "Producto Gratis: " . $data->producto;
                    }else if($cc->tipo_cupon_id == 5){
                        $data = AplicaCuponCinco::where('ordenes_id', $o->id)->first();
                        $nombre = Instituciones::where('id', $data->instituciones_id)->pluck('nombre')->first();
                        $cupon = "Donacion de: $" . $data->dinero . " A: " . $nombre;
                    }
                } 

                $o->cupon = $cupon;

                $usuarioarea = User::where('id', $o->users_id)->pluck('area')->first();
                
                $area = "Extranjero " . $usuarioarea;
                if(AreasPermitidas::where('areas', $usuarioarea)->first()){
                    $area = "Nacional";
                }
                
                $o->area = $area; 
            }

            $data = Servicios::where('id', $idservicio)->first();
            $nombre = $data->nombre; // nombre servicio
            $comision = $data->comision; // comision del servicio

            $totalDinero = number_format((float)$totalDinero, 2, '.', '');
            
            $suma = ($totalDinero * $comision) / 100; // dinero restado

            $pagar = ($totalDinero - $suma); // restar dinero al total de todas las ordenes
            $pagar = number_format((float)$pagar, 2, '.', ''); // poner decimales 
            $suma = number_format((float)$suma, 2, '.', ''); // dinero que se restara al total de ordenes 
    
            $view =  \View::make('backend.paginas.reportes.servicios.reportepagoservicio', compact(['orden', 'comision', 'suma', 'totalDinero', 'nombre', 'pagar', 'f1', 'f2']))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('carta', 'portrait');     
            return $pdf->stream();

        }else if($cupon == 1){ // Envio gratis
            $orden = DB::table('ordenes AS o')
            ->join('ordenes_cupones AS oc', 'oc.ordenes_id', '=', 'o.id')
            ->join('cupones AS c', 'c.id', '=', 'oc.cupones_id')
            ->select('o.id', 'o.fecha_orden')
            ->where('o.servicios_id', $idservicio)
            ->where('o.estado_5', 1) // ordenes completadas
            ->where('o.estado_8', 0) // no canceladas, no afecta cuando se cancela por panel de control
            ->where('c.tipo_cupon_id', 1) // cupon envio gratis
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get(); 
 
            $enviototal = 0;
            // obtener el precio de zona que era
            foreach($orden as $o){
                $precio = OrdenesDirecciones::where('ordenes_id', $o->id)->pluck('copia_envio')->first();
                $o->copia_envio = $precio;
                $o->fecha_orden = date("d-m-Y", strtotime($o->fecha_orden));
                $enviototal = $enviototal + $precio;
            }
        
            $enviototal = number_format((float)$enviototal, 2, '.', ''); 
            $nombre = Servicios::where('id', $idservicio)->pluck('nombre')->first();
    
            $view =  \View::make('backend.paginas.reportes.servicios.reportepagoserviciocupon1', compact(['orden', 'enviototal', 'nombre', 'f1', 'f2']))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('carta', 'portrait');    
            return $pdf->stream();
            
        }else if($cupon == 2){ // Descuento dinero
            $orden = DB::table('ordenes AS o')
            ->join('ordenes_cupones AS oc', 'oc.ordenes_id', '=', 'o.id')
            ->join('cupones AS c', 'c.id', '=', 'oc.cupones_id')
            ->select('o.id', 'o.fecha_orden', 'o.precio_total')
            ->where('o.servicios_id', $idservicio)
            ->where('o.estado_5', 1) // ordenes completadas
            ->where('o.estado_8', 0) // no canceladas, no afecta cuando se cancela por panel de control
            ->where('c.tipo_cupon_id', 2) // cupon descuento dinero
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get();

            $totalorden = 0; // total de la orden
            $totaldescuento = 0; // total de descuento por los cupones
            $totalenvio = 0; // total solo donde aplico el envio zona
          
            // conocer que tipo de cupon es
            foreach($orden as $o){
                $o->fecha_orden = date("d-m-Y", strtotime($o->fecha_orden));

                $info = AplicaCuponDos::where('ordenes_id', $o->id)->first();
                $precio = OrdenesDirecciones::where('ordenes_id', $o->id)->pluck('copia_envio')->first();
                $o->copia_envio = $precio; // copia del envio a esa zona
                $o->descuento = $info->dinero; // lo que se descuenta
                                                
                $descuento = $o->precio_total - $info->dinero; // restar precio de orden - descuento
                if($descuento <= 0){ // evitar numeros negativos
                    $descuento = 0;
                }
  
                if($info->aplico_envio_gratis == 1){
                    $o->aplica = 1; // si aplico, mostrara copia envio zona en reporte
                    $totalenvio = $totalenvio + $precio;
                }else{
                    $o->aplica = 0;
                }
                
                $o->total = number_format((float)$descuento, 2, '.', '');
                $totalorden = $totalorden + $o->precio_total;
                $totaldescuento = $totaldescuento + $info->dinero;   
            }

            $totalorden = number_format((float)$totalorden, 2, '.', '');
            $totaldescuento = number_format((float)$totaldescuento, 2, '.', '');
            $totalenvio = number_format((float)$totalenvio, 2, '.', '');


            $nombre = Servicios::where('id', $idservicio)->pluck('nombre')->first();
    
            $view =  \View::make('backend.paginas.reportes.servicios.reportepagoserviciocupon2', compact(['orden', 'totalorden', 'totaldescuento', 'totalenvio', 'nombre', 'f1', 'f2']))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('carta', 'portrait');    
            return $pdf->stream();
            
        }else if($cupon == 3){ // Descuento porcentaje
            
            $orden = DB::table('ordenes AS o')
            ->join('ordenes_cupones AS oc', 'oc.ordenes_id', '=', 'o.id')
            ->join('cupones AS c', 'c.id', '=', 'oc.cupones_id')
            ->select('o.id', 'o.fecha_orden', 'o.precio_total')
            ->where('o.servicios_id', $idservicio)
            ->where('o.estado_5', 1) // ordenes completadas
            ->where('o.estado_8', 0) // no canceladas, no afecta cuando se cancela por panel de control
            ->where('c.tipo_cupon_id', 3) // cupon descuento dinero
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get();

            $totalorden = 0; // total de la orden
            $totaldescontado = 0; // total de la orden, afectado por porcentaje
            $sumardescuento = 0;
            // conocer que tipo de cupon es
            foreach($orden as $o){
                $o->fecha_orden = date("d-m-Y", strtotime($o->fecha_orden));

                $porcentaje= AplicaCuponTres::where('ordenes_id', $o->id)->pluck('porcentaje')->first();
                $o->porcentaje = $porcentaje; // copia del envio a esa zona
                
                $resta = $o->precio_total * ($porcentaje / 100);                
                $o->descuento = number_format((float)$resta, 2, '.', '');
                $sumardescuento = $sumardescuento + $resta;
                $total = $o->precio_total - $resta;

                if($total <= 0){ 
                    $total = 0;
                }               
                
                $o->total = number_format((float)$total, 2, '.', '');// ya descontado el porcentaje
                $sumardescuento = number_format((float)$sumardescuento, 2, '.', '');
                $totalorden = $totalorden + $o->precio_total; // total orden   
                $totaldescontado = $totaldescontado + $total;                   
            }

            $totalorden = number_format((float)$totalorden, 2, '.', '');
            $totaldescontado = number_format((float)$totaldescontado, 2, '.', '');
            
            $nombre = Servicios::where('id', $idservicio)->pluck('nombre')->first();
    
            $view =  \View::make('backend.paginas.reportes.servicios.reportepagoserviciocupon3', compact(['orden', 'sumardescuento', 'totalorden', 'totaldescontado', 'nombre', 'f1', 'f2']))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('carta', 'portrait');    
            return $pdf->stream();
            
        }else if($cupon == 4){ // Producto Gratis
         
            $orden = DB::table('ordenes AS o')
            ->join('ordenes_cupones AS oc', 'oc.ordenes_id', '=', 'o.id')
            ->join('cupones AS c', 'c.id', '=', 'oc.cupones_id')
            ->select('o.id', 'o.fecha_orden', 'o.precio_total')
            ->where('o.servicios_id', $idservicio)
            ->where('o.estado_5', 1) // ordenes completadas
            ->where('o.estado_8', 0) // no canceladas, no afecta cuando se cancela por panel de control
            ->where('c.tipo_cupon_id', 4) // cupon descuento dinero
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get();

            $totalorden = 0; // total de la orden
            $totaldescontado = 0; // total de la orden, afectado por porcentaje
            // conocer que tipo de cupon es
            foreach($orden as $o){
                $o->fecha_orden = date("d-m-Y", strtotime($o->fecha_orden));

                $info = AplicaCuponCuatro::where('ordenes_id', $o->id)->first();
                $o->minimo = $info->dinero_carrito; // minimo a comprar para aplicar cupon
                $o->producto = $info->producto;               
            }
            
            $nombre = Servicios::where('id', $idservicio)->pluck('nombre')->first();
    
            $view =  \View::make('backend.paginas.reportes.servicios.reportepagoserviciocupon4', compact(['orden', 'nombre', 'f1', 'f2']))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('carta', 'portrait');    
            return $pdf->stream(); 
 
        }
        else if($cupon == 5){ // donacion
            $orden = DB::table('ordenes AS o')
            ->join('ordenes_cupones AS oc', 'oc.ordenes_id', '=', 'o.id')
            ->join('cupones AS c', 'c.id', '=', 'oc.cupones_id')
            ->select('o.id', 'o.fecha_orden', 'o.precio_total', 'o.precio_envio')
            ->where('o.servicios_id', $idservicio)
            ->where('o.estado_5', 1) // ordenes completadas
            ->where('o.estado_8', 0) // no canceladas, no afecta cuando se cancela por panel de control
            ->where('c.tipo_cupon_id', 5) // cupon donacion
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get();

            $total = 0;
            $totalenvio = 0; 
            $totaldonacion = 0; 
          
            // conocer que tipo de cupon es
            foreach($orden as $o){
                $o->fecha_orden = date("d-m-Y", strtotime($o->fecha_orden));

                $info = AplicaCuponCinco::where('ordenes_id', $o->id)->first();
                $data = Instituciones::where('id', $info->instituciones_id)->first();

                $o->lugar = $data->nombre;
                $o->donacion = $info->dinero;
                
                $total = $total + $o->precio_total;
                $totalenvio = $totalenvio + $o->precio_envio; 
                $totaldonacion = $totaldonacion + $info->dinero;
            }

            $total = number_format((float)$total, 2, '.', '');
            $totalenvio = number_format((float)$totalenvio, 2, '.', '');
            $totaldonacion = number_format((float)$totaldonacion, 2, '.', '');
    
            $view =  \View::make('backend.paginas.reportes.servicios.reportepagoserviciocupon5', compact(['orden', 'total', 'totalenvio', 'totaldonacion', 'f1', 'f2']))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('carta', 'portrait');    
            return $pdf->stream();
        }        
        else if($cupon == 6){ // solo pagadas a propietarios

             $orden = DB::table('ordenes')
            ->select('id', 'precio_total', 'fecha_orden', 'pago_a_propi')
            ->where('servicios_id', $idservicio) // ordenes de este servicio
            ->where('estado_5', 1) // ordenes completadas
            ->where('estado_8', 0) // no canceladas
            ->where('pago_a_propi', 1) // unicamente pagadas a propietarios
            ->whereBetween('fecha_orden', array($date1, $date2)) // unicamente esta fecha
            ->get(); 
         
            $totalDinero = 0;
            foreach($orden as $o){
                //sumar 
                $totalDinero = $totalDinero + $o->precio_total;
                $o->fecha_orden = date("d-m-Y h:i A", strtotime($o->fecha_orden));

                $cupon = "";
                if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()){
                    $cc = Cupones::where('id', $oc->cupones_id)->first();
                    if($cc->tipo_cupon_id == 1){
                        $cupon = "envio gratis";
                    }else if($cc->tipo_cupon_id == 2){
                        $data = AplicaCuponDos::where('ordenes_id', $o->id)->first();
                        $dinero = number_format((float)$data->dinero, 2, '.', '');
                        if($data->aplico_envio_gratis == 1){                            
                            $cupon = "Descuento de dinero de: $" . $dinero . " Y envio gratis"; 
                        }else{
                            $cupon = "Descuento de dinero de: $" . $dinero;
                        }

                    }else if($cc->tipo_cupon_id == 3){
                        $data = AplicaCuponTres::where('ordenes_id', $o->id)->first();

                        $cupon = "Descuento porcentaje de: " . $data->porcentaje . "%";
                       

                    }else if($cc->tipo_cupon_id == 4){
                        $data = AplicaCuponCuatro::where('ordenes_id', $o->id)->first();
                        $cupon = "Producto Gratis: " . $data->producto;
                    }else if($cc->tipo_cupon_id == 5){
                        $data = AplicaCuponCinco::where('ordenes_id', $o->id)->first();
                        $nombre = Instituciones::where('id', $data->instituciones_id)->pluck('nombre')->first();
                        $cupon = "Donacion de: $" . $data->dinero . " A: " . $nombre;
                    }
                }

                $o->cupon = $cupon;
            }

            $data = Servicios::where('id', $idservicio)->first();
            $nombre = $data->nombre; // nombre servicio
            $comision = $data->comision; // comision del servicio

            $totalDinero = number_format((float)$totalDinero, 2, '.', '');
            
            $suma = ($totalDinero * $comision) / 100; // dinero restado

            $pagar = ($totalDinero - $suma); // restar dinero al total de todas las ordenes
            $pagar = number_format((float)$pagar, 2, '.', ''); // poner decimales 
            $suma = number_format((float)$suma, 2, '.', ''); // dinero que se restara al total de ordenes 
    
            $view =  \View::make('backend.paginas.reportes.servicios.reportepagoservicio', compact(['orden', 'comision', 'suma', 'totalDinero', 'nombre', 'pagar', 'f1', 'f2']))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('carta', 'portrait');     
            return $pdf->stream();
        }
        else if($cupon == 7){ // solo no pagadas a propietarios

            $orden = DB::table('ordenes')
           ->select('id', 'precio_total', 'fecha_orden', 'pago_a_propi')
           ->where('servicios_id', $idservicio) // ordenes de este servicio
           ->where('estado_5', 1) // ordenes completadas
           ->where('estado_8', 0) // no canceladas
           ->where('pago_a_propi', 0) // unicamente NO pagadas a propietarios
           ->whereBetween('fecha_orden', array($date1, $date2)) // unicamente esta fecha
           ->get(); 
        
           $totalDinero = 0;
           foreach($orden as $o){
                //sumar 
                $totalDinero = $totalDinero + $o->precio_total;
                $o->fecha_orden = date("d-m-Y h:i A", strtotime($o->fecha_orden));

                $cupon = "";
                if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()){
                   $cc = Cupones::where('id', $oc->cupones_id)->first();
                    if($cc->tipo_cupon_id == 1){
                       $cupon = "envio gratis";
                    }else if($cc->tipo_cupon_id == 2){
                       $data = AplicaCuponDos::where('ordenes_id', $o->id)->first();
                       $dinero = number_format((float)$data->dinero, 2, '.', '');
                       if($data->aplico_envio_gratis == 1){                            
                           $cupon = "Descuento de dinero de: $" . $dinero . " Y envio gratis"; 
                       }else{
                           $cupon = "Descuento de dinero de: $" . $dinero;
                       }

                    }else if($cc->tipo_cupon_id == 3){
                       $data = AplicaCuponTres::where('ordenes_id', $o->id)->first();

                       $cupon = "Descuento porcentaje de: " . $data->porcentaje . "%";
                      

                    }else if($cc->tipo_cupon_id == 4){
                       $data = AplicaCuponCuatro::where('ordenes_id', $o->id)->first();
                       $cupon = "Producto Gratis: " . $data->producto;
                    }else if($cc->tipo_cupon_id == 5){
                       $data = AplicaCuponCinco::where('ordenes_id', $o->id)->first();
                       $nombre = Instituciones::where('id', $data->instituciones_id)->pluck('nombre')->first();
                       $cupon = "Donacion de: $" . $data->dinero . " A: " . $nombre;
                    }
                }

               $o->cupon = $cupon;
            }

           $data = Servicios::where('id', $idservicio)->first();
           $nombre = $data->nombre; // nombre servicio
           $comision = $data->comision; // comision del servicio

           $totalDinero = number_format((float)$totalDinero, 2, '.', '');
           
           $suma = ($totalDinero * $comision) / 100; // dinero restado

           $pagar = ($totalDinero - $suma); // restar dinero al total de todas las ordenes
           $pagar = number_format((float)$pagar, 2, '.', ''); // poner decimales 
           $suma = number_format((float)$suma, 2, '.', ''); // dinero que se restara al total de ordenes 
   
           $view =  \View::make('backend.paginas.reportes.servicios.reportepagoservicio', compact(['orden', 'comision', 'suma', 'totalDinero', 'nombre', 'pagar', 'f1', 'f2']))->render();
           $pdf = \App::make('dompdf.wrapper');
           $pdf->loadHTML($view)->setPaper('carta', 'portrait');     
           return $pdf->stream();
       }
    }


    // unicamente tablas
    // reporte de ordenes completadas para pagar a servicios
    function reporteTablas($idservicio, $fecha1, $fecha2, $cupon){

        $date1 = Carbon::parse($fecha1)->format('Y-m-d');
        $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d');

        $f1 = Carbon::parse($fecha1)->format('d-m-Y');
        $f2 = Carbon::parse($fecha2)->format('d-m-Y');

        if($cupon == 0){ // Ninguno

            $orden = DB::table('ordenes')
            ->select('id', 'precio_total', 'fecha_orden')
            ->where('servicios_id', $idservicio) // ordenes de este servicio
            ->where('estado_5', 1) // ordenes completadas
            ->where('estado_8', 0) // no canceladas
            ->whereBetween('fecha_orden', array($date1, $date2)) // unicamente esta fecha
            ->get(); 
         
            $totalDinero = 0;
            foreach($orden as $o){
                //sumar 
                $totalDinero = $totalDinero + $o->precio_total;
                $o->fecha_orden = date("d-m-Y", strtotime($o->fecha_orden));
            }

            $data = Servicios::where('id', $idservicio)->first();
            $nombre = $data->nombre; // nombre servicio
            $comision = $data->comision; // comision del servicio

            $totalDinero = number_format((float)$totalDinero, 2, '.', '');
            
            $suma = ($totalDinero * $comision) / 100; // dinero restado

            //$pagar = number_format((float)$pagar, 2, '.', '');
            $pagar = ($totalDinero - $suma); // restar dinero al total de todas las ordenes
            $pagar = number_format((float)$pagar, 2, '.', ''); // poner decimales 
            $suma = number_format((float)$suma, 2, '.', ''); // dinero que se restara al total de ordenes 
    
            $view =  \View::make('backend.paginas.reportes.servicios.reportepagoservicio-tablas', compact(['orden', 'comision', 'suma', 'totalDinero', 'nombre', 'pagar', 'f1', 'f2']))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('carta', 'portrait');     
            return $pdf->stream();

        }else if($cupon == 1){ // Envio gratis
            $orden = DB::table('ordenes AS o')
            ->join('ordenes_cupones AS oc', 'oc.ordenes_id', '=', 'o.id')
            ->join('cupones AS c', 'c.id', '=', 'oc.cupones_id')
            ->select('o.id', 'o.fecha_orden')
            ->where('o.servicios_id', $idservicio)
            ->where('o.estado_5', 1) // ordenes completadas
            ->where('o.estado_8', 0) // no canceladas, no afecta cuando se cancela por panel de control
            ->where('c.tipo_cupon_id', 1) // cupon envio gratis
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get(); 
 
            $enviototal = 0;
            // obtener el precio de zona que era
            foreach($orden as $o){
                $precio = OrdenesDirecciones::where('ordenes_id', $o->id)->pluck('copia_envio')->first();
                $o->copia_envio = $precio;
                $o->fecha_orden = date("d-m-Y", strtotime($o->fecha_orden));
                $enviototal = $enviototal + $precio;
            }
        
            $enviototal = number_format((float)$enviototal, 2, '.', ''); 
            $nombre = Servicios::where('id', $idservicio)->pluck('nombre')->first();
    
            $view =  \View::make('backend.paginas.reportes.servicios.reportepagoserviciocupon1-tablas', compact(['orden', 'enviototal', 'nombre', 'f1', 'f2']))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('carta', 'portrait');    
            return $pdf->stream();
            
        }else if($cupon == 2){ // Descuento dinero
            $orden = DB::table('ordenes AS o')
            ->join('ordenes_cupones AS oc', 'oc.ordenes_id', '=', 'o.id')
            ->join('cupones AS c', 'c.id', '=', 'oc.cupones_id')
            ->select('o.id', 'o.fecha_orden', 'o.precio_total')
            ->where('o.servicios_id', $idservicio)
            ->where('o.estado_5', 1) // ordenes completadas
            ->where('o.estado_8', 0) // no canceladas, no afecta cuando se cancela por panel de control
            ->where('c.tipo_cupon_id', 2) // cupon descuento dinero
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get();

            $totalorden = 0; // total de la orden
            $totaldescuento = 0; // total de descuento por los cupones
            $totalenvio = 0; // total solo donde aplico el envio zona
          
            // conocer que tipo de cupon es
            foreach($orden as $o){
                $o->fecha_orden = date("d-m-Y", strtotime($o->fecha_orden));

                $info = AplicaCuponDos::where('ordenes_id', $o->id)->first();
                $precio = OrdenesDirecciones::where('ordenes_id', $o->id)->pluck('copia_envio')->first();
                $o->copia_envio = $precio; // copia del envio a esa zona
                $o->descuento = $info->dinero; // lo que se descuenta
                                                
                $descuento = $o->precio_total - $info->dinero; // restar precio de orden - descuento
                if($descuento <= 0){ // evitar numeros negativos
                    $descuento = 0;
                }
  
                if($info->aplico_envio_gratis == 1){
                    $o->aplica = 1; // si aplico, mostrara copia envio zona en reporte
                    $totalenvio = $totalenvio + $precio;
                }else{
                    $o->aplica = 0;
                }
                
                $o->total = number_format((float)$descuento, 2, '.', '');
                $totalorden = $totalorden + $o->precio_total;
                $totaldescuento = $totaldescuento + $info->dinero;   
            }

            $totalorden = number_format((float)$totalorden, 2, '.', '');
            $totaldescuento = number_format((float)$totaldescuento, 2, '.', '');
            $totalenvio = number_format((float)$totalenvio, 2, '.', '');


            $nombre = Servicios::where('id', $idservicio)->pluck('nombre')->first();
    
            $view =  \View::make('backend.paginas.reportes.servicios.reportepagoserviciocupon2-tablas', compact(['orden', 'totalorden', 'totaldescuento', 'totalenvio', 'nombre', 'f1', 'f2']))->render();
            $pdf = \App::make('dompdf.wrapper'); 
            $pdf->loadHTML($view)->setPaper('carta', 'portrait');    
            return $pdf->stream();
            
        }else if($cupon == 3){ // Descuento porcentaje
            
            $orden = DB::table('ordenes AS o')
            ->join('ordenes_cupones AS oc', 'oc.ordenes_id', '=', 'o.id')
            ->join('cupones AS c', 'c.id', '=', 'oc.cupones_id')
            ->select('o.id', 'o.fecha_orden', 'o.precio_total')
            ->where('o.servicios_id', $idservicio)
            ->where('o.estado_5', 1) // ordenes completadas
            ->where('o.estado_8', 0) // no canceladas, no afecta cuando se cancela por panel de control
            ->where('c.tipo_cupon_id', 3) // cupon descuento dinero
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get();

            $totalorden = 0; // total de la orden
            $totaldescontado = 0; // total de la orden, afectado por porcentaje
            $sumardescuento = 0;
            // conocer que tipo de cupon es
            foreach($orden as $o){
                $o->fecha_orden = date("d-m-Y", strtotime($o->fecha_orden));

                $porcentaje= AplicaCuponTres::where('ordenes_id', $o->id)->pluck('porcentaje')->first();
                $o->porcentaje = $porcentaje; // copia del envio a esa zona
                
                $resta = $o->precio_total * ($porcentaje / 100);                
                $o->descuento = number_format((float)$resta, 2, '.', '');
                $sumardescuento = $sumardescuento + $resta;
                $total = $o->precio_total - $resta;

                if($total <= 0){ 
                    $total = 0;
                }               
                
                $o->total = number_format((float)$total, 2, '.', '');// ya descontado el porcentaje
                $sumardescuento = number_format((float)$sumardescuento, 2, '.', '');
                $totalorden = $totalorden + $o->precio_total; // total orden   
                $totaldescontado = $totaldescontado + $total;                   
            }

            $totalorden = number_format((float)$totalorden, 2, '.', '');
            $totaldescontado = number_format((float)$totaldescontado, 2, '.', '');
            
            $nombre = Servicios::where('id', $idservicio)->pluck('nombre')->first();
    
            $view =  \View::make('backend.paginas.reportes.servicios.reportepagoserviciocupon3-tablas', compact(['orden', 'sumardescuento', 'totalorden', 'totaldescontado', 'nombre', 'f1', 'f2']))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('carta', 'portrait');    
            return $pdf->stream();
            
        }else if($cupon == 4){ // Producto Gratis
         
            $orden = DB::table('ordenes AS o')
            ->join('ordenes_cupones AS oc', 'oc.ordenes_id', '=', 'o.id')
            ->join('cupones AS c', 'c.id', '=', 'oc.cupones_id')
            ->select('o.id', 'o.fecha_orden', 'o.precio_total')
            ->where('o.servicios_id', $idservicio)
            ->where('o.estado_5', 1) // ordenes completadas
            ->where('o.estado_8', 0) // no canceladas, no afecta cuando se cancela por panel de control
            ->where('c.tipo_cupon_id', 4) // cupon descuento dinero
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get();

            $totalorden = 0; // total de la orden
            $totaldescontado = 0; // total de la orden, afectado por porcentaje
            // conocer que tipo de cupon es
            foreach($orden as $o){
                $o->fecha_orden = date("d-m-Y", strtotime($o->fecha_orden));

                $info = AplicaCuponCuatro::where('ordenes_id', $o->id)->first();
                $o->minimo = $info->dinero_carrito; // minimo a comprar para aplicar cupon
                $o->producto = $info->producto;               
            }
            
            $nombre = Servicios::where('id', $idservicio)->pluck('nombre')->first();
    
            $view =  \View::make('backend.paginas.reportes.servicios.reportepagoserviciocupon4-tablas', compact(['orden', 'nombre', 'f1', 'f2']))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('carta', 'portrait');    
            return $pdf->stream();  
        } else if($cupon == 5){ // donacion
         
            $orden = DB::table('ordenes AS o')
            ->join('ordenes_cupones AS oc', 'oc.ordenes_id', '=', 'o.id')
            ->join('cupones AS c', 'c.id', '=', 'oc.cupones_id')
            ->select('o.id', 'o.fecha_orden', 'o.precio_total', 'o.precio_envio')
            ->where('o.servicios_id', $idservicio)
            ->where('o.estado_5', 1) // ordenes completadas
            ->where('o.estado_8', 0) // no canceladas, no afecta cuando se cancela por panel de control
            ->where('c.tipo_cupon_id', 5) // cupon donacion
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get();

            $total = 0;
            $totalenvio = 0; 
            $totaldonacion = 0; 
          
            // conocer que tipo de cupon es
            foreach($orden as $o){
                $o->fecha_orden = date("d-m-Y", strtotime($o->fecha_orden));

                $info = AplicaCuponCinco::where('ordenes_id', $o->id)->first();
                $data = Instituciones::where('id', $info->instituciones_id)->first();

                $o->lugar = $data->nombre;
                $o->donacion = $info->dinero;
                
                $total = $total + $o->precio_total;
                $totalenvio = $totalenvio + $o->precio_envio; 
                $totaldonacion = $totaldonacion + $info->dinero;
            }

            $total = number_format((float)$total, 2, '.', '');
            $totalenvio = number_format((float)$totalenvio, 2, '.', '');
            $totaldonacion = number_format((float)$totaldonacion, 2, '.', '');
    
            $view =  \View::make('backend.paginas.reportes.servicios.reportepagoserviciocupon5-tablas', compact(['orden', 'total', 'totalenvio', 'totaldonacion', 'f1', 'f2']))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('carta', 'portrait');    
            return $pdf->stream();
        } 
        else if($cupon == 6){ // unicamente pagadas a propietarios
         
            $orden = DB::table('ordenes')
            ->select('id', 'precio_total', 'fecha_orden', 'pago_a_propi')
            ->where('servicios_id', $idservicio) // ordenes de este servicio
            ->where('estado_5', 1) // ordenes completadas
            ->where('estado_8', 0) // no canceladas
            ->where('pago_a_propi', 1) // solo que se pagaron a propietario
            ->whereBetween('fecha_orden', array($date1, $date2)) // unicamente esta fecha
            ->get(); 
         
            $totalDinero = 0;
            foreach($orden as $o){
                //sumar 
                $totalDinero = $totalDinero + $o->precio_total;
                $o->fecha_orden = date("d-m-Y", strtotime($o->fecha_orden));
            }

            $data = Servicios::where('id', $idservicio)->first();
            $nombre = $data->nombre; // nombre servicio
            $comision = $data->comision; // comision del servicio

            $totalDinero = number_format((float)$totalDinero, 2, '.', '');
            
            $suma = ($totalDinero * $comision) / 100; // dinero restado

            //$pagar = number_format((float)$pagar, 2, '.', '');
            $pagar = ($totalDinero - $suma); // restar dinero al total de todas las ordenes
            $pagar = number_format((float)$pagar, 2, '.', ''); // poner decimales 
            $suma = number_format((float)$suma, 2, '.', ''); // dinero que se restara al total de ordenes 
    
            $view =  \View::make('backend.paginas.reportes.servicios.reportepagoservicio-tablas', compact(['orden', 'comision', 'suma', 'totalDinero', 'nombre', 'pagar', 'f1', 'f2']))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('carta', 'portrait');     
            return $pdf->stream();
        } 
        else if($cupon == 7){ // unicamente no pagadas a propietarios
         
            $orden = DB::table('ordenes')
            ->select('id', 'precio_total', 'fecha_orden', 'pago_a_propi')
            ->where('servicios_id', $idservicio) // ordenes de este servicio
            ->where('estado_5', 1) // ordenes completadas
            ->where('estado_8', 0) // no canceladas
            ->where('pago_a_propi', 0) // solo no pagaron a propietario
            ->whereBetween('fecha_orden', array($date1, $date2)) // unicamente esta fecha
            ->get(); 
         
            $totalDinero = 0;
            foreach($orden as $o){
                //sumar 
                $totalDinero = $totalDinero + $o->precio_total;
                $o->fecha_orden = date("d-m-Y", strtotime($o->fecha_orden));
            }

            $data = Servicios::where('id', $idservicio)->first();
            $nombre = $data->nombre; // nombre servicio
            $comision = $data->comision; // comision del servicio

            $totalDinero = number_format((float)$totalDinero, 2, '.', '');
            
            $suma = ($totalDinero * $comision) / 100; // dinero restado

            //$pagar = number_format((float)$pagar, 2, '.', '');
            $pagar = ($totalDinero - $suma); // restar dinero al total de todas las ordenes
            $pagar = number_format((float)$pagar, 2, '.', ''); // poner decimales 
            $suma = number_format((float)$suma, 2, '.', ''); // dinero que se restara al total de ordenes 
    
            $view =  \View::make('backend.paginas.reportes.servicios.reportepagoservicio-tablas', compact(['orden', 'comision', 'suma', 'totalDinero', 'nombre', 'pagar', 'f1', 'f2']))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('carta', 'portrait');
            return $pdf->stream();
        } 
    }

    //** REPORTE DE TIPO CARGO DE ENVIO */

    // utilizo min de compra para envio gratis el servicio
    function reporteTipoCargoRevuelto($idservicio, $fecha1, $fecha2, $tipo){

        $date1 = Carbon::parse($fecha1)->format('Y-m-d');
        $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d');

        $f1 = Carbon::parse($fecha1)->format('d-m-Y');
        $f2 = Carbon::parse($fecha2)->format('d-m-Y');

        $orden;

        if($tipo == 0){ // revueltos
            $orden = DB::table('ordenes AS o')
            ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
            ->select('o.id AS idorden', 'o.precio_total', 'o.fecha_orden',
            'o.tipo_cargo', 'o.ganancia_motorista')
            ->where('s.id', $idservicio)
            ->where('o.estado_5', 1) // ordenes completadas
            ->where('o.estado_8', 0) // no canceladas
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get(); 
        }else if($tipo == 1){ // por precio zona servicio
            $orden = DB::table('ordenes AS o')
            ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
            ->select('o.id AS idorden', 'o.precio_total', 'o.fecha_orden',
            'o.tipo_cargo', 'o.ganancia_motorista')
            ->where('s.id', $idservicio)
            ->where('o.estado_5', 1) // ordenes completadas
            ->where('o.estado_8', 0) // no canceladas
            ->where('o.tipo_cargo', 1)
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get(); 
        }else if($tipo == 2){ // por mitad de precio
            $orden = DB::table('ordenes AS o')
            ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
            ->select('o.id AS idorden', 'o.precio_total', 'o.fecha_orden',
            'o.tipo_cargo', 'o.ganancia_motorista')
            ->where('s.id', $idservicio)
            ->where('o.estado_5', 1) // ordenes completadas
            ->where('o.estado_8', 0) // no canceladas
            ->where('o.tipo_cargo', 2)
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get();
        }else if($tipo ==3){ // por envio gratis en zona servicio
            $orden = DB::table('ordenes AS o')
            ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
            ->select('o.id AS idorden', 'o.precio_total', 'o.fecha_orden',
            'o.tipo_cargo', 'o.ganancia_motorista')
            ->where('s.id', $idservicio)
            ->where('o.estado_5', 1) // ordenes completadas
            ->where('o.estado_8', 0) // no canceladas
            ->where('o.tipo_cargo', 3)
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get();
        }else if($tipo == 4){ // por min de compra para envio gratis
            $orden = DB::table('ordenes AS o')
            ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
            ->select('o.id AS idorden', 'o.precio_total', 'o.fecha_orden',
            'o.tipo_cargo', 'o.ganancia_motorista')
            ->where('s.id', $idservicio)
            ->where('o.estado_5', 1) // ordenes completadas
            ->where('o.estado_8', 0) // no canceladas
            ->where('o.tipo_cargo', 4)
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get();
        }
            
        $dinero = 0; // sumatoria ganancia motorista
        $dinero2 = 0; // sumatoria precio zona copia
        foreach($orden as $o){
            
            $o->fecha_orden = date("d-m-Y", strtotime($o->fecha_orden)); 

            // obtener nombre de la zona por cada orden
            $data = DB::table('ordenes_direcciones AS o')
            ->join('zonas AS z', 'z.id', '=', 'o.zonas_id')   
            ->select('o.copia_envio', 'z.nombre', 'o.copia_min_gratis')                 
            ->where('o.ordenes_id', $o->idorden) // ordenes completadas 
            ->first(); 

            $nombre =  $data->nombre;
            
            if(strlen($nombre) > 15){

                $cortado = substr($nombre,0,15);
                $o->nombrezona = $cortado."...";
            }else{
                $o->nombrezona = $nombre;
            }

            
            $o->copia_envio = $data->copia_envio;
            
            $dinero = $dinero + $o->ganancia_motorista;
            $dinero2 = $dinero2 + $data->copia_envio;
        }

        $total = number_format((float)$dinero, 2, '.', '');
        $total2 = number_format((float)$dinero2, 2, '.', '');

        $data = Servicios::where('id', $idservicio)->first();
        $nombre = $data->nombre;
          
        $view =  \View::make('backend.paginas.reportes.tipocargo.reporteusoenviogratis', compact(['orden', 'total', 'total2', 'nombre', 'f1', 'f2']))->render();
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
        ->select('o.id', 'or.ordenes_id', 'or.revisador_id', 'o.precio_total',
        'o.precio_envio', 'r.nombre', 'r.identificador', 'o.fecha_orden',  
        'or.fecha', 'o.tipo_pago', 'o.estado_8', 'o.fecha_7', 'o.pago_a_propi',
        'o.fecha_5')
        ->where('or.revisador_id', $id)
        ->whereBetween('or.fecha', array($date1, $date2))
        ->get();

       
       
        $totalcobro = 0; 

        foreach($orden as $o){

            if($o->fecha_7 == null){
                $o->fecha_orden = "Sin completar aun";
            }else{
                $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_7));
            }

            // buscar si aplico cupon
            if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()){

                // buscar tipo de cupon
                $tipo = Cupones::where('id', $oc->cupones_id)->first();

                // ver que tipo se aplico
                // el precio envio ya esta modificado
                if($tipo->tipo_cupon_id == 1){
                  

                    if($o->pago_a_propi == 1){
                        // se paga a propietario
                       
                        // NO SUMAR SI PAGO CON CREDI PUNTOS
                        if($o->tipo_pago == 0){
                           // $totalcobro = $totalcobro + $o->precio_total; 
                           // MENOS LO QUE SE PAGARA A PROPIETARIO
                           // MAS LO QUE CANCELARA CLIENTE
                           // envio es siempre 0
                           $info = -$o->precio_total + $o->precio_total;
                           $totalcobro = $totalcobro + $info;
                        }else{
                            // credi puntos
                            $totalcobro = $totalcobro - $o->precio_total;
                        }
                       // $o->precio_total = number_format((float), 2, '.', '');  
                       $o->precio_total = number_format((float)$o->precio_total, 2, '.', '');
                    }else{
                        // no se paga a propietario
                        // no sumara precio envio, ya que esta seteado a $0.00 por cupon envio gratis
                        // NO SUMAR SI PAGO CON CREDI PUNTOS
                        if($o->tipo_pago == 0){
                            $totalcobro = $totalcobro + $o->precio_total;   
                        }
                        
                        $o->precio_total = number_format((float)$o->precio_total, 2, '.', '');
                    }
                    // 
                   
                }else if($tipo->tipo_cupon_id == 2){
                 
                    // modificar precio
                    $dd = AplicaCuponDos::where('ordenes_id', $o->id)->first();
                    $descuento = $dd->dinero;

                    $total = $o->precio_total - $descuento;
                    if($total <= 0){
                        $total = 0;
                    }

                    $aplicoenvio = 0; // para saver en la app si aplico tambien envio gratis

                    if($dd->aplico_envio_gratis == 0){                              
                       
                    }else{
                        // si aplico el envio gratis este cupon
                        $aplicoenvio = 1;
                    }

                    if($o->pago_a_propi == 1){
                       
                        if($o->tipo_pago == 0){ // efectivo
                            $info = -$o->precio_total + ($total + $o->precio_envio);
                            $totalcobro = $totalcobro + $info;
                                                       
                        }else{
                            // credi puntos
                        
                            $totalcobro = $totalcobro - $o->precio_total;
                        }

                        $afectado = $o->precio_envio + $total;
                        $o->precio_total = number_format((float)$afectado, 2, '.', '');

                    }else{ 
                        // no se le paga a propietario

                        // sumar el precio de envio
                        $suma = $total + $o->precio_envio;

                        // precio modificado con el descuento dinero
                        $o->precio_total = number_format((float)$suma, 2, '.', '');
                        // NO SUMAR SI PAGO CON CREDI PUNTOS
                        if($o->tipo_pago == 0){
                            $totalcobro = $totalcobro + $suma; 
                        }
                    }

                }else if($tipo->tipo_cupon_id == 3){
                    

                    $porcentaje = AplicaCuponTres::where('ordenes_id', $o->id)->pluck('porcentaje')->first();
                    $resta = $o->precio_total * ($porcentaje / 100);
                    $total = $o->precio_total - $resta;

                    if($total <= 0){
                        $total = 0;
                    }

                    if($o->pago_a_propi == 1){
                       
                        if($o->tipo_pago == 0){ // efectivo
                            $info = -$o->precio_total + ($total + $o->precio_envio);
                            $totalcobro = $totalcobro + $info;
                                                       
                        }else{
                            // credi puntos
                        
                            $totalcobro = $totalcobro - $o->precio_total;
                        }

                        $afectado = $o->precio_envio + $total;
                        $o->precio_total = number_format((float)$afectado, 2, '.', '');

                    }else{ 
                        // no se le paga a propietario

                        // sumar el precio de envio
                        $suma = $total + $o->precio_envio;

                        // precio modificado con el descuento dinero
                        $o->precio_total = number_format((float)$suma, 2, '.', '');
                        // NO SUMAR SI PAGO CON CREDI PUNTOS
                        if($o->tipo_pago == 0){
                            $totalcobro = $totalcobro + $suma; 
                        }
                    }

                }else if($tipo->tipo_cupon_id == 4){
                   
                    $producto = AplicaCuponCuatro::where('ordenes_id', $o->id)->pluck('producto')->first();
                    $o->producto = $producto;
                   
                   
                   
                    if($o->pago_a_propi == 1){
                   
                        if($o->tipo_pago == 0){
                            $info = -$o->precio_total + ($o->precio_total + $o->precio_envio);
                            $totalcobro = $totalcobro + $info;                                
                            $o->precio_total = number_format((float)$o->precio_total + $o->precio_envio, 2, '.', '');
                        }else{
                            // CREDI PUNTOS
                            $totalcobro = $totalcobro - $o->precio_total;

                            $o->precio_total = number_format((float)0, 2, '.', '');
                        }
                       
                    }else{
                        // no se le paga a servicio 

                        $cobro = $o->precio_total + $o->precio_envio;
                        $o->precio_total = number_format((float)$cobro, 2, '.', '');
                        // NO SUMAR SI PAGO CON CREDI PUNTOS
                        if($o->tipo_pago == 0){
                            $totalcobro = $totalcobro + $cobro; 
                        }
                    }


                }
                else if($tipo->tipo_cupon_id == 5){
                  
                    $donacion = AplicaCuponCinco::where('ordenes_id', $o->id)->pluck('dinero')->first();
                  
                    
                    $total = $donacion + $o->precio_total;
                    
                    if($o->pago_a_propi == 1){
                   
                        if($o->tipo_pago == 0){
                            $info = -$o->precio_total + ($total + $o->precio_envio);
                            $totalcobro = $totalcobro + $info;                                
                            $o->precio_total = number_format((float)$total + $o->precio_envio, 2, '.', '');
                        }else{
                            // CREDI PUNTOS
                            $totalcobro = $totalcobro - $o->precio_total;

                            $o->precio_total = number_format((float)0, 2, '.', '');
                        }
                       
                    }else{
                        // no se le paga a servicio 

                        $cobro = $total + $o->precio_envio;
                        $o->precio_total = number_format((float)$cobro, 2, '.', '');
                        
                        if($o->tipo_pago == 0){
                            $totalcobro = $totalcobro + $cobro; 
                        }
                    }                  
                }
             
            }else{              

                if($o->pago_a_propi == 1){
                   
                    if($o->tipo_pago == 0){
                        $info = -$o->precio_total + ($o->precio_total + $o->precio_envio);
                        $totalcobro = $totalcobro + $info;                                
                        $o->precio_total = number_format((float)$o->precio_total + $o->precio_envio, 2, '.', '');
                    }else{
                        // CREDI PUNTOS
                        $totalcobro = $totalcobro - $o->precio_total;

                        $o->precio_total = number_format((float)0, 2, '.', '');
                    }
                   
                }else{
                    // no se le paga a servicio 

                    $cobro = $o->precio_total + $o->precio_envio;
                    $o->precio_total = number_format((float)$cobro, 2, '.', '');
                    // NO SUMAR SI PAGO CON CREDI PUNTOS
                    if($o->tipo_pago == 0){
                        $totalcobro = $totalcobro + $cobro; 
                    }
                }
            }
        }
        
        // sumar ganancia de esta fecha
        $totalcobro = number_format((float)$totalcobro, 2, '.', '');

        return view('backend.paginas.revisador.tablas.tablaordenrevisada', compact('orden', 'totalcobro'));
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
        ->select('o.id', 'mo.motoristas_id', 'mo.ordenes_id', 'mo.fecha_agarrada',
         'o.estado_5', 'm.identificador', 'o.precio_total', 'o.precio_envio',
          'mo.fecha_agarrada', 'o.tipo_pago', 'o.pago_a_propi')
        ->where('mo.motoristas_id', $id)
        ->where('o.estado_5', 1) // orden preparada
        ->where('o.estado_8', 0) 
        ->whereNotIn('mo.ordenes_id', $pilaOrdenid)
        ->get(); 

        $sincompletar = 0;
        $totalcobro = 0; 

        foreach($ordenid as $o){            
            $sincompletar = $sincompletar + 1;

            // buscar si aplico cupon
            if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()){

                // buscar tipo de cupon
                $tipo = Cupones::where('id', $oc->cupones_id)->first();

                // ver que tipo se aplico
                // el precio envio ya esta modificado
                if($tipo->tipo_cupon_id == 1){
                    if($o->pago_a_propi == 1){
                        // se paga a propietario
                    
                        // NO SUMAR SI PAGO CON CREDI PUNTOS
                        if($o->tipo_pago == 0){
                        // $totalcobro = $totalcobro + $o->precio_total; 
                        // MENOS LO QUE SE PAGARA A PROPIETARIO
                        // MAS LO QUE CANCELARA CLIENTE
                        // envio es siempre 0
                        $info = -$o->precio_total + $o->precio_total;
                        $totalcobro = $totalcobro + $info;
                        }else{
                            // credi puntos
                            $totalcobro = $totalcobro - $o->precio_total;
                        }
                    // $o->precio_total = number_format((float), 2, '.', '');  
                    $o->precio_total = number_format((float)$o->precio_total, 2, '.', '');
                    }else{
                        // no se paga a propietario
                        // no sumara precio envio, ya que esta seteado a $0.00 por cupon envio gratis
                        // NO SUMAR SI PAGO CON CREDI PUNTOS
                        if($o->tipo_pago == 0){
                            $totalcobro = $totalcobro + $o->precio_total;   
                        }
                        
                        $o->precio_total = number_format((float)$o->precio_total, 2, '.', '');
                    }
                    // 
                
                }else if($tipo->tipo_cupon_id == 2){
                
                    // modificar precio
                    $dd = AplicaCuponDos::where('ordenes_id', $o->id)->first();
                    $descuento = $dd->dinero;

                    $total = $o->precio_total - $descuento;
                    if($total <= 0){
                        $total = 0;
                    }
                
                    if($o->pago_a_propi == 1){
                    
                        if($o->tipo_pago == 0){ // efectivo
                            $info = -$o->precio_total + ($total + $o->precio_envio);
                            $totalcobro = $totalcobro + $info;
                                                    
                        }else{
                            // credi puntos
                        
                            $totalcobro = $totalcobro - $o->precio_total;
                        }

                        $afectado = $o->precio_envio + $total;
                        $o->precio_total = number_format((float)$afectado, 2, '.', '');

                    }else{ 
                        // no se le paga a propietario

                        // sumar el precio de envio
                        $suma = $total + $o->precio_envio;

                        // precio modificado con el descuento dinero
                        $o->precio_total = number_format((float)$suma, 2, '.', '');
                        // NO SUMAR SI PAGO CON CREDI PUNTOS
                        if($o->tipo_pago == 0){
                            $totalcobro = $totalcobro + $suma; 
                        }
                    }

                }else if($tipo->tipo_cupon_id == 3){
                    

                    $porcentaje = AplicaCuponTres::where('ordenes_id', $o->id)->pluck('porcentaje')->first();
                    $resta = $o->precio_total * ($porcentaje / 100);
                    $total = $o->precio_total - $resta;

                    if($total <= 0){
                        $total = 0;
                    }

                    if($o->pago_a_propi == 1){
                    
                        if($o->tipo_pago == 0){ // efectivo
                            $info = -$o->precio_total + ($total + $o->precio_envio);
                            $totalcobro = $totalcobro + $info;
                                                    
                        }else{
                            // credi puntos
                        
                            $totalcobro = $totalcobro - $o->precio_total;
                        }

                        $afectado = $o->precio_envio + $total;
                        $o->precio_total = number_format((float)$afectado, 2, '.', '');

                    }else{ 
                        // no se le paga a propietario

                        // sumar el precio de envio
                        $suma = $total + $o->precio_envio;

                        // precio modificado con el descuento dinero
                        $o->precio_total = number_format((float)$suma, 2, '.', '');
                        // NO SUMAR SI PAGO CON CREDI PUNTOS
                        if($o->tipo_pago == 0){
                            $totalcobro = $totalcobro + $suma; 
                        }
                    }

                }else if($tipo->tipo_cupon_id == 4){
                
                    $producto = AplicaCuponCuatro::where('ordenes_id', $o->id)->pluck('producto')->first();
                    $o->producto = $producto;
                
                
                
                    if($o->pago_a_propi == 1){
                
                        if($o->tipo_pago == 0){
                            $info = -$o->precio_total + ($o->precio_total + $o->precio_envio);
                            $totalcobro = $totalcobro + $info;                                
                            $o->precio_total = number_format((float)$o->precio_total + $o->precio_envio, 2, '.', '');
                        }else{
                            // CREDI PUNTOS
                            $totalcobro = $totalcobro - $o->precio_total;

                            $o->precio_total = number_format((float)0, 2, '.', '');
                        }
                    
                    }else{
                        // no se le paga a servicio 

                        $cobro = $o->precio_total + $o->precio_envio;
                        $o->precio_total = number_format((float)$cobro, 2, '.', '');
                        // NO SUMAR SI PAGO CON CREDI PUNTOS
                        if($o->tipo_pago == 0){
                            $totalcobro = $totalcobro + $cobro; 
                        }
                    }


                }
                else if($tipo->tipo_cupon_id == 5){
                
                    $donacion = AplicaCuponCinco::where('ordenes_id', $o->id)->pluck('dinero')->first();
                
                    
                    $total = $donacion + $o->precio_total;
                    
                    if($o->pago_a_propi == 1){
                
                        if($o->tipo_pago == 0){
                            $info = -$o->precio_total + ($total + $o->precio_envio);
                            $totalcobro = $totalcobro + $info;                                
                            $o->precio_total = number_format((float)$total + $o->precio_envio, 2, '.', '');
                        }else{
                            // CREDI PUNTOS
                            $totalcobro = $totalcobro - $o->precio_total;

                            $o->precio_total = number_format((float)0, 2, '.', '');
                        }
                    
                    }else{
                        // no se le paga a servicio 

                        $cobro = $total + $o->precio_envio;
                        $o->precio_total = number_format((float)$cobro, 2, '.', '');
                        
                        if($o->tipo_pago == 0){
                            $totalcobro = $totalcobro + $cobro; 
                        }
                    }                  
                }
            
            }else{              

                if($o->pago_a_propi == 1){
                
                    if($o->tipo_pago == 0){
                        $info = -$o->precio_total + ($o->precio_total + $o->precio_envio);
                        $totalcobro = $totalcobro + $info;                                
                        $o->precio_total = number_format((float)$o->precio_total + $o->precio_envio, 2, '.', '');
                    }else{
                        // CREDI PUNTOS
                        $totalcobro = $totalcobro - $o->precio_total;

                        $o->precio_total = number_format((float)0, 2, '.', '');
                    }
                
                }else{
                    // no se le paga a servicio 

                    $cobro = $o->precio_total + $o->precio_envio;
                    $o->precio_total = number_format((float)$cobro, 2, '.', '');
                    // NO SUMAR SI PAGO CON CREDI PUNTOS
                    if($o->tipo_pago == 0){
                        $totalcobro = $totalcobro + $cobro; 
                    }
                }
            }
        }    
         
      
        // sumar ganancia de esta fecha
        $totalcobro = number_format((float)$totalcobro, 2, '.', '');   
  
        return view('backend.paginas.revisador.tablas.tablaordenrevisada2', compact('ordenid', 'sincompletar', 'totalcobro'));
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
        ->select('o.id', 'or.ordenes_id', 'or.revisador_id', 'o.precio_total', 
        'o.precio_envio', 'r.nombre', 'r.identificador', 'o.fecha_orden', 
         'or.fecha', 'o.tipo_pago', 'o.pago_a_propi')
        ->where('or.revisador_id', $id)
        ->whereBetween('or.fecha', array($date1, $date2))
        ->orderBy('or.fecha', 'ASC')
        ->get();
 
       
        $totalcobro = 0; 

        foreach($orden as $o){
            // buscar si aplico cupon
            if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()){

                // buscar tipo de cupon
                $tipo = Cupones::where('id', $oc->cupones_id)->first();

                // ver que tipo se aplico
                // el precio envio ya esta modificado
                if($tipo->tipo_cupon_id == 1){
                    if($o->pago_a_propi == 1){
                        // se paga a propietario
                       
                        // NO SUMAR SI PAGO CON CREDI PUNTOS
                        if($o->tipo_pago == 0){
                           // $totalcobro = $totalcobro + $o->precio_total; 
                           // MENOS LO QUE SE PAGARA A PROPIETARIO
                           // MAS LO QUE CANCELARA CLIENTE
                           // envio es siempre 0
                           $info = -$o->precio_total + $o->precio_total;
                           $totalcobro = $totalcobro + $info;
                        }else{
                            // credi puntos
                            $totalcobro = $totalcobro - $o->precio_total;
                        }
                       // $o->precio_total = number_format((float), 2, '.', '');  
                       $o->precio_total = number_format((float)$o->precio_total, 2, '.', '');
                    }else{
                        // no se paga a propietario
                        // no sumara precio envio, ya que esta seteado a $0.00 por cupon envio gratis
                        // NO SUMAR SI PAGO CON CREDI PUNTOS
                        if($o->tipo_pago == 0){
                            $totalcobro = $totalcobro + $o->precio_total;   
                        }
                        
                        $o->precio_total = number_format((float)$o->precio_total, 2, '.', '');
                    }
                    // 
                   
                }else if($tipo->tipo_cupon_id == 2){
                 
                    // modificar precio
                    $dd = AplicaCuponDos::where('ordenes_id', $o->id)->first();
                    $descuento = $dd->dinero;

                    $total = $o->precio_total - $descuento;
                    if($total <= 0){
                        $total = 0;
                    }
                  
                    if($o->pago_a_propi == 1){
                       
                        if($o->tipo_pago == 0){ // efectivo
                            $info = -$o->precio_total + ($total + $o->precio_envio);
                            $totalcobro = $totalcobro + $info;
                                                       
                        }else{
                            // credi puntos
                        
                            $totalcobro = $totalcobro - $o->precio_total;
                        }

                        $afectado = $o->precio_envio + $total;
                        $o->precio_total = number_format((float)$afectado, 2, '.', '');

                    }else{ 
                        // no se le paga a propietario

                        // sumar el precio de envio
                        $suma = $total + $o->precio_envio;

                        // precio modificado con el descuento dinero
                        $o->precio_total = number_format((float)$suma, 2, '.', '');
                        // NO SUMAR SI PAGO CON CREDI PUNTOS
                        if($o->tipo_pago == 0){
                            $totalcobro = $totalcobro + $suma; 
                        }
                    }

                }else if($tipo->tipo_cupon_id == 3){
                    

                    $porcentaje = AplicaCuponTres::where('ordenes_id', $o->id)->pluck('porcentaje')->first();
                    $resta = $o->precio_total * ($porcentaje / 100);
                    $total = $o->precio_total - $resta;

                    if($total <= 0){
                        $total = 0;
                    }

                    if($o->pago_a_propi == 1){
                       
                        if($o->tipo_pago == 0){ // efectivo
                            $info = -$o->precio_total + ($total + $o->precio_envio);
                            $totalcobro = $totalcobro + $info;
                                                       
                        }else{
                            // credi puntos
                        
                            $totalcobro = $totalcobro - $o->precio_total;
                        }

                        $afectado = $o->precio_envio + $total;
                        $o->precio_total = number_format((float)$afectado, 2, '.', '');

                    }else{ 
                        // no se le paga a propietario

                        // sumar el precio de envio
                        $suma = $total + $o->precio_envio;

                        // precio modificado con el descuento dinero
                        $o->precio_total = number_format((float)$suma, 2, '.', '');
                        // NO SUMAR SI PAGO CON CREDI PUNTOS
                        if($o->tipo_pago == 0){
                            $totalcobro = $totalcobro + $suma; 
                        }
                    }

                }else if($tipo->tipo_cupon_id == 4){
                   
                    $producto = AplicaCuponCuatro::where('ordenes_id', $o->id)->pluck('producto')->first();
                    $o->producto = $producto;
                   
                   
                   
                    if($o->pago_a_propi == 1){
                   
                        if($o->tipo_pago == 0){
                            $info = -$o->precio_total + ($o->precio_total + $o->precio_envio);
                            $totalcobro = $totalcobro + $info;                                
                            $o->precio_total = number_format((float)$o->precio_total + $o->precio_envio, 2, '.', '');
                        }else{
                            // CREDI PUNTOS
                            $totalcobro = $totalcobro - $o->precio_total;

                            $o->precio_total = number_format((float)0, 2, '.', '');
                        }
                       
                    }else{
                        // no se le paga a servicio 

                        $cobro = $o->precio_total + $o->precio_envio;
                        $o->precio_total = number_format((float)$cobro, 2, '.', '');
                        // NO SUMAR SI PAGO CON CREDI PUNTOS
                        if($o->tipo_pago == 0){
                            $totalcobro = $totalcobro + $cobro; 
                        }
                    }


                }
                else if($tipo->tipo_cupon_id == 5){
                  
                    $donacion = AplicaCuponCinco::where('ordenes_id', $o->id)->pluck('dinero')->first();
                  
                    
                    $total = $donacion + $o->precio_total;
                    
                    if($o->pago_a_propi == 1){
                   
                        if($o->tipo_pago == 0){
                            $info = -$o->precio_total + ($total + $o->precio_envio);
                            $totalcobro = $totalcobro + $info;                                
                            $o->precio_total = number_format((float)$total + $o->precio_envio, 2, '.', '');
                        }else{
                            // CREDI PUNTOS
                            $totalcobro = $totalcobro - $o->precio_total;

                            $o->precio_total = number_format((float)0, 2, '.', '');
                        }
                       
                    }else{
                        // no se le paga a servicio 

                        $cobro = $total + $o->precio_envio;
                        $o->precio_total = number_format((float)$cobro, 2, '.', '');
                        
                        if($o->tipo_pago == 0){
                            $totalcobro = $totalcobro + $cobro; 
                        }
                    }                  
                }
             
            }else{              

                if($o->pago_a_propi == 1){
                   
                    if($o->tipo_pago == 0){
                        $info = -$o->precio_total + ($o->precio_total + $o->precio_envio);
                        $totalcobro = $totalcobro + $info;                                
                        $o->precio_total = number_format((float)$o->precio_total + $o->precio_envio, 2, '.', '');
                    }else{
                        // CREDI PUNTOS
                        $totalcobro = $totalcobro - $o->precio_total;

                        $o->precio_total = number_format((float)0, 2, '.', '');
                    }
                   
                }else{
                    // no se le paga a servicio 

                    $cobro = $o->precio_total + $o->precio_envio;
                    $o->precio_total = number_format((float)$cobro, 2, '.', '');
                    // NO SUMAR SI PAGO CON CREDI PUNTOS
                    if($o->tipo_pago == 0){
                        $totalcobro = $totalcobro + $cobro; 
                    }
                }
            }
        }
        
        // sumar ganancia de esta fecha
        $totalcobro = number_format((float)$totalcobro, 2, '.', '');   
        

        $nombre = Revisador::where('id', $id)->pluck('nombre')->first();
        
        $view =  \View::make('backend.paginas.reportes.reporteordenrevisada', compact(['orden', 'conteo', 'totalcobro', 'nombre', 'f1', 'f2']))->render();
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

    // generar reporte de ordenes encargo completado por el servicio
    function reporteOrdenesEncargo($idservicio, $fecha1, $fecha2){

        $date1 = Carbon::parse($fecha1)->format('Y-m-d');
        $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d');

        $f1 = Carbon::parse($fecha1)->format('d-m-Y');
        $f2 = Carbon::parse($fecha2)->format('d-m-Y');

        // obtener lista de encargos asignado a este servicio
        $lista = EncargoAsignadoServicio::where('servicios_id', $idservicio)->get();

        $pila = array();
        foreach($lista as $p){
            array_push($pila, $p->encargos_id);
        }
 
        $orden = DB::table('ordenes_encargo')
        ->select('id', 'precio_subtotal', 'fecha_orden', 'fecha_1', 'pago_a_propi')
        ->where('estado_1', 1) // propietario completo la orden
        ->whereBetween('fecha_1', array($date1, $date2)) // unicamente esta fecha
        ->whereIn('encargos_id', $pila)
        ->get(); 
        
        $totalDinero = 0; 
        foreach($orden as $o){
            //sumar 
            $totalDinero = $totalDinero + $o->precio_subtotal;
            $o->fecha_1 = date("d-m-Y h:i A", strtotime($o->fecha_1));

            $pagado = "";
            if($o->pago_a_propi == 1){
                $pagado = "Si";
            }
            $o->pagado = $pagado;
        }

        $data = Servicios::where('id', $idservicio)->first();
        $nombre = $data->nombre; // nombre servicio
        $comision = $data->comision; // comision del servicio
        
        $suma = ($totalDinero * $comision) / 100; 
        $suma = number_format((float)$suma, 2, '.', ''); // redondear
     
        $pagar = ($totalDinero - $suma); 
        
        $totalDinero = number_format((float)$totalDinero, 2, '.', '');
        $pagar = number_format((float)$pagar, 2, '.', '');
 
        $view =  \View::make('backend.paginas.reportes.servicios.reporteordenesencargo', compact(['orden', 'comision', 'suma', 'totalDinero', 'nombre', 'pagar', 'f1', 'f2']))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('carta', 'portrait');      
        return $pdf->stream();
    }


     // filtro de ordenes encargo de motorista que aun no han pagado
     public function ordenesEncargoMotoristaSinEntregar($id){

        // sacar todos los id de ordenes revisadas
        $revisada = DB::table('ordenes_encargo_revisadas')
        ->get();

        $pilaOrdenid = array();
        foreach($revisada as $p){ 
            array_push($pilaOrdenid, $p->ordenes_encargo_id);
        } 
             
        $ordenid = DB::table('motorista_ordenes_encargo AS mo')
        ->join('ordenes_encargo AS o', 'o.id', '=', 'mo.ordenes_encargo_id')
        ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')
        ->select('mo.motoristas_id', 'mo.ordenes_encargo_id', 'mo.fecha_agarrada', 'm.identificador',
         'o.precio_subtotal', 'o.precio_envio', 'mo.fecha_agarrada', 'o.tipo_pago',
         'o.pago_a_propi')
        ->where('mo.motoristas_id', $id)
        ->where('o.estado_3', 1) // encargo completado
        ->where('o.revisado', '!=', 5) // no este en modo cancelado 
        ->whereNotIn('mo.ordenes_encargo_id', $pilaOrdenid)
        ->get(); 

        $totalcobro = 0;
        $conteo = 0;
        foreach($ordenid as $o){    
        $conteo = $conteo + 1;

            if($o->pago_a_propi == 1){
                   
                if($o->tipo_pago == 0){
                    $info = -$o->precio_subtotal + ($o->precio_subtotal + $o->precio_envio);
                    $totalcobro = $totalcobro + $info;                                
                    $o->precio_total = number_format((float)$info, 2, '.', '');
                }else{
                    // CREDI PUNTOS
                    $totalcobro = $totalcobro - $o->precio_subtotal;

                    $o->precio_total = number_format((float)0, 2, '.', '');
                }
               
            }else{
                // no se le paga a servicio 

                $cobro = $o->precio_subtotal + $o->precio_envio;
                $o->precio_total = number_format((float)$cobro, 2, '.', '');
               
                if($o->tipo_pago == 0){
                    $totalcobro = $totalcobro + $cobro; 
                } else{
                    $totalcobro = $totalcobro + 0; 
                }
            }
        }
          
        // sumar ganancia de esta fecha
        $totalcobro = number_format((float)$totalcobro, 2, '.', '');

        return view('backend.paginas.revisador.tablas.tablaencargosnopagadosmoto', compact('ordenid', 'conteo', 'totalcobro'));
    }


    // reporte de ordenes encargos revisados por el cobrador
    public function reporteOrdenRevisadaEncargo($id, $fecha1, $fecha2){

        $date1 = Carbon::parse($fecha1)->format('Y-m-d');
        $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d'); 

        $f1 = Carbon::parse($fecha1)->format('d-m-Y');
        $f2 = Carbon::parse($fecha2)->format('d-m-Y'); 

        $orden = DB::table('ordenes_encargo_revisadas AS or')
        ->join('ordenes_encargo AS o', 'o.id', '=', 'or.ordenes_encargo_id')
        ->join('revisador AS r', 'r.id', '=', 'or.revisador_id')
        ->select('or.ordenes_encargo_id', 'or.revisador_id', 'o.precio_subtotal',
        'o.precio_envio', 'r.nombre', 'r.identificador', 'o.fecha_orden',  
        'or.fecha', 'o.tipo_pago', 'o.pago_a_propi')
        ->where('or.revisador_id', $id)
        ->whereBetween('or.fecha', array($date1, $date2))
        ->orderBy('or.fecha', 'ASC')
        ->get();

        $totalcobro = 0;

        foreach($orden as $o){    
           

            if($o->pago_a_propi == 1){
                   
                if($o->tipo_pago == 0){
                    $info = -$o->precio_subtotal + ($o->precio_subtotal + $o->precio_envio);
                    $totalcobro = $totalcobro + $info;                                
                    $o->precio_total = number_format((float)$info, 2, '.', '');
                }else{
                    // CREDI PUNTOS
                    $totalcobro = $totalcobro - $o->precio_subtotal;

                    $o->precio_total = number_format((float)0, 2, '.', '');
                }
               
            }else{
                // no se le paga a servicio 

                $cobro = $o->precio_subtotal + $o->precio_envio;
                $o->precio_total = number_format((float)$cobro, 2, '.', '');
               
                if($o->tipo_pago == 0){
                    $totalcobro = $totalcobro + $cobro; 
                } else{
                    $totalcobro = $totalcobro + 0; 
                }
            }

        }
        
        // sumar ganancia de esta fecha
        $totalcobro = number_format((float)$totalcobro, 2, '.', '');
       

        $nombre = Revisador::where('id', $id)->pluck('nombre')->first(); 
        
        $view =  \View::make('backend.paginas.reportes.reporteordenencargorevisada', compact(['orden', 'conteo', 'caja', 'nombre', 'f1', 'f2', 'totalcobro']))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('carta', 'portrait');
   
        return $pdf->stream();
    }

 







}  
  