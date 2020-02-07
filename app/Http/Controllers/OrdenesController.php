<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Ordenes;
use App\OrdenesDirecciones;
use App\OrdenesDescripcion;
use App\Motoristas;
use App\OrdenesPendiente;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrdenesController extends Controller
{
    /*public function __construct()
    {
        $this->middleware('auth:admin');
    }*/
   
    // lista de ordenes
    public function index(){

        return view('backend.paginas.ordenes.listaorden');
    }
 
    // tabla de lista de ordenes
    public function tablaorden(){

        $orden = DB::table('ordenes AS o')
        ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
        ->select('o.id', 's.identificador', 'o.precio_total', 'o.fecha_orden')
        ->get(); 

        foreach($orden as $o){
            $fechaOrden = $o->fecha_orden;
            $hora1 = date("h:i A", strtotime($fechaOrden));
            $fecha1 = date("d-m-Y", strtotime($fechaOrden));
            $o->fecha_orden = $hora1 . " " . $fecha1;  
        }

        return view('backend.paginas.ordenes.tablas.tablaorden', compact('orden'));
    } 

    // informacion de esa orden
    public function informacion(Request $request){
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
            ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
            ->join('ordenes_direcciones AS od', 'od.ordenes_id', '=', 'o.id')
            ->join('zonas AS z', 'z.id', '=', 'od.zonas_id')
            ->select('o.id', 's.nombre AS nombreServicio', 'od.nombre AS nombreCliente',
            'z.identificador', 'od.direccion', 'od.numero_casa', 'od.punto_referencia',
            'od.telefono', 'o.nota_orden', 'o.precio_total', 'o.precio_envio',
             'o.fecha_orden', 'o.cambio', 'o.estado_2', 'o.fecha_2', 'o.hora_2',
             'o.estado_3', 'o.fecha_3', 'o.estado_4', 'o.fecha_4', 'o.estado_5', 'o.fecha_5',
             'o.estado_6', 'o.fecha_6', 'o.estado_7', 'o.fecha_7', 'o.estado_8', 
             'o.fecha_8', 'o.mensaje_8', 'o.visible', 'o.visible_p', 'o.visible_p2',
             'o.visible_p3', 'o.cancelado_cliente', 'o.cancelado_propietario',
             'o.envio_gratis', 'o.visible_m', 'o.ganancia_motorista') 
            ->where('o.id', $request->id)
            ->first(); 

            $horaestimada = "";
                if($orden->estado_4 == 1){
                    $time1 = Carbon::parse($orden->fecha_4);
                    $horaestimada = $time1->addMinute($orden->hora_2)->format('h:i A d-m-Y'); 
                }                
            

            return ['success' => 1, 'orden' => $orden, 'horaestimada' => $horaestimada]; 
          }else{
            return ['success' => 2];
          }
        }
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
        ->get(); 

        foreach($orden as $o){
            $fechaOrden = $o->fecha_agarrada;
            $hora1 = date("h:i A", strtotime($fechaOrden));
            $fecha1 = date("d-m-Y", strtotime($fechaOrden));
            $o->fecha_agarrada = $hora1 . " " . $fecha1;  
        }

        return view('backend.paginas.ordenes.tablas.tablamotoorden', compact('orden'));
    } 

    // ver calificaciones de los motoristas
    public function index3(){

        return view('backend.paginas.ordenes.listamotoexpe');
    }

    // tabla de calificaciones de motorista
    public function tablamotoexpe(){

        $orden = DB::table('motorista_experiencia AS mo')
        ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')
        ->select('mo.ordenes_id', 'm.identificador', 'm.nombre', 'mo.experiencia', 'mo.mensaje', 'mo.fecha')
        ->get();

        foreach($orden as $o){
            $fechaOrden = $o->fecha;
            $hora1 = date("h:i A", strtotime($fechaOrden));
            $fecha1 = date("d-m-Y", strtotime($fechaOrden));
            $o->fecha = $hora1 . " " . $fecha1;  
        }

        return view('backend.paginas.ordenes.tablas.tablamotoexpe', compact('orden'));
    } 


    // buscar motorista ordenes
    public function index4(){

        $moto = Motoristas::all();

        return view('backend.paginas.ordenes.listabuscarmotoorden', compact('moto'));
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
            ->join('motorista_ordenes AS moo', 'moo.ordenes_id', '=', 'o.id')
            ->select('o.id AS idorden', 'o.envio_gratis',
            'o.precio_envio', 'mo.motoristas_id', 'm.identificador', 'o.fecha_orden', 
            'o.ganancia_motorista', 's.identificador AS identiservicio', 
            'o.estado_7')
            ->where('mo.motoristas_id', $id)
            ->whereBetween('o.fecha_orden', array($date1, $date2))          
            ->get(); 

            foreach($orden as $o){
                $fechaOrden = $o->fecha_orden;
                $hora1 = date("h:i A", strtotime($fechaOrden));
                $fecha1 = date("d-m-Y", strtotime($fechaOrden));
                $o->fecha_orden = $fecha1 . " " . $hora1;  
            }           
 
            return view('backend.paginas.ordenes.tablas.tablabuscarmotoorden', compact('orden'));
        }else{
            return ['success' => 2];
        }
    }


    // informacion de orden buscada
    public function infoordenbuscada(Request $request){
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
            ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
            ->join('ordenes_direcciones AS od', 'od.ordenes_id', '=', 'o.id')
            ->join('zonas AS z', 'z.id', '=', 'od.zonas_id')
            ->select('o.id', 's.nombre AS nombreServicio', 'od.nombre AS nombreCliente',
            'z.identificador', 'od.direccion', 'od.numero_casa', 'od.punto_referencia',
            'od.telefono', 'o.nota_orden', 'o.precio_total', 'o.precio_envio',
             'o.fecha_orden', 'o.cambio', 'o.estado_2', 'o.fecha_2', 'o.hora_2',
             'o.estado_3', 'o.fecha_3', 'o.estado_4', 'o.fecha_4', 'o.estado_5', 'o.fecha_5',
             'o.estado_6', 'o.fecha_6', 'o.estado_7', 'o.fecha_7', 'o.estado_8', 
             'o.fecha_8', 'o.mensaje_8', 'o.visible', 'o.visible_p', 'o.visible_p2',
             'o.visible_p3', 'o.cancelado_cliente', 'o.cancelado_propietario',
             'o.envio_gratis', 'o.visible_m', 'o.ganancia_motorista') 
            ->where('o.id', $request->id)
            ->first(); 

            return ['success' => 1, 'orden' => $orden]; 
          }else{
            return ['success' => 2];
          }
        }
    }

    // filtro para obtener algunos datos basicos
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
            ->join('motorista_ordenes AS moo', 'moo.ordenes_id', '=', 'o.id')
            ->select('mo.motoristas_id', 'o.estado_7', 'o.estado_8', 'o.envio_gratis', 'o.ganancia_motorista')
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

            
            $totalmarcagratis=0;
            foreach ($orden as $valor){
                if($valor->envio_gratis == 1){
                    $totalmarcagratis = $totalmarcagratis + 1;
                }
            }

            $totalganancia=0;
            foreach ($orden as $valor){
                // no cancelado y si completada
                if($valor->estado_8 == 0 && $valor->estado_7 == 1){
                    $totalganancia = $totalganancia + $valor->ganancia_motorista;
                }
            }

            return ['success' => 1, 'totalagarradas' => $totalagarradas,
                    'totalcompletada' => $totalcompletas, 'totalcancelada' => $totalcanceladas, 
                    'totalmarcagratis' => $totalmarcagratis, 'totalganancia' => $totalganancia]; 
          }else{
            return ['success' => 2]; 
          }
        }
    }

    // reporte de servicio para cobrar por motorista prestado
    function reporte($idmoto, $idservicio, $fecha1, $fecha2){

        $date1 = Carbon::parse($fecha1)->format('Y-m-d');
        $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d');

        $ordenFiltro = DB::table('motorista_ordenes AS mo')
        ->join('ordenes AS o', 'o.id', '=', 'mo.ordenes_id')
        ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')
        ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
        ->join('motorista_ordenes AS moo', 'moo.ordenes_id', '=', 'o.id')
        ->join('ordenes_direcciones AS od', 'od.ordenes_id', '=', 'o.id')
        ->select('o.id', 'o.precio_total', 'o.fecha_orden', 's.id AS idservicio', 'mo.motoristas_id',
                'od.zonas_id AS idzona')
        ->where('mo.motoristas_id', $idmoto)      
        ->whereBetween('o.fecha_orden', array($date1, $date2))          
        ->get();

        $dinero = 0;
        foreach($ordenFiltro as $o){

            $dato = DB::table('zonas_servicios')
            ->where('zonas_id', $o->idzona)
            ->where('servicios_id', $o->idservicio)
            ->first();

            $o->precio_envio = $dato->precio_envio;

            //sumar
            $dinero = $dinero + $dato->precio_envio;

            $fechaOrden = $o->fecha_orden;
            $hora1 = date("h:i A", strtotime($fechaOrden));
            $fecha1 = date("d-m-Y", strtotime($fechaOrden));
            $o->fecha_orden = $fecha1 . " " . $hora1;  
        }
        
        $totalDinero = number_format((float)$dinero, 2, '.', '');

        $view =  \View::make('backend.paginas.reportes.cobroServicioMotoPrestado', compact(['ordenFiltro', 'totalDinero']))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('carta', 'portrait');

        return $pdf->stream();
    }

    // reporte para pago del motorista, SOLO ORDENES NO CANCELADAS, Y ORDENES COMPLETADAS
    function reporte1($idmoto, $fecha1, $fecha2){

        $date1 = Carbon::parse($fecha1)->format('Y-m-d');
        $date2 = Carbon::parse($fecha2)->addDays(1)->format('Y-m-d');

        $ordenFiltro = DB::table('motorista_ordenes AS mo')
        ->join('ordenes AS o', 'o.id', '=', 'mo.ordenes_id')
        ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')
        ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
        ->join('motorista_ordenes AS moo', 'moo.ordenes_id', '=', 'o.id')
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

            $fechaOrden = $o->fecha_orden;
            $hora1 = date("h:i A", strtotime($fechaOrden));
            $fecha1 = date("d-m-Y", strtotime($fechaOrden));
            $o->fecha_orden = $fecha1 . " " . $hora1;  
        }

        $nombre = Motoristas::where('id', $idmoto)->pluck('nombre')->first();
        
        $totalDinero = number_format((float)$dinero, 2, '.', '');

        $view =  \View::make('backend.paginas.reportes.pagoServicioMotorista', compact(['ordenFiltro', 'totalDinero', 'nombre']))->render();
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
}
  