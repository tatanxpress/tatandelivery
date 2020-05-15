<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Ordenes;
use App\OrdenesDirecciones;
use App\OrdenesDescripcion;
use App\Motoristas;
use App\OrdenesPendiente;
use App\OrdenesCupones;
use App\Cupones;
use App\AplicaCuponUno;
use App\AplicaCuponDos;
use App\AplicaCuponTres;
use App\AplicaCuponCuatro;
use App\MotoristaExperiencia;
use App\MotoristaOrdenes;
use App\AplicaCuponCinco;
use App\Instituciones;
use OneSignal;



use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrdenesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
   
    // lista de ordenes
    public function index(){

        return view('backend.paginas.ordenes.listaorden');
    }
 
    // tabla de lista de ordenes, ultimas 100
    public function tablaorden(){

        $orden = DB::table('ordenes AS o')
        ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
        ->select('o.id', 's.identificador', 'o.precio_total', 'o.fecha_orden', 'o.estado_7', 'o.estado_8')
        ->latest('o.id')
        ->take(100)       
        ->get(); 

        foreach($orden as $o){
            $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));;  

            $cupon = "";
            if(OrdenesCupones::where('ordenes_id', $o->id)->first()){
                $cupon = "Si";
            }
            $o->cupon = $cupon;
        }

        return view('backend.paginas.ordenes.tablas.tablaorden', compact('orden'));
    } 
   
    // ubicacion de orden entrega
    public function entregaUbicacion($id){

        $mapa = OrdenesDirecciones::where('ordenes_id', $id)->select('latitud', 'longitud')->first();

        $api = "AIzaSyB-Iz6I6GtO09PaXGSQxZCjIibU_Li7yOM";
        return view('backend.paginas.ordenes.mapaentrega', compact('mapa', 'api'));
    }

     // ubicacion de orden entrega
     public function listaproducto($id){

        return view('backend.paginas.ordenes.listaordenproducto', compact('id'));
    }
      
    // tabla de productos
    public function productos($id){
            
        $producto = DB::table('ordenes_descripcion AS od')
        ->join('ordenes AS o', 'o.id', '=', 'od.ordenes_id')
        ->join('producto AS p', 'p.id', '=', 'od.producto_id')
        ->select('od.id', 'p.id AS productoid', 'od.ordenes_id', 'p.nombre', 'p.descripcion', 'p.utiliza_imagen', 'p.imagen', 'od.cantidad', 'od.precio', 'od.nota') 
        ->where('o.id', $id) 
        ->get();  

        foreach($producto as $o){
                
            $cantidad = $o->cantidad;
            $precio = $o->precio;
            $multi = $cantidad * $precio;
            $o->multiplicado = number_format((float)$multi, 2, '.', '');  
            $o->preciounidad = number_format((float)$o->precio, 2, '.', '');  
        }
        
        return view('backend.paginas.ordenes.tablas.tablaordenproducto', compact('producto'));  
    }

    // ver motorista que agarraron la orden
    public function index2(){

        return view('backend.paginas.ordenes.listamotoorden');
    }

    // tabla de ordenes de un motorista
    public function tablamotoorden(){

        $orden = DB::table('motorista_ordenes AS mo')
        ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')
        ->select('mo.ordenes_id', 'm.identificador', 'm.nombre', 'mo.fecha_agarrada')
        ->latest('mo.ordenes_id')
        ->take(100)
        ->get(); 

        foreach($orden as $o){
            $o->fecha_agarrada = date("h:i A d-m-Y", strtotime($o->fecha_agarrada));
        }

        return view('backend.paginas.ordenes.tablas.tablamotoorden', compact('orden'));
    } 

    // ver calificaciones de los motoristas
    public function index3(){

        $motoristas = Motoristas::all();
        return view('backend.paginas.ordenes.listamotoexpe', compact('motoristas'));
    }

    // tabla de calificaciones de motorista
    public function tablamotoexpe(){

        $orden = DB::table('motorista_experiencia AS mo')
        ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')
        ->select('mo.ordenes_id', 'm.identificador', 'm.nombre', 'mo.experiencia', 'mo.mensaje', 'mo.fecha')
        ->latest('mo.ordenes_id')
        ->take(100)
        ->get();
 
        foreach($orden as $o){
            $o->fecha = date("h:i A d-m-Y", strtotime($o->fecha)); 
        }

        return view('backend.paginas.ordenes.tablas.tablamotoexpe', compact('orden'));
    } 


    // buscar motorista ordenes
    public function index4(){

        $moto = Motoristas::all();

        return view('backend.paginas.ordenes.listabuscarmotoorden', compact('moto'));
    }
    
    // calificacion global del motorista
    public function calificacionGlobal(Request $request){
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

            if(MotoristaExperiencia::where('motoristas_id', $request->id)->first()){

                $todo = MotoristaExperiencia::where('motoristas_id', $request->id)->get();
                $calificacion = 0;
                $contador = 0;
                foreach($todo as $t){
                    $calificacion = $calificacion + $t->experiencia;
                    $contador++;
                } 

                $total = $calificacion / $contador;
                $total = number_format((float)$total, 2, '.', '');
                return ['success' => 1, 'sumatoria' => $calificacion, 'contador' => $contador, 'calificacion' => $total];
 
            }else{
                return ['success' => 2];
            }
        }
    }


    // buscador de motorista ordenes agarradas por fecha
    public function buscador($id, $fecha1, $fecha2){
    
        if(Motoristas::where('id', $id)->first()){
 
            $date1 = Carbon::parse($fecha1)->format('Y-m-d');
            $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d');
            
            $orden = DB::table('motorista_ordenes AS mo')
            ->join('ordenes AS o', 'o.id', '=', 'mo.ordenes_id')
            ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')
            ->join('servicios AS s', 's.id', '=', 'o.servicios_id')           
            ->select('o.id AS idorden',
            'o.precio_envio', 'mo.motoristas_id', 'm.identificador', 'mo.fecha_agarrada', 
            'o.ganancia_motorista', 's.identificador AS identiservicio', 
            'o.estado_7') 
            ->where('mo.motoristas_id', $id)
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get(); 

            foreach($orden as $o){
                $o->fecha_agarrada = date("h:i A d-m-Y", strtotime($o->fecha_agarrada));               
            }
  
            return view('backend.paginas.ordenes.tablas.tablabuscarmotoorden', compact('orden'));
        }else{
            return ['success' => 2];
        }
    }

    // cancelar orden por panel de control
    public function cancelarOrdenPanel(Request $request){

        if($request->isMethod('post')){  

            $regla = array( 
                'id' => 'required', 
                'mensaje' => 'required'
            );
 
            $mensaje = array(
                'id.required' => 'id es requerido',  
                'mensaje.required' => 'mensaje es requerido'                  
            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }

            /* solo podra cancelar si 
                estado 5 == 1  // propietario completo orden
                estado 6 == 1 // motorista inicia entrega de la orden               
                estado 8 == 0 // aun no cancelada                
            */

            if($oo = Ordenes::where('id', $request->id)
            ->where('estado_5', 1)
            ->where('estado_6', 1)
            ->where('estado_8', 0)){

                // y tambien que tambien no haya sido cancelada extra ya
                if(OrdenesDirecciones::where('ordenes_id', $request->id)
                    ->where('cancelado_extra', 0)->first()){
                        
                        OrdenesDirecciones::where('ordenes_id', $request->id)
                        ->update(['cancelado_extra' => 1]); 
        
                        Ordenes::where('id', $request->id)
                        ->update(['mensaje_8' => $request->mensaje]);

                        // notificacion al cliente
                        $uus = User::where('id', $oo->users_id)->first();

                        $titulo = "Lamentamos mucho decirte";
                        $mensaje = "Tu pedido sufrio un percance, pronto nos comunicaremos contigo";

                        try {
                            $this->envioNoticacionCliente($titulo, $mensaje, $uus->device_id);
                        } catch (Exception $e) {
                            
                        }
        
                        return ['success' => 1];

                    }else{
                        // ya fue seteada
                        return ['success' => 2];
                    }

            }else{
                return ['success' => 3];
            }
        } 
    }

    // buscar por numero de orden
    public function buscarNumOrden($id){
            
        $orden = DB::table('ordenes AS o')
        ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
        ->select('o.id', 's.identificador', 's.nombre', 'o.fecha_orden')
        ->where('o.id', $id)
        ->get(); 

        foreach($orden as $o){
            $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));
        } 

        return view('backend.paginas.ordenes.tablas.tablaordenbuscador', compact('orden'));
    }
    

    //*** INFORMACION DE LAS ORDENES ********/

    // informacion del cliente
    public function informacioncliente(Request $request){
        if($request->isMethod('post')){  

            $regla = array( 
                'id' => 'required', 
            );
 
            $mensaje = array(
                'id.required' => 'id es requerido',                      
            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }
            
            if(Ordenes::where('id', $request->id)->first()){

                $orden = DB::table('ordenes AS o')
                ->join('ordenes_direcciones AS od', 'od.ordenes_id', '=', 'o.id')
                ->join('zonas AS z', 'z.id', '=', 'od.zonas_id')
                ->join('users AS u', 'u.id', '=', 'od.users_id')
                ->select('o.id', 'od.nombre', 'od.direccion', 'od.numero_casa', 'od.punto_referencia',
                'od.latitud', 'od.longitud', 'od.latitud_real', 'od.longitud_real',
                'od.telefono', 'od.copia_tiempo_orden', 'od.copia_envio',
                'u.phone', 'z.identificador', 'z.nombre AS nombrezona') 
                ->where('o.id', $request->id)
                ->get();
                      
                return ['success' => 1, 'orden' => $orden];           
            }else{
                return ['success' => 2];
            }
        }
    } 

    public function informacionorden(Request $request){
        if($request->isMethod('post')){  

            $regla = array( 
                'id' => 'required', 
            );
 
            $mensaje = array(
                'id.required' => 'id es requerido',                      
            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }
            
            if(Ordenes::where('id', $request->id)->first()){

                $orden = DB::table('ordenes AS o')
                ->join('ordenes_direcciones AS od', 'od.ordenes_id', '=', 'o.id')
                ->select('o.nota_orden', 'o.cambio', 'o.estado_2', 'o.fecha_2', 'o.hora_2',
                'od.copia_tiempo_orden', 'o.estado_3', 'o.fecha_3', 'o.estado_4', 'o.fecha_4',
                'o.estado_5', 'o.fecha_5','o.estado_6', 'o.fecha_6','o.estado_7', 'o.fecha_7',
                'o.estado_8', 'o.fecha_8', 'o.mensaje_8', 'o.cancelado_cliente', 'o.cancelado_propietario',
                'od.copia_envio', 'o.ganancia_motorista', 'od.cancelado_extra')
                ->where('o.id', $request->id)
                ->get();

                foreach($orden as $o){

                    $suma = $o->hora_2 + $o->copia_tiempo_orden;
                    $o->minutostotal = $suma;
                    
                    $canceladopor = ""; 
                    if($o->cancelado_cliente == 1){
                        $canceladopor = "Cliente";
                    }                
                    if($o->cancelado_propietario == 1){
                        $canceladopor = "Propietario";
                    }
                    $o->canceladopor = $canceladopor;

                    $tiempo = $o->copia_tiempo_orden + $o->hora_2;

                    $o->minutostotal = $tiempo;
        
                    $estimada = "";
                    if($o->estado_4 == 1){ 
                        // tomara el tiempo, desde cuando propietario inicia la orden
                        $timer = Carbon::parse($o->fecha_4);
                        // sumar minutos del propietario + tiempo extra de la zona
                        $horaEstimada = $timer->addMinute($tiempo)->format('h:i A d-m-Y');
                        $estimada = $horaEstimada; 
                    }
                    $o->estimada = $estimada;

                    // modificar fechas
                    if($o->estado_2 == 1){
                        $o->fecha_2 = date("h:i A", strtotime($o->fecha_2));
                    }
                    if($o->estado_3 == 1){
                        $o->fecha_3 = date("h:i A", strtotime($o->fecha_3));
                    }
                    if($o->estado_4 == 1){
                        $o->fecha_4 = date("h:i A", strtotime($o->fecha_4));
                    }
                    if($o->estado_5 == 1){
                        $o->fecha_5 = date("h:i A", strtotime($o->fecha_5));
                    }
                    if($o->estado_6 == 1){
                        $o->fecha_6 = date("h:i A", strtotime($o->fecha_6));
                    }
                    if($o->estado_7 == 1){
                        $o->fecha_7 = date("h:i A", strtotime($o->fecha_7));
                    }
                    if($o->estado_8 == 1){
                        $o->fecha_8 = date("h:i A", strtotime($o->fecha_8));
                    }
                }   

                return ['success' => 1, 'orden' => $orden];
            }
        }
    }

    public function informacioncargo(Request $request){
        if($request->isMethod('post')){  

            $regla = array( 
                'id' => 'required', 
            );
 
            $mensaje = array(
                'id.required' => 'id es requerido',                      
            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }
            
            if(Ordenes::where('id', $request->id)->first()){

                $orden = DB::table('ordenes AS o')
                ->join('ordenes_direcciones AS od', 'od.ordenes_id', '=', 'o.id')
                ->select('o.precio_total', 'o.precio_envio', 'o.tipo_cargo',
                'od.copia_min_gratis')
                ->where('o.id', $request->id)
                ->get();
                
                $aplico = "No";
                foreach($orden as $o){ 
                   
                    // verificar si ocupo cupon
                    if($oc = OrdenesCupones::where('ordenes_id', $request->id)->first()){
                        $aplico = "Si";
                        
                        $c = Cupones::where('id', $oc->cupones_id)->first();
                        // ver que tipo de cupon fue aplicado
                        if($c->tipo_cupon_id == 1){ // envio gratis
                            $info = AplicaCuponUno::where('ordenes_id', $request->id)->first();
                            $o->tipocupon = 1;
                            $o->dinerocarrito = $info->dinero;
                        }else if($c->tipo_cupon_id == 2){ // descuento dinero
                            $info = AplicaCuponDos::where('ordenes_id', $request->id)->first();
                            $o->tipocupon = 2;
                            $o->dinerocarrito = $info->dinero; // dinero que se esta descontando
                            $o->aplicoenvio = $info->aplico_envio_gratis;
                            
                            $descuento = $o->precio_total - $info->dinero;
                            if($descuento <= 0){
                                $descuento = 0;
                            }
                            $envio = $o->precio_envio;
                            if($info->aplico_envio_gratis == 1){
                                $envio = 0;
                            }

                            $suma = $descuento + $envio;
                            $o->pagara = number_format((float)$suma, 2, '.', '');
                        }else if($c->tipo_cupon_id == 3){ // descuento porcentaje
                            $info = AplicaCuponTres::where('ordenes_id', $request->id)->first();
                            $o->tipocupon = 3;
                            $o->dinerocarrito = $info->dinero;
                            $o->porcentaje = $info->porcentaje;

                            $resta = $o->precio_total * ($info->porcentaje / 100);
                            $total = $o->precio_total - $resta;

                            if($total <= 0){
                                $total = 0;
                            }

                            $suma = $total + $o->precio_envio;

                            $o->pagara = number_format((float)$suma, 2, '.', '');
                        }else if($c->tipo_cupon_id == 4){ // producto gratis
                            $info = AplicaCuponCuatro::where('ordenes_id', $request->id)->first();
                            $o->tipocupon = 4;
                            $o->dinerocarrito = $info->dinero_carrito;
                            $o->producto = $info->producto;
                        }
                        else if($c->tipo_cupon_id == 5){ // donacion
                            $info = AplicaCuponCinco::where('ordenes_id', $request->id)->first();
                            $nombre = Instituciones::where('id', $info->instituciones_id)->pluck('nombre')->first();
                            $o->tipocupon = 5;
                            $o->dinero = $info->dinero;
                            $o->institucion = $nombre;

                            $total = $o->precio_total + $o->precio_envio + $info->dinero;

                            $o->total = number_format((float)$total, 2, '.', '');
                        }
                        else{
                            $o->tipocupon = 0;
                        }

                    } 

                    $o->aplico = $aplico;
                }   

                return ['success' => 1, 'orden' => $orden];
            }
        }
    }

    public function informacionmotorista(Request $request){
        if($request->isMethod('post')){  

            $regla = array( 
                'id' => 'required', 
            );
 
            $mensaje = array(
                'id.required' => 'id es requerido',                      
            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }
            
            if(MotoristaOrdenes::where('ordenes_id', $request->id)->first()){

                $motorista = DB::table('motorista_ordenes AS mo')
                ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')
                ->select('m.nombre', 'm.identificador', 'mo.fecha_agarrada')
                ->where('mo.ordenes_id', $request->id)
                ->get();
                
                
                foreach($motorista as $o){                    
                    $o->fecha_agarrada = date("h:i A d-m-Y", strtotime($o->fecha_agarrada));
                }   

                return ['success' => 1, 'orden' => $motorista];
            }else{
                return ['success' => 2];
            }
        }
    }

    public function informaciontipocargo(Request $request){
        if($request->isMethod('post')){  

            $regla = array( 
                'id' => 'required', 
            );
 
            $mensaje = array(
                'id.required' => 'id es requerido',                      
            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }
            
            if($or = Ordenes::where('id', $request->id)->first()){

                $tipocargo = $or->tipo_cargo;

               
                $datos = DB::table('ordenes_direcciones')                
                ->where('ordenes_id', $request->id)
                ->first();

                // copia del cargo de envio a esa zona
                $cargozona = $datos->copia_envio;
                // copia minimo para envio gratis
                $mingratis = $datos->copia_min_gratis;

                $mitad = "";
                if($tipocargo == 2){
                    $division = $cargozona / 2;
                    $division = number_format((float)$division, 2, '.', '');
                    $mitad = $division;
                }
                
                return ['success' => 1, 'tipo' => $tipocargo, 'precio' => $cargozona, 'mitad' => $mitad, 'mingratis' => $mingratis];
            }
        }
    }

    // filtro para obtener algunos datos basicos del motorista
    public function filtro(Request $request){
        if($request->isMethod('post')){  

            $regla = array( 
                'id' => 'required',
                'fecha1' => 'required',
                'fecha2' => 'required' 
            );
 
            $mensaje = array(
                'id.required' => 'id es requerido',
                'fecha1.required' => 'fecha1 es requerido',
                'fecha2.required' => 'fecha2 es requerido',                
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

            $date1 = Carbon::parse($request->fecha1)->format('Y-m-d');
            $date2 = Carbon::parse($request->fecha2)->addDays(1)->format('Y-m-d');
            
            $orden = DB::table('motorista_ordenes AS mo')
            ->join('ordenes AS o', 'o.id', '=', 'mo.ordenes_id')
            ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')
            ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
            ->select('mo.motoristas_id', 'o.estado_7', 'o.estado_8', 'o.ganancia_motorista')
            ->where('mo.motoristas_id', $request->id)
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get(); 
 
            $totalagarradas=0;
            foreach ($orden as $valor){
                $totalagarradas = $totalagarradas + 1;
            }

            $totalcompletas=0;
            foreach ($orden as $valor){
                if($valor->estado_7 == 1){
                    $totalcompletas = $totalcompletas + 1;
                }
            }

            $totalcanceladas=0;
            foreach ($orden as $valor){
                if($valor->estado_8 == 1){
                    $totalcanceladas = $totalcanceladas + 1;
                }
            }

            $totalganancia=0;
            foreach ($orden as $valor){
                // no cancelado y si completada
                if($valor->estado_8 == 0 && $valor->estado_7 == 1){
                    $totalganancia = $totalganancia + $valor->ganancia_motorista;
                }
            }

            $total = number_format((float)$totalganancia, 2, '.', '');

            return ['success' => 1, 'totalagarradas' => $totalagarradas,
                    'totalcompletada' => $totalcompletas, 'totalcancelada' => $totalcanceladas, 
                    'totalganancia' => $total]; 
          }else{
            return ['success' => 2]; 
          }
        }
    }

    // reporte para pago del motorista, SOLO ORDENES NO CANCELADAS, Y ORDENES COMPLETADAS
    function reporte1($idmoto, $fecha1, $fecha2){

        $date1 = Carbon::parse($fecha1)->format('Y-m-d');
        $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d');

        $f1 = Carbon::parse($fecha1)->format('d-m-Y');
        $f2 = Carbon::parse($fecha2)->format('d-m-Y');

        $ordenFiltro = DB::table('motorista_ordenes AS mo')
        ->join('ordenes AS o', 'o.id', '=', 'mo.ordenes_id')
        ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')
        ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
        ->join('ordenes_direcciones AS od', 'od.ordenes_id', '=', 'o.id')
        ->select('o.id', 'o.fecha_orden', 'mo.motoristas_id', 'o.estado_8', 'o.estado_7',
                    'o.ganancia_motorista')
        ->where('mo.motoristas_id', $idmoto)
        ->where('o.estado_8', 0)
        ->where('o.estado_7', 1)
        ->whereBetween('o.fecha_orden', array($date1, $date2))          
        ->get();

        $dinero = 0;
        foreach($ordenFiltro as $o){

            //sumar
            $dinero = $dinero + $o->ganancia_motorista;

    
            $o->fecha_orden = date("d-m-Y h:i A", strtotime($o->fecha_orden));
        } 

        $nombre = Motoristas::where('id', $idmoto)->pluck('nombre')->first();
        
        $totalDinero = number_format((float)$dinero, 2, '.', '');

        $view =  \View::make('backend.paginas.reportes.pagoServicioMotorista', compact(['ordenFiltro', 'totalDinero', 'nombre', 'f1', 'f2']))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('carta', 'portrait');
 
        return $pdf->stream();
    }

    // ver ordenes pendiente de motorista
    public function index5(){

        return view('backend.paginas.ordenes.listapendienteorden');
    }
 
    // tabla de ordenes que fueron ingresadas cuando el servicio completo estado 5,
    // y no habia motorista asignado
    public function tablaordenpendiente(){

        $orden = DB::table('ordenes_pendiente AS op')
        ->join('ordenes AS o', 'o.id', '=', 'op.ordenes_id')
        ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
        ->select('op.ordenes_id', 's.identificador', 's.nombre',
         'o.precio_total', 'o.precio_envio', 'o.fecha_orden', 'op.fecha', 'o.fecha_4',
        'o.hora_2')
        ->where('op.activo', 1)
        ->get(); 
  
        foreach($orden as $o){
            $fechaOrden = $o->fecha_orden;
            $hora1 = date("h:i A", strtotime($fechaOrden));
            $fecha1 = date("d-m-Y", strtotime($fechaOrden));
            $o->fecha_orden = $hora1 . " " . $fecha1;  

            $fecha = $o->fecha;
            $hora2 = date("h:i A", strtotime($fecha));
            $fecha2 = date("d-m-Y", strtotime($fecha));
            $o->fecha = $hora2 . " " . $fecha2; 
              
            $suma = $o->precio_total + $o->precio_envio;
            $o->total = number_format((float)$suma, 2, '.', '');

            $time1 = Carbon::parse($o->fecha_4);
            $horaEstimada = $time1->addMinute($o->hora_2)->format('h:i A d-m-Y');
            $o->horaEstimada = $horaEstimada; 
        } 
 
        return view('backend.paginas.ordenes.tablas.tablapendienteorden', compact('orden'));
    } 

    // vista para buscar un # orden
    public function index6(){

        return view('backend.paginas.ordenes.listavistanumeroorden');
    }

    // ocultar una orden pendiente de motorista
    function ocultarordenpendiente(Request $request){
        if($request->isMethod('post')){  

            $regla = array(  
                'id' => 'required', 
            );
 
            $mensaje = array(
                'id.required' => 'id es requerido',                      
            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }
            
          if(OrdenesPendiente::where('id', $request->id)->first()){

            OrdenesPendiente::where('id', $request->id)->update([
                'activo' => 0
                ]);  

            return ['success' => 1]; 
          }else{
            return ['success' => 2];
          }
        }
    }


    
    public function envioNoticacionCliente($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionCliente($titulo, $mensaje, $pilaUsuarios);
    }

}
  