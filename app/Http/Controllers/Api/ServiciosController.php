<?php

namespace App\Http\Controllers\Api;

use App\CarritoExtraModelo;
use App\CarritoTemporalModelo;
use App\Direccion;
use App\HorarioServicio;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Producto;
use App\Servicios;
use App\TipoServicios;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\User;
use Carbon\Carbon;

class ServiciosController extends Controller
{
   // buscar servicios (tienda, snack, restaurante) por id del usuario, obtiene zonas_id
    public function getServiciosZona(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(                
                'userid' => 'required',               
            );    
        
            $mensajeDatos = array(                                      
                'userid.required' => 'El id del usuario es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }    
            // obtener zona segun id del usuario
            $idzona = User::where('id', $request->userid)->pluck('zonas_id')->first();

            $tengoDireccion = 0;
            // sacar id zona del usuario
            if(Direccion::where('user_id', $request->userid)
            ->where('seleccionado', 1)->first())
            {                 
                $tengoDireccion = 1;           
            }
                
            $servicios = DB::table('tipo_servicios_zonas AS tz')
            ->join('tipo_servicios AS t', 't.id', '=', 'tz.tipo_servicios_id')
            ->select('t.id AS tipoServicioID', 't.nombre', 't.imagen')
            ->where('tz.zonas_id', $idzona)
            ->where('tz.activo', '1') //solo servicios disponibles
            ->orderBy('tz.posicion', 'ASC')
            ->get();
                                
            return [
                'success' => 1,                     
                'servicios' => $servicios,
                'haydireccion' => $tengoDireccion                  
            ];
        }
    }

