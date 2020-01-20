<?php

namespace App\Http\Controllers\Api;

use App\CarritoExtraModelo;
use App\CarritoTemporalModelo;
use App\Direccion;
use App\HorarioServicio;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Zonas;
use App\Producto;
use App\Servicios;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\User;
use Carbon\Carbon;

class CarritoTemporalController extends Controller
{
    // agregar productos al carrito de compras
    public function agregarProducto(Request $request){
        if($request->isMethod('post')){ 
            // validaciones para los datos
            $reglaDatos = array(                
                'userid' => 'required',                
                'productoid' => 'required',
                'mismoservicio' => 'required' // para preguntar si borra contenido anterior y crear nuevo carrito
            );    
                    
            $mensajeDatos = array(                                      
                'userid.required' => 'El id del usuario es requerido.',
                'productoid.required' => 'El id del producto es requerido.',
                'mismoservicio.required' => 'El ID del mismo servicio requerido.', 
                );
            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            } 
            
            if(!Direccion::where('user_id', $request->userid)->where('seleccionado', 1)->first())
            {
                return [
                    'success' => 6 // usuario sin direccion seleccionada
                ];
            }

            DB::beginTransaction();
        
            try {
                // sacar id del servicio por el producto
                $datos = DB::table('servicios AS s')
                ->join('servicios_tipo AS st', 'st.servicios_1_id', '=', 's.id')
                ->join('producto AS p', 'p.servicios_tipo_id', '=', 'st.id')            
                ->select('s.id AS idServicio', 'p.utiliza_cantidad')
                ->where('p.id', $request->productoid)
                ->first();
                $idservicio = $datos->idServicio; //id servcio
                $utilizaCantidad = $datos->utiliza_cantidad; // saver si utiliza cantidad este producto
                             
                // verificar si el usuario va a borrar la tabla de carrito de compras
                if($request->mismoservicio == 1){ // borrar tablas
                    $tabla1 = CarritoTemporalModelo::where('users_id', $request->userid)->first();
                    CarritoExtraModelo::where('carrito_temporal_id', $tabla1->id)->delete();
                    CarritoTemporalModelo::where('users_id', $request->userid)->delete();
                    DB::commit();
                }
                // preguntar si usuario ya tiene un carrito de compras
                if($cart = CarritoTemporalModelo::where('users_id', $request->userid)->first()){
                    
                        // ver limite de unidades del producto que quiere agregar y comparar si esta el mismo producto en carrito
                        // no esta agregando del mismo servicio
                        if($cart->servicios_id != $idservicio){

                            $nombreServicio = Servicios::where('id', $cart->servicios_id)->pluck('nombre')->first();

                            return [
                                'success' => 1, // no agregando del mismo servicio
                                'nombre' => $nombreServicio // nombre del servicio que tengho el carrito de compras
                            ];
                        }
                        if($utilizaCantidad == 1){
                            // total de unidades que tenemos en ese carrito del producto que queremos agregar
                            $producto = CarritoExtraModelo::where('carrito_temporal_id', $cart->id)
                            ->where('producto_id', $request->productoid)->get();

                            // sumar las cantidades que tenemos en el carrito
                            $total = collect($producto)->sum('cantidad');
                            
                            // unidades del producto normal
                            $unidades = Producto::where('id', $request->productoid)->first();
                            
                            // sumar unidades que vienen + las del carrito
                            $sum = $request->cantidad + $total;
                            
                            if($sum > $unidades->unidades){// cantidad en carrito supera el limite de producto disponible
                          
                                // saver si tenemos ese producto en carrito de compras
                                if(CarritoExtraModelo::where('carrito_temporal_id', $cart->id)->where('producto_id', $request->productoid)->first()){
                                    return [
                                        'success' => 2,
                                        'unidades' => $unidades->unidades // el producto si esta en el carrito, asi que hay unidades en el carrito
                                    ];
                                }else{
                                    return [
                                        'success' => 3, // no esta el producto en el carrito, la cantidad agregar supera a las unidades disponiblee
                                        'unidades' => $unidades->unidades
                                    ];
                                }
                                
                            }
                        }
                        
                        $notaProducto = $request->notaproducto;
                        if (empty($notaProducto)){
                            $notaProducto = "";
                        }
                        // si esta agregando del mismo servicio
                        $extra = new CarritoExtraModelo();
                        $extra->carrito_temporal_id = $cart->id;
                        $extra->producto_id = $request->productoid;
                        $extra->cantidad = $request->cantidad; // siempre sera 1 el minimo
                        $extra->nota_producto = $notaProducto;
                        $extra->save();
                        DB::commit();

                        return [ //producto guardado
                            'success' => 4
                        ];                                    
                }else{
                    // verificar si utiliza cantidad el servicio
                    if($utilizaCantidad == 1){
                        // verificar si no hay unidades disponible de ese producto
                        if(!Producto::where('id', $request->productoid)->where('unidades', '>=', $request->cantidad)->first()){
                            
                            $unidades = Producto::where('id', $request->productoid)->pluck('unidades')->first(); 
                            return [ 
                                'success' => 5, // sin cantidad disponible
                                'unidades' => $unidades // unidades disponible de ese producto
                            ];
                        }
                    }
                    // crear carrito nuevo

                    // obtener zona del usuario donde pide
                    $di = Direccion::where('user_id', $request->userid)->where('seleccionado', 1)->first();
                   
                    $carrito = new CarritoTemporalModelo();
                    $carrito->users_id = $request->userid;
                    $carrito->servicios_id = $idservicio;
                    $carrito->zonas_id = $di->zonas_id;
                    $carrito->save();
                    $notaProducto = $request->notaproducto;
                    if (empty($notaProducto)){
                        $notaProducto = "";
                    }

                    // guardar producto
                    $idcarrito = $carrito->id;
                    $extra = new CarritoExtraModelo();
                    $extra->carrito_temporal_id = $idcarrito;
                    $extra->producto_id = $request->productoid;
                    $extra->cantidad = $request->cantidad; // siempre sera 1 el minimo
                    $extra->nota_producto = $notaProducto;
                    $extra->save();
                    DB::commit();
                    
                    return [
                        'success' => 7 // producto agregado
                    ];
                }  
                       
            }catch(\Error $e){
                DB::rollback();

                return [
                    'success' => 6 //error
                ];
            }
        } 
    }

     // devuelve todos los productos del carrito
     public function verCarritoCompras(Request $request){
        if($request->isMethod('post')){ 
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

            if(User::where('id', $request->userid)->first()){

                // preguntar si NO tiene direccion el usuario           
                if(!Direccion::where('user_id', $request->userid)->where('seleccionado', 1)->first())
                {
                    return [
                        'success' => 1 // usuario sin direccion
                    ];
                }
            
                try {

                    $activo = 0; // saver si producto esta activo
                    $excedido = 0; // saver si ha excedido las unidades
                    $limitePromocion = 0; // saver si producto es promocion, y limite por orden
                    $limiteorden = 0; // verificar si tiene limite de ordenar un producto por pedido

                    // preguntar si usuario ya tiene un carrito de compras
                    if($cart = CarritoTemporalModelo::where('users_id', $request->userid)->first()){
                        $producto = DB::table('producto AS p')
                        ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')          
                        ->select('p.id AS productoID', 'p.nombre', 'c.cantidad', 
                        'p.unidades', 'p.imagen', 'p.precio', 'p.activo', 'p.disponibilidad', 
                        'c.id AS carritoid', 'p.es_promocion', 'p.limite_orden', 'p.cantidad_por_orden', 'p.utiliza_imagen')
                        ->where('c.carrito_temporal_id', $cart->id)
                        ->get();

                        $servicioidC = $cart->servicios_id; // id del servicio que esta en el carrito
                    
                        // verificar unidades de cada producto
                        foreach ($producto as $pro) {     
                        
                            // buscar si el producto ocupa cantidad
                            $uni = Producto::where('id', $pro->productoID)->first();
                            
                            // obtener todo el producto igual del carrito y sumar sus cantidades
                            $obtenido = CarritoExtraModelo::where('carrito_temporal_id', $cart->id)
                            ->where('producto_id', $pro->productoID)->get();
                            // sumar cantidades del carrito del mismo producto
                            $cantidadCarrito = collect($obtenido)->sum('cantidad');

                            // sacar si tiene limite por pedido
                            $limiteorden = $pro->limite_orden;
                            
                            // sumar todo el producto igual, y ver si excedio o no
                            if($uni->utiliza_cantidad){                            
                                
                                // preguntar si excedio la cantidades con las unidades del producto
                                if($cantidadCarrito > $pro->unidades){
                                    $pro->excedio = 1; // si exedio las unidades disponibles este producto
                                    $pro->suma = $cantidadCarrito; // unidades disponibles en el servicio
                                    $excedido = 1; // no dejara pasar a procesar orden ya que hay unidades exedidas
                                }else{
                                    $pro->excedio = 0; // ninguna unidad excedida
                                    $pro->suma = $cantidadCarrito; // unidades en carrito
                                }

                                // verificar si producto tiene limite producto cantidad por orden
                               // if($pro->es_promocion){
                                    if($pro->limite_orden){
                                        if($cantidadCarrito > $pro->cantidad_por_orden){
                                            // limite por orden excedida
                                            $pro->promocion = 1;
                                            $limitePromocion = 1;
                                        }else{
                                            $pro->promocion = 0;
                                        }
                                    }else{
                                        $pro->promocion = 0;
                                    }
                               /* }else{
                                    $pro->promocion = 0;
                                }*/

                            }else{
                                // no utiliza cantidad, asi que no esta excedido

                                $pro->excedio = 0;
                                // verificar si producto tiene limite promocion por orden
                                //if($pro->es_promocion){
                                    if($pro->limite_orden){
                                        if($cantidadCarrito > $pro->cantidad_por_orden){
                                            // limite por orden excedida
                                            $pro->promocion = 1;
                                            $limitePromocion = 1;
                                        }else{
                                            $pro->promocion = 0;
                                        }
                                    }else{
                                        $pro->promocion = 0;   
                                    }
                                /*}else{
                                    $pro->promocion = 0;
                                }*/
                            } 
                        
                            // saver si al menos un producto no esta activo o disponible
                            if($pro->activo == 0 || $pro->disponibilidad == 0){
                                $activo = 1; // producto no disponible
                            }
                            // multiplicar cantidad por el precio de cada producto
                            $precio = $pro->cantidad * $pro->precio;
                            $pro->precio = $precio;


                        } //end foreach

                    // sub total de la orden
                    $subTotal = collect($producto)->sum('precio'); // sumar todo el precio
                    $cc = collect($producto)->sum('cantidad'); // sumar todas las cantidades

                    // validacion de horarios para este servicio
           
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

                    $horarioLocal = 3; // para revisar el horario normal de hoy dia
                    $cerrado = 3; // saver si esta cerrado todo el dia hoy

                    // verificar si usara la segunda hora
                    $dato = DB::table('horario_servicio AS h')
                    ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                    ->where('h.dia', $diaSemana) // dia
                    ->where('h.servicios_id', $servicioidC) // id servicio que tengo en el carrito
                    ->where('h.segunda_hora', 1) // segunda hora habilitada
                    ->get();

                      // si verificar con la segunda hora
                        if(count($dato) >= 1){
                   
                            $horario = DB::table('horario_servicio AS h')
                            ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                            ->where('h.segunda_hora', 1) // segunda hora habilitada
                            ->where('h.servicios_id', $servicioidC) // id servicio
                            ->where('h.dia', $diaSemana) // dia    
                            ->where('h.hora1', '<=', $hora)
                            ->where('h.hora2', '>=', $hora)
                            ->orWhere('h.hora3', '<=', $hora)
                            ->where('h.hora4', '>=', $hora)
                            ->get();

                            if(count($horario) >= 1){ // abierto
                                $horarioLocal = 0;
                            }else{
                                $horarioLocal = 1; //cerrado
                            }

                        }else{
                        
                            // verificar sin la segunda hora
                            $horario = DB::table('horario_servicio AS h')
                            ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                            ->where('h.segunda_hora', 0) // segunda hora deshabilitada
                            ->where('h.servicios_id', $servicioidC) // id servicio
                            ->where('h.dia', $diaSemana)                                                     
                            ->where('h.hora1', '<=', $hora) 
                            ->where('h.hora2', '>=', $hora) 
                            ->get();

                            if(count($horario) >= 1){
                                $horarioLocal = 0; // abierto
                            }else{
                                $horarioLocal = 1; //cerrado
                            }
                        }  

                        // preguntar si este dia esta cerrado
                        $cerradoHoy = HorarioServicio::where('servicios_id', $servicioidC)->where('dia', $diaSemana)->first();
                      
                        if($cerradoHoy->cerrado == 1){
                            $cerrado = 1; // local cerrado este dia
                        }else{
                            $cerrado = 0;
                        }
                        
                        // sacar id de zona del carrito
                        $zon = DB::table('zonas AS z')->where('z.id', $cart->zonas_id)->first();
                       
                        $zonaSaturacion = $zon->saturacion; // saver si tenemos adomicilio completo a esta zona

                        // buscar el cerrado de emergencia
                        $emergencia = DB::table('servicios AS c')->where('c.id', $servicioidC)->first();

                        $cerradoEmergencia = 0;
                        $cerradoEmergencia = $emergencia->cerrado_emergencia; // cerrado emergencia este local
                        
                        // horario delivery para esa zona
                        $horaDelivery = DB::table('zonas AS z')
                        ->where('z.id', $servicioidC)
                        ->where('z.hora_abierto_delivery', '<=', $hora) 
                        ->where('z.hora_cerrado_delivery', '>=', $hora) 
                        ->get();
            
                        if(count($horaDelivery) >= 1){
                            $horaDelivery = 0; // abierto
                        }else{
                            $horaDelivery = 1; // cerrado
                        }   
            
                        return [
                            'success' => 2,
                            'producto' => $producto, //todos los productos
                            'subtotal' => number_format((float)$subTotal, 2, '.', ''), // subtotal
                            'excedido' => $excedido, // unidades excedidas
                            'activo' => $activo, // un producto no esta activo,
                            'promocion' => $limitePromocion, // saver si un producto supera promocion
                            'cantidad' => $cc, // cantidad total en carrito
                            'horario' => $horarioLocal, // horario normal por dia
                            'cerrado' => $cerrado, // si es 1, el local esta cerrado hoy
                            'cerrado_emergencia' => $cerradoEmergencia, //local cerrado por emergencia
                            'zona_saturacion' => $zonaSaturacion, // sin adomicilio para esta zona 
                        ];

                    }else{
                        return [
                            'success' => 3  // no tiene carrito de compras
                        ];
                    }
                }catch(\Error $e){
                    return [
                        'success' => 4, // error
                    ];
                }
            }
            else{
                return ['success' => 5]; // usuario no encontrado
            }
        }
    }

    // borrar producto individual
    public function eliminarProducto(Request $request){
        if($request->isMethod('post')){ 
            $reglaDatos = array(                
                'userid' => 'required',
                'carritoid' => 'required'
            );
                  
            $mensajeDatos = array(                              
                'userid.required' => 'El id del usuario es requerido',
                'carritoid.required' => 'El id del carrito es requerido'
                );
            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );
            if($validarDatos->fails())
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  
            // verificar si tenemos carrito
            if($ctm = CarritoTemporalModelo::where('users_id', $request->userid)->first()){
                
                // encontrar el producto a borrar
                if(CarritoExtraModelo::where('id', $request->carritoid)->first()){
                    CarritoExtraModelo::where('id', $request->carritoid)->delete();

                    // saver si tenemos mas productos aun
                    $dato = CarritoExtraModelo::where('carrito_temporal_id', $ctm->id)->get();

                    if(count($dato) == 0){
                        CarritoTemporalModelo::where('id', $ctm->id)->delete();

                        return ['success' => 1]; // carrito de compras borrado
                    }

                    return ['success' => 2]; // producto eliminado
                }else{
                    // producto a borrar no encontrado
                    return [
                        'success' => 3
                    ];
                }
            }else{              
                return [
                    'success' => 4   // sin carrito
                ];
            }
        }
    }

     // ver producto del carrito, y trae su cantidad elegida
     public function verProducto(Request $request){
        if($request->isMethod('post')){ 
            $reglaDatos = array(                
                'userid' => 'required',
                'carritoid' => 'required' //es id del producto
            );
                  
            $mensajeDatos = array(                              
                'userid.required' => 'El id del usuario es requerido',
                'carritoid.required' => 'El id del producto es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails())
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            // buscar si tiene carrito
            if(CarritoTemporalModelo::where('users_id', $request->userid)->first()){
                    
               
                if(CarritoExtraModelo::where('id', $request->carritoid)->first()){
                 
                    // informacion del producto + cantidad elegida
                    $producto = DB::table('producto AS p')
                    ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')
                    ->join('servicios_tipo AS s', 's.id', '=', 'p.servicios_tipo_id')
                    ->join('servicios AS ss', 'ss.id', '=', 's.servicios_1_id')
                    ->select('p.id AS productoID', 'p.nombre', 'p.descripcion', 'c.cantidad', 'c.nota_producto', // cantidad que tengo de ese producto nomas
                     'p.unidades', 'p.imagen', 'p.precio', 'p.utiliza_cantidad', 'p.utiliza_nota', 'p.nota', 'p.utiliza_imagen', 'ss.nombre AS nombreServicio')
                    ->where('c.id', $request->carritoid)
                    ->first();

                    return [
                        'success' => 1,
                        'producto' => $producto,
                    ];

                }else{
                    return [
                        'success' => 2 // producto no encontrado
                    ];
                }
            }else{
                return [
                    'success' => 3 // no tiene carrito
                ];
            }
        }
    }

     // eliminar carrito de compras
     public function eliminarCarritoCompras(Request $request){
        if($request->isMethod('post')){ 
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
            if($carrito = CarritoTemporalModelo::where('users_id', $request->userid)->first()){
                CarritoExtraModelo::where('carrito_temporal_id', $carrito->id)->delete();
                CarritoTemporalModelo::where('users_id', $request->userid)->delete();
                return [
                    'success' => 1 // carrito borrado
                ];
            }else{
                return [
                    'success' => 2 // el carrito esta vacio
                ];
            }
        } 
    }

     // cambiar cantidad del producto y verificar si hay unidades, solo si utiliza cantidad
     public function cambiarCantidad(Request $request){
        if($request->isMethod('post')){ 
           
            $reglaDatos = array(
                'userid' => 'required',
                'cantidad' => 'required',
                'carritoid' => 'required',
            );
            $mensajeDatos = array(
                'userid.required' => 'El id del usuario es requerido',
                'cantidad.required' => 'La cantidad es requerido',
                'carritoid.required' => 'id del carrito extra es requerido'
                );
            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );
            if($validarDatos->fails())
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            // buscar carrito de compras a quien pertenece el producto
            // verificar si existe el carrito
            if(CarritoTemporalModelo::where('users_id', $request->userid)->first()){
                // verificar si existe el carrito extra id que manda el usuario
                if(CarritoExtraModelo::where('id', $request->carritoid)->first()){

                   
                    
                    // preguntar si esta disponible o no el producto
                    $producto = DB::table('carrito_temporal AS c')
                    ->join('carrito_extra AS ce', 'ce.carrito_temporal_id', '=', 'c.id')
                    ->join('producto AS p', 'p.id', '=', 'ce.producto_id')
                    ->select('p.id','p.utiliza_cantidad', 'p.unidades', 'ce.cantidad', 'p.activo', 'p.disponibilidad')
                    ->where('ce.id', $request->carritoid)
                    ->first();

                    if($producto->disponibilidad == 0 || $producto->activo == 0){
                        return ['success' => 1]; // producto no disponible
                    }
                   
                    $nota = $request->nota;
                    if(empty($nota) || $nota == null){
                        $nota = "";
                    }

                    CarritoExtraModelo::where('id', $request->carritoid)->update(['cantidad' => $request->cantidad,
                    'nota_producto' => $nota]);

                    return [
                        'success' => 2 // cantidad actualizada
                    ];
                  
                }else{                    
                    return [
                        'success' => 3 //producto no encontrado
                    ];
                }
            }else{                
                return [
                    'success' => 4 // no hay carrito
                ];
            }     
        }    
    }

    // ver pantalla de procesar orden
    public function verProcesarOrden(Request $request){
        if($request->isMethod('post')){ 
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
               
            try {
                // preguntar si usuario ya tiene un carrito de compras
                if($cart = CarritoTemporalModelo::where('users_id', $request->userid)->first()){

                    // sacar id del servicio del carrito
                    $servicioidC = $cart->servicios_id;
                    $zonaiduser = 0;
                    // sacar id zona del usuario
                    if($user = Direccion::where('user_id', $request->userid)
                    ->where('seleccionado', 1)->first())
                    {
                        $zonaiduser = $user->zonas_id; // zona id donde esta el usuario
                    }  
                    
                    $envioPrecio = 0;
                   
                   // precio de la zona
                   // aqui no importa si esta activo o inactivo, solo obtendra el precio
                   // para ver el proceso debe existir en zonas_servicios
                    if($zz = DB::table('zonas_servicios AS z')
                    ->select('z.precio_envio')                       
                    ->where('z.zonas_id', $zonaiduser)
                    ->where('z.servicios_id', $servicioidC)
                    ->first()){                       
                        $envioPrecio = $zz->precio_envio;
                    }

                    $direccion = "";
                    // obtener direccion
                    if($di = Direccion::where('user_id', $request->userid)->where('seleccionado', 1)->first()){                        
                        $direccion = $di->direccion;
                    }else{
                        // no hay direccion
                        return [
                            'success' => 2
                        ];
                    }

                    // todo el producto del carrito de compras
                    $producto = DB::table('producto AS p')
                    ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')
                    ->select('p.precio', 'c.cantidad')
                    ->where('c.carrito_temporal_id', $cart->id)
                    ->get();

                    //$subTotal = 0;
                    $pila = array();

                    foreach($producto as $p){
                        $cantidad = $p->cantidad;
                        $precio = $p->precio;
                        $multi = $cantidad * $precio;
                        array_push($pila, $multi); 
                    }

                    $resultado=0;
                    foreach ($pila as $valor){
                        $resultado=$resultado+$valor;
                    }

                    $datosServicio = Servicios::where('id', $servicioidC)->first();
                    $gratis = $datosServicio->envio_gratis;
                    
                    $total = $resultado;
                    if($gratis == 0){
                        $total = $resultado + $envioPrecio;
                    }
                    $convertir = number_format((float)$total, 2, '.', '');
                    $t = (string)$convertir;
                    
                    return [
                        'success' => 1,
                        'total' => $t,
                        'subtotal' => number_format((float)$resultado, 2, '.', ''),
                        'envio' => $envioPrecio,
                        'direccion' => $direccion,
                        'gratis' => $gratis
                    ];
                    
                }else{
                    return [
                        'success' => 2  // no tiene carrito de compras      
                    ];
                }
            }catch(\Error $e){
                return [
                    'success' => 3,
                ];
            }
        }
    }

}