    // retorna locales segun tipo servicio
    public function getTipoServicios(Request $request){
       
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'userid' => 'required',
                'tipo' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'userid.required' => 'El id del usuario es requerido.',
                'tipo.required' => 'El tipo servicio es requerido.',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            if(User::where('id', $request->userid)->first()){

                // obtener ciudad segun id del usuario
            $idzona = User::where('id', $request->userid)->pluck('zonas_id')->first();

            $nombreServicio = TipoServicios::where('id', $request->tipo)->pluck('nombre')->first();

                

            // dia        
            $numSemana = [
                0 => 1, // domingo
                1 => 2, // lunes
                2 => 3, // martes
                3 => 4, // miercoles
                4 => 5, // jueves
                5 => 6, // viernes
                6 => 7, // sabado
            ]; 

            // hora y fecha
            $getValores = Carbon::now('America/El_Salvador');
            $getDiaHora = $getValores->dayOfWeek;
            $diaSemana = $numSemana[$getDiaHora];
            $hora = $getValores->format('H:i:s');

            // servicios para la zona
            $servicios = DB::table('zonas_servicios AS z') 
            ->join('servicios AS s', 's.id', '=', 'z.servicios_id')
            ->select('s.id AS idServicio', 's.nombre AS nombreServicio',
                    's.descripcion', 's.imagen', 's.logo', 's.tipo_vista', 's.cerrado_emergencia')
            ->where('z.zonas_id', $idzona)
            ->where('s.tipo_servicios_id', $request->tipo) 
            ->where('s.activo', 1) 
            ->get();

          
               // verificar si esta agregado a favoritos
               foreach ($servicios as $user) {

                   // verificar si usara la segunda hora               
                    $dato = DB::table('horario_servicio AS h')
                    ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                    ->where('h.segunda_hora', 1) // segunda hora habilitada
                    ->where('h.servicios_id', $user->idServicio) // id servicio   1
                    ->where('h.dia', $diaSemana) // dia   2
                    ->get();

                      // si verificar con la segunda hora
                    if(count($dato) >= 1){
      
                       // return 'entro';


                       /* $horario = DB::table('horario_servicio AS h')
                        ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                        ->where('h.segunda_hora', '1') // segunda hora habilitada
                        ->where('h.servicios_id', $user->idServicio) // id servicio                        
                        ->where('h.dia', $diaSemana) // dia                        
                        ->where('h.hora1', '<=', $hora)
                        ->where('h.hora2', '>=', $hora)
                        ->orWhere('h.hora3', '<=', $hora)
                        ->where('h.hora4', '>=', $hora)
                        ->take(1)
                        ->get();*/

                        $horario = DB::table('horario_servicio AS h')
                        ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                        ->where('h.segunda_hora', '1') // segunda hora habilitada
                        ->where('h.servicios_id', $user->idServicio) // id servicio                        
                        ->where('h.dia', $diaSemana) // dia                        
                        ->whereTime('h.hora1', '<=', $hora)
                        ->whereTime('h.hora2', '>=', $hora)
                        ->orWhere('h.hora3', '<=', $hora)
                        ->where('h.hora4', '>=', $hora)
                        //->take(1)
                        ->get();

                        return [$horario];

                        if(count($horario) >= 1){ // abierto
                            $user->horarioLocal = 0;
                            
                            
                        }else{
                            $user->horarioLocal = 1; //cerrado

                          
                        }

                    }else{
                            // verificar sin la segunda hora
                            $horario = DB::table('horario_servicio AS h')
                            ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                            ->where('h.segunda_hora', 0) // segunda hora habilitada
                            ->where('h.servicios_id', $user->idServicio) // id servicio
                            ->where('h.dia', $diaSemana)                                                     
                            ->where('h.hora1', '<=', $hora) 
                            ->where('h.hora2', '>=', $hora) 
                            ->get();

                            if(count($horario) >= 1){
                                $user->horarioLocal = 0;
                            }else{
                                $user->horarioLocal = 1; //cerrado
                            }
                        }  

                     // preguntar si este dia esta cerrado
                    $cerradoHoy = HorarioServicio::where('servicios_id', $user->idServicio)->where('dia', $diaSemana)->first();                       
                      
                    if($cerradoHoy->cerrado == 1){
                        $user->cerrado = 1;
                    }else{
                        $user->cerrado = 0;
                    }                           
                }               
          



            // problema para enviar a esta zona, ejemplo motoristas sin disponibilidad
            $zonaSa = DB::table('zonas AS z')
            ->select('z.saturacion')
            ->where('z.id', $idzona)
            ->first();
            $zonaSaturacion = $zonaSa->saturacion;
                        
            // horario delivery para esa zona
            $horaDelivery = DB::table('zonas AS z')
            ->where('z.id', $idzona)
            ->where('z.hora_abierto_delivery', '<=', $hora)
            ->where('z.hora_cerrado_delivery', '>=', $hora)
            ->get();
            
            if(count($horaDelivery) >= 1){
                $horaDelivery = 0; // abierto
            }else{
                $horaDelivery = 1; // cerrado
            }
                        
            $tengoCarrito = 0; // para saver si tengo carrito de compras
            $resultado=0;
            // verificar si tengo algun carrito de compras con productos
            if($car = CarritoTemporalModelo::where('users_id', $request->userid)->first()){  
                // ver si tiene al menos 1 producto agregado
                if(CarritoExtraModelo::where('carrito_temporal_id', $car->id)->first()){
                    $tengoCarrito = 1;
                    $producto = DB::table('carrito_extra AS c')
                        ->join('producto AS p', 'p.id', '=', 'c.producto_id')
                        ->where('c.carrito_temporal_id', $car->id)
                        ->select('p.precio', 'c.cantidad')
                        ->get();

                        $pila = array();

                        foreach($producto as $p){
                            $cantidad = $p->cantidad;
                            $precio = $p->precio;
                            $multi = $cantidad * $precio;
                            array_push($pila, $multi); 
                        }
                       
                        foreach ($pila as $valor){
                            $resultado=$resultado+$valor; //sumar que sera el sub total
                        }
                }                
            }

            return [    
                'nombre' => $nombreServicio, // saver nombre del servicio, tienda, snack
                'success' => 1, 
                'zonasaturacion' => $zonaSaturacion,
                'horadelivery' => $horaDelivery,
                'hayorden' => $tengoCarrito, // saver si tenemos carrito
                'total' => number_format((float)$resultado, 2, '.', ''), //subtotal
                'servicios' => $servicios
            ];                  
               
            }else{
                return ['success' => 2];
            }
        }   
    }

    // devuelve los productos vista tipo comida
    public function getTodoProductoVistaComida(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(                
                'servicioid' => 'required',
            ); 
        
            $mensajeDatos = array(                                      
                'servicioid.required' => 'El id del servicio es requerido.',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }    

            if(Servicios::where('id', $request->servicioid)->first()){

             $tipo = DB::table('servicios_tipo AS st')    
            ->join('servicios AS s', 's.id', '=', 'st.servicios_1_id')
            ->select('st.id AS tipoId', 'st.nombre AS nombreSeccion')
            ->where('st.servicios_1_id', $request->servicioid)
            ->where('st.activo', 1)
            ->orderBy('st.posicion', 'ASC')
            ->get();

            $resultsBloque = array();        
            $index = 0;

            foreach($tipo as $secciones){
                array_push($resultsBloque,$secciones);          
            
                $subSecciones = DB::table('producto AS p')  
                ->select('p.id AS idProducto','p.nombre AS nombreProducto', 'p.descripcion AS descripcionProducto',
                         'p.imagen AS imagenProducto', 'p.precio AS precioProducto', 'p.es_promocion', 'p.utiliza_imagen')
                ->where('p.servicios_tipo_id', $secciones->tipoId)
                ->where('p.activo', 1) // para inactivarlo solo para administrador
                ->where('p.disponibilidad', 1) // para inactivarlo pero el propietario
                ->where('p.es_promocion', 0)
                ->orderBy('p.posicion', 'ASC')
                ->get(); 
                
                $resultsBloque[$index]->productos = $subSecciones; //agregar los productos en la sub seccion
                $index++;
            }

            $numSemana = [
                0 => 1, // domingo
                1 => 2, // lunes
                2 => 3, // martes
                3 => 4, // miercoles
                4 => 5, // jueves
                5 => 6, // viernes
                6 => 7, // sabado
            ];

            $getValores = Carbon::now('America/El_Salvador');
            $getDiaHora = $getValores->dayOfWeek;
            $diaSemana = $numSemana[$getDiaHora];   
                        
            $servicio = DB::table('servicios AS s')
            ->join('tiempo_aprox AS t', 't.servicios_id', '=', 's.id')
            ->select('s.nombre', 's.descripcion', 's.imagen', 't.tiempo', 's.minimo', 's.utiliza_minimo')
            ->where('s.id', $request->servicioid)
            ->where('t.dia', $diaSemana)
            ->get();
            
            //obtener horario
            $horario = DB::table('horario_servicio')            
            ->where('servicios_id', $request->servicioid)
            ->where('dia', $diaSemana)
            ->first(); 
            
            $hora1 = date("h:i A", strtotime($horario->hora1));
            $hora2 = date("h:i A", strtotime($horario->hora2));
            $hora3 = date("h:i A", strtotime($horario->hora3));
            $hora4 = date("h:i A", strtotime($horario->hora4));
            $segundaHora = $horario->segunda_hora; // si es 1, ocupa las 2 horas
            $cerrado = $horario->cerrado; // saver si hoy esta cerrado

            return [
                'success' => 1,
                'servicio' => $servicio,
                'horario' => ['hora1' => $hora1, 'hora2'=> $hora2, 'hora3' => $hora3, 'hora4' => $hora4, 'segunda' => $segundaHora, 'cerrado' => $cerrado],
                'productos' => $tipo
            ];
                
            }else{
                return ['success' => 2];
            }
        }
    }

    // informacion de producto individual
    public function getProductoIndividual(Request $request){
        if($request->isMethod('post')){ 
 
            // validaciones para los datos
            $reglaDatos = array(                
                'productoid' => 'required',
            );    
        
            $mensajeDatos = array(                                      
                'productoid.required' => 'El id del producto es requerido.',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if(Producto::where('id', $request->productoid)->first()){

            $producto = DB::table('servicios AS s')
            ->join('servicios_tipo AS st', 'st.servicios_1_id', '=', 's.id')
            ->join('producto AS p', 'p.servicios_tipo_id', '=', 'st.id')
            ->select('p.id', 'p.nombre', 'p.descripcion', 'p.precio', 
            'p.unidades', 'p.imagen', 'p.activo', 'p.disponibilidad', 'p.utiliza_imagen', 'p.utiliza_cantidad', 'p.utiliza_nota', 'p.nota')
            ->where('p.id', $request->productoid)
            ->get();
             
                return ['success' => 1, 'producto' => $producto];

            }else{
                return ['success' => 2];
            }
        }
    }

    // vista de promociones
    public function verPublicidad(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(                
                'userid' => 'required',
            );
        
            $mensajeDatos = array(                                      
                'userid.required' => 'El id del usuario es requerido.',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($usuario = User::where('id', $request->userid)->first()){

                $zonaid = $usuario->zonas_id;

                $zonaPublicidad = DB::table('zonas_publicidad AS zp')
                ->join('publicidad AS p', 'p.id', '=', 'zp.publicidad_id')
                ->select('zp.zonas_id', 'p.id AS publiid', 'p.imagen', 'p.logo', 'p.descripcion',
                'p.nombre', 'p.tipo_publicidad', 'p.activo',
                'p.url_facebook', 'p.utiliza_facebook', 'p.url_youtube', 'p.utiliza_youtube',
                'p.url_instagram', 'p.titulo', 'p.utiliza_titulo', 'p.utiliza_instagram', 'p.titulo_descripcion', 'p.utiliza_descripcion',
                'p.telefono', 'p.utiliza_telefono', 'utiliza_visitanos', 'p.visitanos', 'zp.posicion')
                ->where('zp.zonas_id', $zonaid)
                ->where('p.activo', 1)
                ->orderBy('zp.posicion', 'ASC')
                ->get();

                $resultsBloque = array();
                $index = 0;

                foreach($zonaPublicidad as $secciones){
                    array_push($resultsBloque,$secciones);
                
                        $subSecciones = DB::table('publicidad_producto AS p')
                        ->join('producto AS pro', 'pro.id', '=', 'p.producto_id')
                        ->select('p.publicidad_id', 'pro.nombre', 'pro.id AS productoid', 
                        'pro.descripcion', 'pro.imagen', 'pro.precio', 'pro.utiliza_imagen')
                        ->where('publicidad_id', $secciones->publiid)
                        ->get();
                        
                        $resultsBloque[$index]->productos = $subSecciones;
                        $index++;
                }

                return ['success' => 1, 'publicidad' => $zonaPublicidad];

            }else{
                return ['success' => 2];
            }
        }
    }

}
