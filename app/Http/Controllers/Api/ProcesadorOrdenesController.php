<?php

namespace App\Http\Controllers\Api;

use App\CarritoExtraModelo;
use App\CarritoTemporalModelo;
use App\Direccion;
use App\HorarioServicio;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MotoristaOrdenes; 
use App\OrdenesDirecciones;
use App\Ordenes;
use App\OrdenesDescripcion;
use App\Producto;
use App\Servicios; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\User;
use Carbon\Carbon;
use OneSignal;
use App\MotoristaExperiencia;
use DateTime;
use App\OrdenesPendiente;
use App\TipoCupon;
use App\Cupones;
use App\CuponDescuentoDinero;
use App\CuponDescuentoPorcentaje;
use App\CuponEnvioServicios;
use App\CuponEnvioZonas;
use App\CuponProductoGratis;
use App\OrdenesCupones;
use App\CuponDescuentoDineroServicios;
use App\CuponDescuentoPorcentajeServicios;
use App\CuponEnvioDinero;
use App\AplicaCuponUno;
use App\AplicaCuponDos;
use App\AplicaCuponTres;
use App\AplicaCuponCuatro;
use App\AplicaCuponCinco;
use App\CuponDonacion;
use App\Instituciones;
use Exception;

class ProcesadorOrdenesController extends Controller
{
    // enviar la primer orden
    public function procesarOrdenEstado1(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'userid' => 'required',
                'aplicacupon' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'userid.required' => 'El id del usuario es requerido.',
                'aplicacupon.required' => 'Aplica cupon es requerido.',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            DB::beginTransaction();
           
            try {
                // verificar si tengo carrito
                if($cart = CarritoTemporalModelo::where('users_id', $request->userid)->first()){
                    
                    // agarrar todos los productos del carrito
                    $producto = DB::table('producto AS p')
                    ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')          
                    ->select('p.id AS productoID', 'c.cantidad', 'p.precio', 'p.unidades', 
                    'p.activo', 'p.disponibilidad', 'c.id AS carritoid', 'p.limite_orden', 'p.cantidad_por_orden')
                    ->where('c.carrito_temporal_id', $cart->id)
                    ->get();

                    $hayProducto = 0; //previenir ordenes sin productos
                    $activo = 0; // producto esta activo o disponible
                    $excedido = 0; // producto no esta excedido en unidades
                    $hayEnvio = 0; // para ver si mandamos de este servicio a esa zona, digamos si el servicio esta activo o inactivo
                    $coincideZona = 0; // saber si el carrito zona es igual a la direccion del usuario
                    $servicioidC = $cart->servicios_id; // id servicio que esta en el carrito
                    $zonaidd = $cart->zonas_id; // guardar id de la zona
                    $zonaiduser = 0; // id zona donde esta el usuario selecciono su direccion
                    $limitePromocion = 0; // saver si producto es promocion, y limite por orden
                    $privado = 0; // saver si el servicio es privado para evitar entrar en horario de zona

                    if(count($producto) >= 1){
                        $hayProducto = 1;
                    }

                    // sacar id zona del usuario
                    if($datoZona = Direccion::where('user_id', $request->userid)->where('seleccionado', 1)->first()){

                        $zonaiduser = $datoZona->zonas_id; // zona idzona donde esta el usuario                        
                        $idzonacarrito = $cart->zonas_id; // sacar idzona del carrito temporal

                        // comparar zona del carrito
                        // zona_id direccion seleccionada "es igual" a la zona donde se agrego el carrito
                        if($zonaiduser == $idzonacarrito){
                            $coincideZona = 1;
                        }
                    }
                    
                    // buscar si el servicio brinda adomicilio a esa zona
                    if(DB::table('zonas_servicios')
                    ->where('zonas_id', $zonaiduser)
                    ->where('servicios_id', $servicioidC)
                    ->where('activo', 1)
                    ->first()){
                        $hayEnvio = 1; // el servicio adomicilio a esa zona esta activo
                    }
                   
                    $pilaSub = array(); // para saver si subtotal supera el minimo consumible
                 
                    // recorrer cada producto
                    foreach ($producto as $pro) {     
                    
                        // buscar si el producto ocupa cantidad
                        $uni = Producto::where('id', $pro->productoID)->first();              
                        // obtener todo el producto igual del carrito y sumar sus cantidades
                        $obtenido = CarritoExtraModelo::where('carrito_temporal_id', $cart->id)
                        ->where('producto_id', $pro->productoID)->get();
                        // sumar cantidades del carrito del mismo producto
                        $cantidadCarrito = collect($obtenido)->sum('cantidad');

                        // sumar todo el producto igual, y ver si excedio o no
                        if($uni->utiliza_cantidad){
                                                  
                            // preguntar si excedio la cantidades con las unidades del producto
                            if($cantidadCarrito > $pro->unidades){
                                $excedido = 1; // un producto ha superado las unidades disponibles
                            }
                        
                            if($pro->limite_orden){
                                if($cantidadCarrito > $pro->cantidad_por_orden){
                                    // limite por orden excedida
                                    $limitePromocion = 1;
                                }
                            }                         

                        }else{
                           
                            if($pro->limite_orden){
                                if($cantidadCarrito > $pro->cantidad_por_orden){
                                    // limite por orden excedida
                                    $limitePromocion = 1;
                                }
                            }                          
                        }

                        // un producto no esta disponible o activo
                        if($pro->activo == 0 || $pro->disponibilidad == 0){
                            $activo = 1;
                        }

                        // saver el minimo consumible
                        $cantidad = $pro->cantidad;
                        $precio = $pro->precio;
                        $multi = $cantidad * $precio;
                        array_push($pilaSub, $multi); 
                    }

                    $consumido=0;
                    foreach ($pilaSub as $valor){
                        $consumido=$consumido+$valor;
                    }
                   
                    // saver si lo consumible es mayor al minimo
                    $servicioConsumo = Servicios::where('id', $cart->servicios_id)->first();

                    $minimo = $servicioConsumo->minimo;
                    $utilizaMinimo = $servicioConsumo->utiliza_minimo;
                    $privado = $servicioConsumo->privado;

                    // con esto se sabe si se paga al propietario del servicio o no
                    $pagoPropi = $servicioConsumo->pago_a_ordenes;
                  
                    $minimoConsumido = 0;

                    if($utilizaMinimo == 1){
                        if($consumido >= $minimo){
                            $minimoConsumido = 1;
                        }
                    }

                    $minimoString = (string) $minimo;

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

                    $horarioLocal = 0; // saver si esta cerrado por su horario

                    // verificar si usara la segunda hora
                    $dato = DB::table('horario_servicio AS h')
                    ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                    ->where('h.segunda_hora', 1) // segunda hora habilitada
                    ->where('h.servicios_id', $servicioidC) // id servicio
                    ->where('h.dia', $diaSemana) // dia
                    ->get();
                   
                    // si verificar con la segunda hora
                    if(count($dato) >= 1){
            
                        $horario = DB::table('horario_servicio AS h')
                        ->join('servicios AS s', 's.id', '=', 'h.servicios_id')
                        ->where('h.segunda_hora', '1') // segunda hora habilitada
                        ->where('h.servicios_id', $servicioidC) // id servicio
                        ->where('h.dia', $diaSemana) // dia
                        ->where(function ($query) use ($hora) {
                            $query->where('h.hora1', '<=' , $hora)
                                ->where('h.hora2', '>=' , $hora)
                                ->orWhere('h.hora3', '<=', $hora)
                                ->where('h.hora4', '>=' , $hora);
                        }) 
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
                        ->where('h.segunda_hora', 0) // segunda hora habilitada
                        ->where('h.servicios_id', $servicioidC) // id servicio
                        ->where('h.dia', $diaSemana)                                                     
                        ->where('h.hora1', '<=', $hora) 
                        ->where('h.hora2', '>=', $hora) 
                        ->get();

                        if(count($horario) >= 1){
                            $horarioLocal = 0;
                        }else{
                            $horarioLocal = 1; //cerrado
                        }
                    } 
                    
                    // preguntar si este dia esta cerrado
                    $cerradoHoy = HorarioServicio::where('servicios_id', $servicioidC)
                    ->where('dia', $diaSemana)->first();
                
                    $cerrado = 0; // saver si esta cerrado hoy normalmente

                    if($cerradoHoy->cerrado == 1){
                        $cerrado = 1; //cerrado
                    }else{
                        $cerrado = 0; // no cerrado
                    }
                   
                    // sacar id de zona del carrito
                    $zon = DB::table('zonas')->where('id', $zonaidd)->first();
                
                    // zona saturacion
                    $zonaSaturacion = $zon->saturacion;
                    $mensajeZona = $zon->mensaje;

                    // buscar el cerrado de emergencia
                    $emergencia = DB::table('servicios')
                    ->where('id', $servicioidC)
                    ->first();

                    $cerradoEmergencia = 0;
                    $cerradoEmergencia = $emergencia->cerrado_emergencia;

                    // servicio no activo
                    $servicionoactivo = 0;
                    $servicionoactivo = $emergencia->activo;
                    
                    // horario delivery para esa zona
                    $horaD = DB::table('zonas')
                    ->where('id', $zonaidd)
                    ->where('hora_abierto_delivery', '<=', $hora)
                    ->where('hora_cerrado_delivery', '>=', $hora)
                    ->get();
                   
                    $horarioDelivery = DB::table('zonas')
                    ->where('id', $zonaidd)   // id de la zona
                    ->first();

                    // copia del tiempo extra de la zona que se agrega
                    $copiaTiempoOrden = $horarioDelivery->tiempo_extra;


                    $hora1 = date("h:i A", strtotime($horarioDelivery->hora_abierto_delivery));
                    $hora2 = date("h:i A", strtotime($horarioDelivery->hora_cerrado_delivery));
                            
                    $horaDelivery = 0; // abierto
                    if(count($horaD) >= 1){
                        $horaDelivery = 1; // abierto
                    }else{
                        $horaDelivery = 0; // cerrado
                    } 

                    // saver si el usuario esta activo
                    $usuarioActivo = User::where('id', $request->userid)->pluck('activo')->first();

                    // solo disponible para servicios que sean privados. 

                    // estos datos son para saver si el servicio privado dara adomicilio hasta una determinada
                    // horario, si la zona da de 7 am a 10 pm, el servicio privado es libre de decidir
                    // su horario de entrega a esa zona. solo propietarios con servicio privado.

                    $datos_info = DB::table('zonas_servicios')
                    ->where('zonas_id', $zonaiduser)
                    ->where('servicios_id', $servicioidC)                    
                    ->first();

                    $tiempo_limite = $datos_info->tiempo_limite;
                    $horainicio = $datos_info->horario_inicio;
                    $horafinal = $datos_info->horario_final;
                    $limiteentrega = 0;

                    $hora1limite = date("h:i A", strtotime($horainicio));
                    $hora2limite = date("h:i A", strtotime($horafinal));

                    // sacar dinero limite por orden 
                    $limitedineroorden = DB::table('servicios')->where('id', $servicioidC)->pluck('compra_limite')->first();
                    
                    if($tiempo_limite == 1){

                        // revisado de tiempo
                        if (($horainicio < $hora) && ($hora < $horafinal)) {
                            $limiteentrega = 0; // abierto                        
                        }else{
                            $limiteentrega = 1; // cerrado
                        }
                    
                    }else{
                        // este dato no es tomado en cuenta si $tiempolimite == 0
                        $limiteentrega = 1; // cerrado
                    }     

                
                    //**** VALIDACIONES *****//

                    if($excedido == 1){ // producto excedido en cantidad*
                        return ['success' => 1];
                    }

                    if($activo == 1){ // un producto no esta disponible*
                        return ['success' => 2];
                    }

                    if($coincideZona == 0){ //l a zona de envio no coincide de donde solicito este servicio 
                        return ['success' => 3]; 
                    }                 // direccion siempre tiene que haber, sino se dispara success 3
                    
                    // solo servicios publicos
                    if($privado == 0){
                        if($zonaSaturacion == 1 ){ // no hay entregas para esta zona por el momento*
                            return ['success' => 5, 'mensaje' => $mensajeZona];
                        }
                    }

                    if($cerradoEmergencia == 1){ // local cerrado por emergencia*
                        return ['success' => 6];
                    }

                    // solo negocios publicos
                    if($privado == 0){
                        if($horaDelivery == 0){ // horario de entrega a esta zona a finalizado
                            return ['success' => 7, 'hora1' => $hora1, 'hora2' => $hora2];
                        }
                    }
                   
                    if($cerrado == 1){ // cerrado normalmente este dia*
                        return ['success' => 8];
                    }
                    if( $horarioLocal == 1){ // horario ya cerrado*
                        return ['success' => 9];
                    }
                    if($hayEnvio == 0){ // saver si el servicio envia a esa zona
                        return ['success' => 10];
                    }
                    if($usuarioActivo == 0){ // usuario no activo
                        return ['success' => 11];
                    }      
                    
                    if($privado == 1){
                        if($utilizaMinimo == 1){ // utiliza minimo de ventas
                            if($minimoConsumido == 0){ //lo consumible no supera el minimo de venta
                                return ['success' => 12, 'minimo' => number_format((float)$minimoString, 2, '.', '')];
                            }
                        }
                    }

                    if($limitePromocion == 1){
                        // un producto excedio limite de promocion por orden
                        return ['success' => 13];
                    }

                    if($hayProducto == 0){ // hay productos en el carrito de compras
                        return ['success' => 17];
                    }

                    // servicio no activo
                    if($servicionoactivo == 0){ // 0 es inactivo
                        return ['success' => 18];
                    }

                    // solo para servicios privados, que quieren poner su horario de entrega a la zona 
                    // que dan servicio
                    if($privado == 1){
                        if($tiempo_limite == 1){
                            if($limiteentrega == 1){
                                return ['success' => 19, 'hora1' => $hora1limite, 'hora2' => $hora2limite];
                            }
                        }
                    }

                    // success 20 ocupado para carrito de compras vacio
                    if($consumido > $limitedineroorden){
                        $l = number_format((float)$limitedineroorden, 2, '.', '');
                        return ['success' => 21, 'limite' => $l];
                    }                   

                    // Verificar validez del cupon
                    if($request->aplicacupon == 1){
                        // verificar que exista                  

                        if($ccs = Cupones::where('texto_cupon', $request->cupon)->first()){

                            // verificar validacion si es valido a un
                            $usolimite = $ccs->uso_limite;
                            $contador = $ccs->contador;
                            $activo = $ccs->activo;
                                    
                            if($ccs->ilimitado == 0){
                                // verificar si aun es valido este cupon
                                if($contador >= $usolimite || $activo == 0){
                                    return ['success' => 22]; // cupon ya no es valido
                                }
                            }                                
        
                        }else{
                            // cupon no encontrado
                            return ['success' => 23];
                        }
                    }

                   

                    //INGRESAR DATOS
                
                    // obtener todos los productos de la orden
                    $producto = CarritoExtraModelo::where('carrito_temporal_id', $cart->id)->get();

                    // obtener fila
                    $servicioData = CarritoExtraModelo::where('carrito_temporal_id', $cart->id)->first();

                    // buscar id servicio con el producto
                    $buscar = DB::table('servicios AS s')
                    ->join('servicios_tipo AS st', 'st.servicios_1_id', '=', 's.id')
                    ->join('producto AS p', 'p.servicios_tipo_id', '=', 'st.id')
                    ->select('s.id')
                    ->where('p.id', $servicioData->producto_id)
                    ->first();

                    // obtener id del servicio
                    $servicioid = $buscar->id;

                    // sacar precio envio
                    $envioPrecio = 0;
                    $gananciamotorista = 0;
                    $tipocargo = 0;
                    $mitadprecio = 0;
                    $zona_envio_gratis = 0;
                    $copiaenvio = 0;
                    $copiamingratis = 0;
                    
                    // precio de la zona, aqui ya verificamos que si existe y esta activo
                    if($zz = DB::table('zonas_servicios')
                    ->where('zonas_id', $cart->zonas_id)
                    ->where('servicios_id', $servicioid)
                    ->first()){       
                        // PRIORIDAD 1                
                        $envioPrecio = $zz->precio_envio;
                        $tipocargo = 1;
                        $copiaenvio = $zz->precio_envio;
                        $copiamingratis = $zz->costo_envio_gratis;
                        
                        $gananciamotorista = $zz->ganancia_motorista;                        
                        $mitadprecio = $zz->mitad_precio; 
                        $zona_envio_gratis = $zz->zona_envio_gratis;
                    }

                    // PRIORIDAD 2
                    // mitad de precio para el envio
                    if($mitadprecio == 1){
                        if($envioPrecio != 0){
                            $dividir = $envioPrecio;
                            $envioPrecio = $dividir / 2;
                            $tipocargo = 2;
                        }                       
                    }

                    // PRIORIDAD 3
                    // envio gratis a esta zona
                    if($zona_envio_gratis == 1){
                        $envioPrecio = 0;
                        $tipocargo = 3;
                    }                    
                   
                    // array
                    $pila = array();

                    // recorrer cada producto para saver cantidad y precio
                    foreach($producto as $p){
                        $cantidad = $p->cantidad; // cantidad
                        $dato = Producto::where('id', $p->producto_id)->first(); // info de ese producto
                        $multi = $cantidad * $dato->precio; //multiplicar cantidad por precio
                        array_push($pila, $multi); // unir para subtotal $

                        // restar productos, solo si utiliza cantidad
                        if($dato->utiliza_cantidad == 1){
                            $unidad = $dato->unidades; //unidad que hay de ese producto
                            $resta = $unidad - $cantidad; // restar
                            if($resta < 0){ // por seguridad setearlo a 0
                                Producto::where('id', $dato->id)->update(['unidades' => 0]);
                            }else{
                                Producto::where('id', $dato->id)->update(['unidades' => $resta]);
                            }
                        }
                    }
                    
                    // sumar el array de precios $ subtotales
                    $resultado=0;
                    foreach ($pila as $valor){
                        $resultado=$resultado+$valor;
                    }
                    
                    // convertir subtotal a decimal y tipo string
                    $convertir = number_format((float)$resultado, 2, '.', '');
                    $precio_orden = (string) $convertir;
                    
                    // fecha hoy dia
                    $fecha = Carbon::now('America/El_Salvador');
                                      
                    // sacar minimo de compra para envio gratis, sino pagara el envio
                    $datosInfo = DB::table('zonas_servicios')
                    ->where('zonas_id', $zonaiduser)
                    ->where('servicios_id', $servicioidC)
                    ->first();

                    // PRIORIDAD 4
                    // variable para saver si sub total supero min requerido para nuevo cargo
                   
                    // esta zona tiene un minimo de $$ para aplicar nuevo cargo
                    if($datosInfo->min_envio_gratis == 1){
                        $costo = $datosInfo->costo_envio_gratis;

                        // precio envio sera 0, si supera $$ o igual en carrito de compras
                        if($resultado >= $costo){

                            $envioPrecio = $datosInfo->nuevo_cargo;  // aplicar el nuevo tipo de cargo
                            $tipocargo = 4;
                        }
                    }                  
                    
                    $notaOrden = $request->nota_orden;
                    if(empty($notaOrden) || $notaOrden == null){
                        $notaOrden = "";
                    }
                   
                    $cambio = $request->cambio;
                    if(empty($cambio) || $cambio == null){
                        $cambio = "";
                    }

                    // ver si el producto sera visible al motorista
                    $productovisible = $servicioConsumo->producto_visible;


                    //****** CUPONES *********/

                    // ya verificado que cupon es valido y exista, ingresar registros

                    // setear precio envio si es cupon envio gratis, o el de descuento dinero
                    
                                        
                    if($request->aplicacupon == 1){
                        if($ccs = Cupones::where('texto_cupon', $request->cupon)->first()){
                            
                            if($ccs->tipo_cupon_id == 1){                                
                                $envioPrecio = 0;                               
                            }
                            else if($ccs->tipo_cupon_id == 2){
                                 if($cdd = CuponDescuentoDinero::where('cupones_id', $ccs->id)->first()){
                                     if($cdd->aplica_envio_gratis){
                                         $envioPrecio = 0;
                                     }
                                 }
                            }
                        }
                    }
                   
                    // orden crear normalmente, saver el tiempo automatico o no, depende del estado_2
                    // crear la orden
                    $idOrden = DB::table('ordenes')->insertGetId(
                        [ 'users_id' => $request->userid,
                        'servicios_id' => $servicioid,
                        'nota_orden' => $notaOrden,
                        'cambio' => $cambio,
                        'fecha_orden' => $fecha,
                        'precio_total' => $precio_orden,
                        'precio_envio' => $envioPrecio,                       
                        'mensaje_8' => "",
                        'visible_p' => 1,
                        'visible' => 1,
                        'estado_2' => 0,
                        'hora_2' => 0,
                        'estado_3' => 0,
                        'estado_4' => 0,
                        'estado_5' => 0,
                        'estado_6' => 0,
                        'estado_7' => 0,
                        'estado_8' => 0,
                        'visible_p2' => 0,
                        'visible_p3' => 0,                      
                        'cancelado_cliente' => 0,
                        'cancelado_propietario' => 0,
                        'visible_m' => $productovisible, // si es 1, puede ver los productos el motorista
                        'ganancia_motorista' => $gananciamotorista ,
                        'tipo_cargo' => $tipocargo, // hay 4 tipos,
                        'pago_a_propi' => $pagoPropi  
                        ]
                    );
 

                    // guardar registro de los cupones
                    // YA VERIFICADO que cupon esta activo y hay aun uso.
                    if($request->aplicacupon == 1){
                        if($ccs = Cupones::where('texto_cupon', $request->cupon)->first()){                          
                           
                            if($ccs->tipo_cupon_id == 1){
                                                               
                                //  minimo a comprar para aplicar envio gratis
                                $ced = CuponEnvioDinero::where('cupones_id', $ccs->id)->first();

                                // verifica minimo
                                if($resultado >= $ced->dinero){

                                    // verificar servicio es valido
                                    if(CuponEnvioServicios::where('cupones_id', $ccs->id)->where('servicios_id', $servicioid)->first()){

                                        $idzona = Direccion::where('user_id', $request->userid)->where('seleccionado', 1)->pluck('zonas_id')->first();              
                                        
                                        // verificar zona es valido
                                        if(CuponEnvioZonas::where('cupones_id', $ccs->id)->where('zonas_id', $idzona)->first()){

                                                // ingresar registro
                                                $reg = new OrdenesCupones;
                                                $reg->ordenes_id = $idOrden;
                                                $reg->cupones_id = $ccs->id;
                                                $reg->save();

                                                $contador = $ccs->contador;
                                                $contador = $contador + 1;

                                                // sumas +1 el contador
                                                Cupones::where('id', $ccs->id)->update(['contador' => $contador]);

                                                $envioPrecio = 0;
                                                $uno = new AplicaCuponUno;
                                                $uno->ordenes_id = $idOrden;
                                                $uno->dinero = $ced->dinero;
                
                                                $uno->save();
                                        }else{
                                            return ['success' => 23]; // cupon no valido
                                        }
                                    }else{
                                        return ['success' => 23]; // cupon no valido
                                    }                       
                                }else{
                                    return ['success' => 23]; // cupon no valido
                                }                                
                            }
                            else if($ccs->tipo_cupon_id == 2){

                                // verificar que da para este servicio
                                if(CuponDescuentoDineroServicios::where('cupones_id', $ccs->id)->where('servicios_id', $servicioid)->first()){
                                    
                                    // ingresar registro
                                    $reg = new OrdenesCupones;
                                    $reg->ordenes_id = $idOrden;
                                    $reg->cupones_id = $ccs->id;
                                    $reg->save();

                                    $contador = $ccs->contador;
                                    $contador = $contador + 1;

                                    // sumas +1 el contador
                                    Cupones::where('id', $ccs->id)->update(['contador' => $contador]);

                                    $cdd = CuponDescuentoDinero::where('cupones_id', $ccs->id)->first();

                                    $dos = new AplicaCuponDos;
                                    $dos->ordenes_id = $idOrden;
                                    $dos->dinero = $cdd->dinero;
                                    $dos->aplico_envio_gratis = $cdd->aplica_envio_gratis; 
    
                                    $dos->save();

                                }else{
                                    return ['success' => 23]; // cupon no valido
                                }                                
                            }
                            else if($ccs->tipo_cupon_id == 3){
                             
                                // verificar minimo
                                // minimo a comprar para aplicar descuento
                                $cdp = CuponDescuentoPorcentaje::where('cupones_id', $ccs->id)->first();

                                // verifica minimo
                                if($resultado >= $cdp->dinero){

                                    // verificar servicio si aplica
                                    if(CuponDescuentoPorcentajeServicios::where('cupones_id', $ccs->id)->where('servicios_id', $servicioid)->first()){
                                        
                                        // ingresar registro
                                        $reg = new OrdenesCupones;
                                        $reg->ordenes_id = $idOrden;
                                        $reg->cupones_id = $ccs->id;
                                        $reg->save();

                                        $contador = $ccs->contador;
                                        $contador = $contador + 1;

                                        // sumas +1 el contador
                                        Cupones::where('id', $ccs->id)->update(['contador' => $contador]);
                                        
                                        $tres = new AplicaCuponTres;
                                        $tres->ordenes_id = $idOrden;
                                        $tres->dinero = $cdp->dinero;
                                        $tres->porcentaje = $cdp->porcentaje; 

                                        $tres->save();

                                    }else{
                                        return ['success' => 23]; // cupon no valido
                                    }                                  
                                }else{
                                    return ['success' => 23]; // cupon no valido
                                }

                            }
                            else if($ccs->tipo_cupon_id == 4){

                                $contador = $ccs->contador;
                                $contador = $contador + 1;

                                // sumas +1 el contador
                                Cupones::where('id', $ccs->id)->update(['contador' => $contador]);

                                 // verificar minimo
                                // minimo a comprar para aplicar producto gratis
                                $cpg = CuponProductoGratis::where('cupones_id', $ccs->id)->first();


                                // verifica minimo
                                if($resultado >= $cpg->dinero_carrito){

                                    
                                    // verificar servicio si aplica
                                    if(CuponProductoGratis::where('cupones_id', $ccs->id)->where('servicios_id', $servicioid)->first()){
                                       
                                        // ingresar registro
                                        $reg = new OrdenesCupones;
                                        $reg->ordenes_id = $idOrden;
                                        $reg->cupones_id = $ccs->id;
                                        $reg->save();
                                      
                                        $contador = $ccs->contador;
                                        $contador = $contador + 1;

                                        // sumas +1 el contador
                                        Cupones::where('id', $ccs->id)->update(['contador' => $contador]);
                                       
                                        $cuatro = new AplicaCuponCuatro;
                                        $cuatro->ordenes_id = $idOrden;
                                        $cuatro->dinero_carrito = $cpg->dinero_carrito;
                                        $cuatro->producto = $cpg->nombre;
        
                                        $cuatro->save();
                                        
                                    }else{
                                        return ['success' => 23]; // cupon no valido
                                    }                                  
                                }else{
                                    return ['success' => 23]; // cupon no valido
                                }                               
                            }
                            else if($ccs->tipo_cupon_id == 5){ // cupon donacion

                                $contador = $ccs->contador;
                                $contador = $contador + 1;

                                // sumas +1 el contador
                                Cupones::where('id', $ccs->id)->update(['contador' => $contador]);
                                $cd = CuponDonacion::where('cupones_id', $ccs->id)->first();       


                                // ingresar registro
                                $reg = new OrdenesCupones;
                                $reg->ordenes_id = $idOrden;
                                $reg->cupones_id = $ccs->id;
                                $reg->save();

                                $contador = $ccs->contador;
                                $contador = $contador + 1;

                                // sumas +1 el contador
                                Cupones::where('id', $ccs->id)->update(['contador' => $contador]);

                                $cinco = new AplicaCuponCinco;
                                $cinco->ordenes_id = $idOrden;
                                $cinco->instituciones_id = $cd->instituciones_id;
                                $cinco->dinero = $cd->dinero;
 
                                $cinco->save();               
                            }
                            
                            else{
                                return ['success' => 23]; // cupon no valido
                            }
                        }else{
                            return ['success' => 23]; // cupon no encontrado
                        }
                    }
                     
                        // guadar todos los productos de esa orden
                        foreach($producto as $p){

                            // multiplicar cantidad por precio                                        
                            $productos = DB::table('producto AS p')->where('p.id', $p->producto_id)->first();

                            $notaP = $p->nota_producto;
                            if(empty($notaP) || $notaP == null){
                                $notaP = "";
                            }

                            $data = array('ordenes_id' => $idOrden,
                                        'producto_id' => $p->producto_id,
                                        'cantidad' => $p->cantidad,
                                        'precio' => $productos->precio,
                                        'nota' => $notaP);
                            OrdenesDescripcion::insert($data);
                        }
                       
                    // guardar direccion del usuario
                    $datoDir = Direccion::where('user_id', $request->userid)->where('seleccionado', 1)->first();
                    $dNombre = $datoDir->nombre;
                    $dDireccion = $datoDir->direccion;
                    $dNumero = $datoDir->numero_casa;
                    $dPunto = $datoDir->punto_referencia;
                    $dLati = $datoDir->latitud;
                    $dLong = $datoDir->longitud;
                    $dZona = $datoDir->zonas_id;
                    $dUser = $datoDir->user_id;
                    $dLatiReal = $datoDir->latitud_real;
                    $dLongiReal = $datoDir->longitud_real;
                    $revisado = $datoDir->revisado;

                    if(empty($dNumero)){
                        $dNumero = "";
                    }

                    if(empty($dPunto)){
                        $dPunto = "";
                    }

                    if(empty($dLati)){
                        $dLati = "";
                    }

                    if(empty($dLong)){
                        $dLong = "";
                    }

                    if(empty($dLatiReal)){
                        $dLatiReal = "";
                    }

                    if(empty($dLongiReal)){
                        $dLongiReal = "";
                    }

                    $dispositivo = "3"; // por defecto
                    if($request->dispositivo != null){
                        $dispositivo = $request->dispositivo;
                    }
                    
                    $nuevaDir = new OrdenesDirecciones;
                    $nuevaDir->users_id = $dUser;
                    $nuevaDir->ordenes_id = $idOrden;
                    $nuevaDir->zonas_id = $dZona;
                    $nuevaDir->nombre = $dNombre;
                    $nuevaDir->direccion = $dDireccion;
                    $nuevaDir->numero_casa = $dNumero;
                    $nuevaDir->punto_referencia = $dPunto;
                    $nuevaDir->latitud = $dLati;
                    $nuevaDir->longitud = $dLong;                   
                    $nuevaDir->latitud_real = $dLatiReal;
                    $nuevaDir->longitud_real = $dLongiReal;
                    $nuevaDir->copia_envio = $copiaenvio;
                    $nuevaDir->copia_min_gratis = $copiamingratis;
                    $nuevaDir->copia_tiempo_orden = $copiaTiempoOrden;
                    $nuevaDir->movil_ordeno = $dispositivo; // si es 1, es ios, sino android
                    $nuevaDir->revisado = $revisado;
                    
                    $nuevaDir->save();

                    // guardar notificacion id, solo utilizado para iphone
                   /* if($request->onesignalid != null){
                        if($request->onesignalid != "0000"){
                            User::where('id', $request->userid)->update(['device_id' => $request->onesignalid]);
                        }
                    }*/
                    
                   
                    // BORRAR CARRITO TEMPORAL DEL USUARIO
                    
                    //CarritoExtraModelo::where('carrito_temporal_id', $cart->id)->delete();
                    //CarritoTemporalModelo::where('users_id', $request->userid)->delete();
                    
                    // NOTIFICACIONES AL PROPIETARIO
                    // obtener todos los propietarios registrado al servicio
                    $propietarios = DB::table('propietarios')
                    ->where('servicios_id', $cart->servicios_id)
                    ->where('disponibilidad', 1)
                    ->where('activo', 1)
                    ->get(); 
                  
                    // unir todos los identificadores para el envio de notificaciones
                    $pilaPropietarios = array();
                        foreach($propietarios as $m){
                            if(!empty($m->device_id)){
                                //EVITAR LOS NUEVOS REGISTRADOS
                                if($m->device_id != "0000"){                                   
                                    array_push($pilaPropietarios, $m->device_id); 
                                }
                            }
                        }  

                    // NOTIFICACIONES A PROPIETARIOS, DISPONIBLES
                    if(!empty($pilaPropietarios)){
                        $titulo = "Nueva Orden #".$idOrden;
                        $mensaje = "Ver orden nueva!";
                                             
                        if(!empty($pilaPropietarios)){
                            try {
                                $this->envioNoticacionPropietario($titulo, $mensaje, $pilaPropietarios);                               
                            } catch (Exception $e) {                              
                            }                                                        
                        }

                    }else{               

                        // GUARDAR REGISTROS SINO HAY PROPIETARIO DISPONIBLE
                        
                        /**TIPO

                            1- orden nueva, y no hay propietario disponible
                            1- orden nueva y no hay motoristas disponible

                            3- orden inicia (estado4) y no hay motorista disponible
                            4- orden termina prepararse (estado5) y no hay motorista disponible
                            */

                        $osp = new OrdenesPendiente;
                        $osp->ordenes_id = $idOrden;
                        $osp->fecha = $fecha;
                        $osp->activo = 1;
                        $osp->tipo = 1;
                        $osp->save();  
                        
                        // ENVIAR NOTIFICACIONES SOBRE LA ORDEN QUE NO HAY NINGUN PROPIETARIO DISPONIBLE
                        $administradores = DB::table('administradores')
                        ->where('activo', 1)
                        ->where('disponible', 1)
                        ->get();

                        $pilaAdministradores = array();
                        foreach($administradores as $p){
                            if(!empty($p->device_id)){
                               
                                if($p->device_id != "0000"){
                                    array_push($pilaAdministradores, $p->device_id);
                                }
                            }
                        }  
 
                        //si no esta vacio
                        if(!empty($pilaAdministradores)){
                            $titulo = "Orden sin Propietario";
                            $mensaje = "Verificar";
                            try {
                                $this->envioNoticacionAdministrador($titulo, $mensaje, $pilaAdministradores);
                            } catch (Exception $e) {
                                
                            }
                            
                        }
                    }   

                        // SINO HAY MOTORISTA DISPONIBLE A ESE SERVICIO, MANDAR AVISO A ADMINISTRADORES
                        
                        $mototabla = DB::table('motoristas_asignados AS ms')
                        ->join('motoristas AS m', 'm.id', '=', 'ms.motoristas_id')
                        ->where('m.activo', 1)
                        ->where('m.disponible', 1)
                        ->where('ms.servicios_id', $servicioid)
                        ->get();
 
                        $pilamoto = array();
                        foreach($mototabla as $p){  
                            if(!empty($p->device_id)){
                                if($p->device_id != "0000"){
                                    array_push($pilamoto, $p->device_id); 
                                }                        
                            }
                        }

                        // SINO HAY MOTORISTA, GUARDAR REGISTRO Y ENVIAR LA NOTIFICACION
                        if(empty($pilamoto)){
   
                            $osp = new OrdenesPendiente;
                            $osp->ordenes_id = $idOrden; 
                            $osp->fecha = $fecha;
                            $osp->activo = 1;
                            $osp->tipo = 2;
                            $osp->save();
                            
                            // ENVIAR NOTIFICACIONES SOBRE LA ORDEN QUE NO HAY NINGUN MOTORISTA DISPONIBLE
                            $administradores = DB::table('administradores')
                            ->where('activo', 1)
                            ->where('disponible', 1)
                            ->get();
    
                            $pilaAdministradores = array();
                            foreach($administradores as $p){
                                if(!empty($p->device_id)){
                                   
                                    if($p->device_id != "0000"){
                                        array_push($pilaAdministradores, $p->device_id);
                                    }
                                }
                            } 
    
                            //si no esta vacio
                            if(!empty($pilaAdministradores)){
                                $titulo = "Orden sin Motorista Disponible";
                                $mensaje = "Verificar";
                                try {
                                    $this->envioNoticacionAdministrador($titulo, $mensaje, $pilaAdministradores);
                                } catch (Exception $e) {
                                    
                                }
                                                                
                            }
                        }

                    
                        DB::commit();

                    return ['success' => 14];

                }else{
                    return [
                        'success' => 20 // carrito de compras no encontrado
                    ];
                }
 
            } catch(\Throwable $e){
                DB::rollback();
                return [
                    'success' => 16,
                    'message' => "e".  $e
                ];
            }
        }
    }

    public function verificarCupon(Request $request){
       
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'userid' => 'required',
                'cupon' => 'required'              
            );
        
            $mensajeDatos = array(                                      
                'userid.required' => 'El id del usuario es requerido.',
                'cupon.required' => 'cupon es requerido'            
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            // verificar si usuario tiene carrito de compras
            if($cart = CarritoTemporalModelo::where('users_id', $request->userid)->first()){

                // verificar que el usuario tiene una direccion
                if(Direccion::where('user_id', $request->userid)->where('seleccionado', 1)->first()){

                }else{
                    return ['success' => 20]; // direccion no encontrada
                }


                // verificar que tipo de cupon es y si aun es valido
                if($cupon = Cupones::where('texto_cupon', $request->cupon)->first()){

                    $tipocupon = $cupon->tipo_cupon_id;
                    $usolimite = $cupon->uso_limite;
                    $contador = $cupon->contador;
                    $activo = $cupon->activo;
                    $zonacarrito = $cart->zonas_id;
                    $serviciocarrito = $cart->servicios_id;
                    
                    if($cupon->ilimitado == 0){
                        // verificar si aun es valido este cupon
                        if($contador >= $usolimite || $activo == 0){
                            return ['success' => 2]; // cupon ya no es valido
                        }
                    }  

                    if($tipocupon == 1){// tipo: envio gratis

                        // buscar si este cupon aplica a esta zona
                        if(CuponEnvioZonas::where('cupones_id', $cupon->id)->where('zonas_id', $zonacarrito)->first()){

                            // buscar si este cupon aplica al servicio a comprarle
                            if(CuponEnvioServicios::where('cupones_id', $cupon->id)->where('servicios_id', $serviciocarrito)->first()){

                                // buscar el cupon para encontrar el monto minimo aplicable
                                if($ced = CuponEnvioDinero::where('cupones_id', $cupon->id)->first()){
                                    
                                    $dinerominimo = $ced->dinero; // minimo $ para aplicar envio gratis
 
                                    // obtener el total del carrito de compras
                                    $producto = DB::table('producto AS p')
                                    ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')          
                                    ->select('p.id AS productoID', 'c.cantidad', 'p.precio')
                                    ->where('c.carrito_temporal_id', $cart->id)
                                    ->get();

                                    $pilaSub = array(); // para obtener el sub total
                
                                    // recorrer cada producto
                                    foreach ($producto as $pro) { 
                
                                        // saver el precio multiplicado por la cantidad
                                        $cantidad = $pro->cantidad;
                                        $precio = $pro->precio;
                                        $multi = $cantidad * $precio;
                                        array_push($pilaSub, $multi); 
                                    }
                
                                    $consumido=0;
                                    foreach ($pilaSub as $valor){
                                        $consumido=$consumido+$valor;
                                    }

                                    // comprobar que supera el minimo o igual para aplicar cargo de envio $0.00
                                    if($consumido >= $dinerominimo){

                                        return ['success' => 3]; // correcto
                                    }else{

                                        $dinerominimo = number_format((float)$dinerominimo, 2, '.', '');
                                        return ['success' => 4, 'minimo' => $dinerominimo];
                                        // el sub total en carrito de compra no supera el minimo a comprar
                                        // para dar envio gratis con este cupon
                                    }

                                }else{
                                    return ['success'=> 5];
                                    // cupon no encontrado, aunque fuera raro, ya que se ingresa
                                    // al crear cupon tipo envio gratis
                                }

                            }else{
                                return ['success' => 6]; // este cupon no aplica a este servicio
                            }

                        }else{
                            return ['success' => 7]; // este cupon no aplica a su direccion actual
                        }

                    }else if($tipocupon == 2){ // tipo: descuento $

                        // buscar datos del cupon de dinero
                        if($cdd = CuponDescuentoDinero::where('cupones_id', $cupon->id)->first()){

                            $dinerodescuento = $cdd->dinero;
                            $aplicaenviogratis = $cdd->aplica_envio_gratis;
                            // verificar que aplica para este servicio el descuento en dinero
                            if(CuponDescuentoDineroServicios::where('cupones_id', $cupon->id)
                            ->where('servicios_id', $serviciocarrito)->first()){

                                $producto = DB::table('producto AS p')
                                ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')          
                                ->select('p.id AS productoID', 'c.cantidad', 'p.precio')
                                ->where('c.carrito_temporal_id', $cart->id)
                                ->get();

                                $pilaSub = array(); // para obtener el sub total
                 
                                // recorrer cada producto
                                foreach ($producto as $pro) { 
            
                                    // saver el precio multiplicado por la cantidad
                                    $cantidad = $pro->cantidad;
                                    $precio = $pro->precio;
                                    $multi = $cantidad * $precio;
                                    array_push($pilaSub, $multi); 
                                }
            
                                $consumido=0;
                                foreach ($pilaSub as $valor){
                                    $consumido=$consumido+$valor;
                                }     

                                // descontar dinero a la orden
                                $subtotal = $consumido - $dinerodescuento;
                                if($subtotal <= 0){
                                    $subtotal = 0;
                                }

                                $subtotal = number_format((float)$subtotal, 2, '.', '');

                                //** conocer el envio, tipo de cargo */
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
                                $zz = DB::table('zonas_servicios')                                   
                                ->where('zonas_id', $zonaiduser)
                                ->where('servicios_id', $cart->servicios_id)
                                ->first();

                                // obtiene precio envio de la zona
                                // PRIORIDAD 1
                                $envioPrecio = $zz->precio_envio;                   

                                // PRIORIDAD 2
                                // mitad de precio al envio, solo servicios publicos
                                if($zz->mitad_precio == 1){
                                    if($envioPrecio != 0){
                                        $envioPrecio = $envioPrecio / 2;
                                    }                        
                                }

                                // PRIORIDAD 3
                                // envio gratis a esta zona, solo servicios publicos desde panel de control
                                if($zz->zona_envio_gratis == 1){
                                    $envioPrecio = 0;
                                }

                                $datosInfo = DB::table('zonas_servicios AS z')
                                ->select('z.min_envio_gratis', 'costo_envio_gratis')                       
                                ->where('z.zonas_id', $zonaiduser)
                                ->where('z.servicios_id', $cart->servicios_id)
                                ->first();

                                // PRIORIDAD 4
                                // esta zona tiene un minimo de $$ para envio gratis
                                if($datosInfo->min_envio_gratis == 1){
                                    // precio envio sera 0, si supera $$ en carrito de compras
                                    if($consumido > $datosInfo->costo_envio_gratis){
                                        $envioPrecio = 0;
                                    }
                                }       

                                // ver si este cupon aplica envio gratis
                                if($aplicaenviogratis == 1){
                                    $envioPrecio = 0;
                                }

                                // sumar sub total + cargo de envio
                                $totalsumado = $subtotal + $envioPrecio;

                                $subtotal = number_format((float)$subtotal, 2, '.', '');
                                $envioPrecio = number_format((float)$envioPrecio, 2, '.', '');
                                $totalsumado = number_format((float)$totalsumado, 2, '.', '');

                                // si aplica el descuento en dinero para este servicio
                                //dinero: es el sub total
                                return ['success' => 8, 
                                'dinero' => $subtotal, 
                                'cargo' => $envioPrecio, 
                                'aplica' => $aplicaenviogratis, 
                                'total' => $totalsumado,
                                'descuento' => $cdd->dinero];

                            }else{
                                return ['success' => 9];
                                // para este servicio no aplica el descuento en dinero
                            } 

                        }else{
                            return ['success' => 10]; 
                            // cupon no encontrado, aunque fuera raro,
                            // ya que este se ingresa al crear un cupo tipo descuento dinero
                        }

                    }else if($tipocupon == 3){ // tipo: descuento %

                        // obtener datos del cupon del porcentaje
                        if($cdp = CuponDescuentoPorcentaje::where('cupones_id', $cupon->id)->first()){

                            $porcentaje = $cdp->porcentaje;
                            $minimo = $cdp->dinero;
                            // verificar que aplica para este servicio el descuento de porcentaje
                            if(CuponDescuentoPorcentajeServicios::where('cupones_id', $cupon->id)
                            ->where('servicios_id', $serviciocarrito)->first()){

                                // si aplica el descuento en porcentaje para este servicio
                                // obtener el total del carrito de compras
                                $producto = DB::table('producto AS p')
                                ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')          
                                ->select('p.id AS productoID', 'c.cantidad', 'p.precio')
                                ->where('c.carrito_temporal_id', $cart->id)
                                ->get();

                                $pilaSub = array(); // para obtener el sub total
                 
                                // recorrer cada producto
                                foreach ($producto as $pro) { 
            
                                    // saver el precio multiplicado por la cantidad
                                    $cantidad = $pro->cantidad;
                                    $precio = $pro->precio;
                                    $multi = $cantidad * $precio;
                                    array_push($pilaSub, $multi); 
                                }
            
                                $consumido=0;
                                foreach ($pilaSub as $valor){
                                    $consumido=$consumido+$valor;
                                }      
                                
                                
                                // verificar si supera el minimo a comprar para que aplique este
                                // descuento en %
                                if($consumido >= $minimo){
                                    // PORCENTAJE APLICADO                              
                                    $resta = $consumido * ($porcentaje / 100);
                                    $total = $consumido - $resta;

                                    if($total <= 0){
                                        $total = 0;
                                    }

                                    $total = number_format((float)$total, 2, '.', '');

                                    //** conocer el envio, tipo de cargo */
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
                                    $zz = DB::table('zonas_servicios')                                   
                                    ->where('zonas_id', $zonaiduser)
                                    ->where('servicios_id', $cart->servicios_id)
                                    ->first();

                                    // obtiene precio envio de la zona
                                    // PRIORIDAD 1
                                    $envioPrecio = $zz->precio_envio;                   

                                    // PRIORIDAD 2
                                    // mitad de precio al envio, solo servicios publicos
                                    if($zz->mitad_precio == 1){
                                        if($envioPrecio != 0){
                                            $envioPrecio = $envioPrecio / 2;
                                        }                        
                                    }

                                    // PRIORIDAD 3
                                    // envio gratis a esta zona, solo servicios publicos desde panel de control
                                    if($zz->zona_envio_gratis == 1){
                                        $envioPrecio = 0;
                                    }

                                    $datosInfo = DB::table('zonas_servicios AS z')
                                    ->select('z.min_envio_gratis', 'costo_envio_gratis')                       
                                    ->where('z.zonas_id', $zonaiduser)
                                    ->where('z.servicios_id', $cart->servicios_id)
                                    ->first();

                                    // PRIORIDAD 4
                                    // esta zona tiene un minimo de $$ para envio gratis
                                    if($datosInfo->min_envio_gratis == 1){
                                        // precio envio sera 0, si supera $$ en carrito de compras
                                        if($consumido > $datosInfo->costo_envio_gratis){
                                            $envioPrecio = 0;
                                        }
                                    }       
                                   
                                    // sumar sub total + cargo de envio
                                    $totalsumado = $total + $envioPrecio;
                                    
                                    // sub total, cargo envio, total, porcentaje
                                    return ['success' => 11, 'dinero' => $total, 'cargo' => $envioPrecio, 'total' => $totalsumado, 'aplica' => $porcentaje];
                                }else{
                                    // consumible no alcanza el minimo para aplicar este cupon de 
                                    // descuento
                                    $minimo = number_format((float)$minimo, 2, '.', '');                                   

                                    return ['success' => 12, 'dinero' => $minimo];
                                }

                            }else{
                                return ['success' => 13];
                                // para este servicio no aplica el descuento en dinero
                            }

                        }else{
                            return ['success' => 14]; 
                            // cupon no encontrado, aunque fuera raro,
                            // ya que este se ingresa al crear un cupo tipo descuento dinero
                        }

                    }else if($tipocupon == 4){ // tipo: producto gratis

                        // buscar el cupon y su servicio para ver si aplica el producto gratis
                        if($cdg = CuponProductoGratis::where('cupones_id', $cupon->id)
                        ->where('servicios_id', $serviciocarrito)->first()){

                            //comprobar si el minimo de compra supera el monto minimo para 
                            // producto gratis
                            $dinerominimo = $cdg->dinero_carrito;

                            $producto = DB::table('producto AS p')
                            ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')          
                            ->select('p.id AS productoID', 'c.cantidad', 'p.precio')
                            ->where('c.carrito_temporal_id', $cart->id)
                            ->get();

                            $pilaSub = array(); // para obtener el sub total
             
                            // recorrer cada producto
                            foreach ($producto as $pro) { 
        
                                // saver el precio multiplicado por la cantidad
                                $cantidad = $pro->cantidad;
                                $precio = $pro->precio;
                                $multi = $cantidad * $precio;
                                array_push($pilaSub, $multi); 
                            }
        
                            $consumido=0;
                            foreach ($pilaSub as $valor){
                                $consumido=$consumido+$valor;
                            }  

                                // comprobar que supera el minimo o igual para producto gratis
                                if($consumido >= $dinerominimo){
                                    // producto gratis
                                    return ['success' => 15, 'cargo' => $cdg->nombre];
                                }else{

                                    $t = number_format((float)$dinerominimo, 2, '.', '');
                                    return ['success' => 16, 'dinero' => $t];
                                    // el sub total en carrito de compra no supera el minimo a comprar
                                    // para dar producto gratis
                                }

                        }else{
                            // no aplica para este servicio el producto gratis
                            return ['success' => 17];
                        }
                    }else if($tipocupon == 5){ // tipo: donacion

                        // obtener datos del cupon donacion
                        if($cd = CuponDonacion::where('cupones_id', $cupon->id)->first()){

                            $institucion = $cd->instituciones_id;
                            $donacion = $cd->dinero;   
                            

                            // si aplica el cupon
                            // obtener el total del carrito de compras
                            $producto = DB::table('producto AS p')
                            ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')          
                            ->select('p.id AS productoID', 'c.cantidad', 'p.precio')
                            ->where('c.carrito_temporal_id', $cart->id)
                            ->get();

                            $pilaSub = array(); // para obtener el sub total
                
                            // recorrer cada producto
                            foreach ($producto as $pro) { 
        
                                // saver el precio multiplicado por la cantidad
                                $cantidad = $pro->cantidad;
                                $precio = $pro->precio;
                                $multi = $cantidad * $precio;
                                array_push($pilaSub, $multi); 
                            }
        
                            $consumido=0;
                            foreach ($pilaSub as $valor){
                                $consumido=$consumido+$valor;
                            }                                                                      
                            
                            // DONACION                              
                            $total = $consumido + $donacion;
                            $total = number_format((float)$total, 2, '.', '');

                            //** conocer el envio, tipo de cargo */
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
                            $zz = DB::table('zonas_servicios')                                   
                            ->where('zonas_id', $zonaiduser)
                            ->where('servicios_id', $cart->servicios_id)
                            ->first();

                            // obtiene precio envio de la zona
                            // PRIORIDAD 1
                            $envioPrecio = $zz->precio_envio;                   

                            // PRIORIDAD 2
                            // mitad de precio al envio, solo servicios publicos
                            if($zz->mitad_precio == 1){
                                if($envioPrecio != 0){
                                    $envioPrecio = $envioPrecio / 2;
                                }                        
                            }

                            // PRIORIDAD 3
                            // envio gratis a esta zona, solo servicios publicos desde panel de control
                            if($zz->zona_envio_gratis == 1){
                                $envioPrecio = 0;
                            }

                            $datosInfo = DB::table('zonas_servicios AS z')
                            ->select('z.min_envio_gratis', 'costo_envio_gratis')                       
                            ->where('z.zonas_id', $zonaiduser)
                            ->where('z.servicios_id', $cart->servicios_id)
                            ->first();

                            // PRIORIDAD 4
                            // esta zona tiene un minimo de $$ para envio gratis
                            if($datosInfo->min_envio_gratis == 1){
                                // precio envio sera 0, si supera $$ en carrito de compras
                                if($consumido > $datosInfo->costo_envio_gratis){
                                    $envioPrecio = 0;
                                }
                            }       
                            
                            // sumar sub total + cargo de envio
                            $totalsumado = $total + $envioPrecio;

                            $totalsumado = number_format((float)$totalsumado, 2, '.', '');
                            
                            // sub total, cargo envio, total, donacion, descripcion
                            return ['success' => 22, 
                            'dinero' => $total, 
                            'cargo' => $envioPrecio, 
                            'total' => $totalsumado,                            
                            'descripcion' => $cd->descripcion];   

                        }else{
                            return ['success' => 21]; // cupon no encontrado
                        }
                    }else{
                        return ['success' => 21]; // cupon no encontrado
                    }
                }else{
                    return ['success' => 21]; // cupon no encontrado
                }
            }else{
                return ['success' => 1]; // carrito de compras no encontrado
            }
        }
    }

    // ver ordenes por usuario
    public function verOrdenes(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'userid' => 'required'               
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
                $orden = DB::table('ordenes AS o')
                    ->join('servicios AS s', 's.id', '=', 'o.servicios_id')              
                    ->select('o.id', 's.nombre', 'o.precio_total',
                    'o.nota_orden', 'o.fecha_orden', 'o.precio_envio')
                    ->where('o.users_id', $request->userid)
                    ->where('o.visible', 1)
                    ->get();
                 
                foreach($orden as $o){                    
                    $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));


                    // direccion de envio
                    $direccion = OrdenesDirecciones::where('ordenes_id', $o->id)->pluck('direccion')->first();

                    $o->direccion = $direccion;

                    // buscar si aplico cupon
                    if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()){
                        $o->aplicacupon = 1;
                        // buscar tipo de cupon
                        $tipo = Cupones::where('id', $oc->cupones_id)->first();

                        // ver que tipo se aplico
                        // el precio envio ya esta modificado
                        if($tipo->tipo_cupon_id == 1){
                            $o->tipocupon = 1;

                        }else if($tipo->tipo_cupon_id == 2){
                            $o->tipocupon = 2;
                            // modificar precio
                            $descuento = AplicaCuponDos::where('ordenes_id', $o->id)->pluck('dinero')->first();

                            $total = $o->precio_total - $descuento;
                            if($total <= 0){
                                $total = 0;
                            }

                            $t = $total + $o->precio_envio;

                            // precio modificado con el descuento dinero
                            $o->precio_total = number_format((float)$t, 2, '.', '');

                        }else if($tipo->tipo_cupon_id == 3){
                            $o->tipocupon = 3;

                            $porcentaje = AplicaCuponTres::where('ordenes_id', $o->id)->pluck('porcentaje')->first();
                            $resta = $o->precio_total * ($porcentaje / 100);
                            $total = $o->precio_total - $resta;

                            if($total <= 0){
                                $total = 0;
                            }

                            $t = $total + $o->precio_envio;

                            $o->precio_total = number_format((float)$t, 2, '.', '');

                        }else if($tipo->tipo_cupon_id == 4){
                            $o->tipocupon = 4;
                            $producto = AplicaCuponCuatro::where('ordenes_id', $o->id)->pluck('producto')->first();

                            $o->producto = $producto;

                            // solo sumara sub total + envio
                            $total = $o->precio_total + $o->precio_envio;
                            $o->precio_total = number_format((float)$total, 2, '.', '');
                        }
                        else if($tipo->tipo_cupon_id == 5){
                            $o->tipocupon = 5;

                            // sumar sub total + envio + donacion
                            $acc = AplicaCuponCinco::where('ordenes_id', $o->id)->pluck('dinero')->first();

                            $total = $o->precio_total + $o->precio_envio;
                            $total = $total + $acc;
                            $o->precio_total = number_format((float)$total, 2, '.', '');
                        }
                        else{
                            // dado error, extrano
                            $o->tipocupon = 0;
                            
                          
                            $total = $o->precio_total + $o->precio_envio;
                            $total = number_format((float)$total, 2, '.', '');
        
                            $o->precio_total = $total;
                        }

                    }else{
                        $o->aplicacupon = 0;
                      
                        $total = $o->precio_total + $o->precio_envio;
                        $total = number_format((float)$total, 2, '.', '');
    
                        $o->precio_total = $total;
                    }
                }

                // mensaje para que el usuario vea como tocar la tarjeta
                // /** En nueva actualizacion ya no se vera este mensaje */
                $mensaje = "Tocar la NOTA para ver estado de orden";

                return ['success' => 1, 'ordenes' => $orden, 'mensaje' => $mensaje];
            }else{
                return ['success' => 2];
            }            
        }
    }

     // ver ordenes por usuario, version mejorada para ver que tipo de cupon se ha aplicado
     // version 1.18 android
     public function verOrdenesMejorado(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'userid' => 'required'               
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
                $orden = DB::table('ordenes AS o')
                    ->join('servicios AS s', 's.id', '=', 'o.servicios_id')              
                    ->select('o.id', 's.nombre', 'o.precio_total',
                    'o.nota_orden', 'o.fecha_orden', 'o.precio_envio')
                    ->where('o.users_id', $request->userid)
                    ->where('o.visible', 1)
                    ->get();
                 
                foreach($orden as $o){                    
                    $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));


                    // direccion de envio
                    $direccion = OrdenesDirecciones::where('ordenes_id', $o->id)->pluck('direccion')->first();

                    $o->direccion = $direccion;

                    $cupon = "";

                    // buscar si aplico cupon
                    if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()){
                       
                        // buscar tipo de cupon
                        $tipo = Cupones::where('id', $oc->cupones_id)->first();

                        // ver que tipo se aplico
                        // el precio envio ya esta modificado
                        if($tipo->tipo_cupon_id == 1){
                           
                            $cupon = "Envío Gratis";

                        }else if($tipo->tipo_cupon_id == 2){
                           
                            // modificar precio
                            $data = AplicaCuponDos::where('ordenes_id', $o->id)->first();

                            $total = $o->precio_total - $data->dinero;
                            if($total <= 0){
                                $total = 0;
                            }

                            if($data->aplico_envio_gratis == 1){
                                $cupon = "Descuento de: $" . $data->dinero . " Con Envío Gratis";
                            }else{
                                $cupon = "Descuento de: $" . $data->dinero;
                            }

                            $t = $total + $o->precio_envio; // si aplico envio gratis, este sera $0.00
                            $o->precio_total = number_format((float)$t, 2, '.', '');

                        }else if($tipo->tipo_cupon_id == 3){
                          
                            $porcentaje = AplicaCuponTres::where('ordenes_id', $o->id)->pluck('porcentaje')->first();
                            $resta = $o->precio_total * ($porcentaje / 100);
                            $total = $o->precio_total - $resta;

                            if($total <= 0){
                                $total = 0;
                            }

                            $cupon = "Rebaja de: " . $porcentaje . "%";

                            $t = $total + $o->precio_envio;

                            $o->precio_total = number_format((float)$t, 2, '.', '');

                        }else if($tipo->tipo_cupon_id == 4){
                            
                            $producto = AplicaCuponCuatro::where('ordenes_id', $o->id)->pluck('producto')->first();

                            $o->producto = $producto;

                            $cupon = "Producto Gratis: " . $producto;

                            // solo sumara sub total + envio
                            $total = $o->precio_total + $o->precio_envio;
                            $o->precio_total = number_format((float)$total, 2, '.', '');
                        }
                        else if($tipo->tipo_cupon_id == 5){
                           
                            // sumar sub total + envio + donacion
                            $data = AplicaCuponCinco::where('ordenes_id', $o->id)->first();
                            $ins = Instituciones::where('id', $data->instituciones_id)->pluck('nombre')->first();
 
                            $cupon = "Donación de: $" . $data->dinero . " A: " . $ins; 

                            $total = $o->precio_total + $o->precio_envio;
                            $total = $total + $data->dinero;
                            $o->precio_total = number_format((float)$total, 2, '.', '');
                        }
                        else{
                            // dado error, extrano
                                 
                            $total = $o->precio_total + $o->precio_envio;
                            $total = number_format((float)$total, 2, '.', '');
        
                            $o->precio_total = $total;
                        }

                    }else{
                        
                        $total = $o->precio_total + $o->precio_envio;
                        $total = number_format((float)$total, 2, '.', '');
    
                        $o->precio_total = $total;
                    }  
                    
                    $o->cupon = $cupon;
                }

                // mensaje para que el usuario vea como tocar la tarjeta
                // /** En nueva actualizacion ya no se vera este mensaje */
                $mensaje = "Tocar la NOTA para ver estado de orden";

                return ['success' => 1, 'ordenes' => $orden, 'mensaje' => $mensaje];
            }else{
                return ['success' => 2];
            }            
        }
    }

    // ver ordenes por usuario
    public function verOrdenPorID(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenid' => 'required'               
            );
        
            $mensajeDatos = array(                                      
                'ordenid.required' => 'El id del la orden es requerido.'            
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            if(Ordenes::where('id', $request->ordenid)->first()){
            
                $orden = DB::table('ordenes')                  
                    ->select('id', 'fecha_orden', 'estado_2', 'fecha_2',
                    'hora_2', 'estado_3', 'fecha_3', 'estado_4', 'fecha_4',
                    'estado_5', 'fecha_5', 'estado_6', 'fecha_6', 'estado_7',
                    'fecha_7', 'estado_8', 'fecha_8', 'mensaje_8')
                    ->where('id', $request->ordenid)                
                    ->get();                
                      
                // CLIENTE MIRA EL TIEMPO DEL PROPIETARIO MAS COPIA DEL TIEMPO DE ZONA
                $tiempo = OrdenesDirecciones::where('ordenes_id', $request->ordenid)->first();

                // obtener fecha orden y sumarle tiempo si estado es igual a 2
                foreach($orden as $o){

                    $sumado = $tiempo->copia_tiempo_orden + $o->hora_2;
                    $o->hora_2 = $sumado;

                    // ver si fue cancelado desde panel de control
                    $o->canceladoextra = $tiempo->cancelado_extra;
                     
                    if($o->estado_2 == 1){ // propietario da el tiempo de espera                        
                        $o->fecha_2 = date("h:i A d-m-Y", strtotime($o->fecha_2));                    
                    }

                    if($o->estado_3 == 1){ 
                        $o->fecha_3 =date("h:i A d-m-Y", strtotime($o->fecha_3));  
                    }
                
                    if($o->estado_4 == 1){ // orden en preparacion
                        $time1 = Carbon::parse($o->fecha_4);
                        
                        // ya va sumado el tiempo extra de la zona, aqui arriba
                        $horaEstimada = $time1->addMinute($o->hora_2)->format('h:i A d-m-Y');
                        $o->horaEstimada = $horaEstimada;
                    }
                    
                    if($o->estado_5 == 1){                             
                        $o->fecha_5 = date("h:i A d-m-Y", strtotime($o->fecha_5));
                    }

                    if($o->estado_6 == 1){                     
                        $o->fecha_6 = date("h:i A d-m-Y", strtotime($o->fecha_6));
                    }

                    if($o->estado_7 == 1){
                        $o->fecha_7 = date("h:i A d-m-Y", strtotime($o->fecha_7));
                    }

                    if($o->estado_8 == 1){
                        $o->fecha_8 = date("h:i A d-m-Y", strtotime($o->fecha_8));
                    }

                    $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));
                }
            
                return ['success' => 1, 'ordenes' => $orden];
            }else{
                return ['success' => 2];
            }
        }
    }

    // lista de productos de la orden
    public function ordenProductos(Request $request){
        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required'
        );

        $mensajeDatos = array(
            'ordenid.required' => 'El id de la orden es requerido.'
            );

        $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

        if($validarDatos->fails()) 
        {
            return [
                'success' => 0, 
                'message' => $validarDatos->errors()->all()
            ];
        }  

        if(Ordenes::where('id', $request->ordenid)->first()){
            $producto = DB::table('ordenes AS o')
                        ->join('ordenes_descripcion AS od', 'od.ordenes_id', '=', 'o.id')
                        ->join('producto AS p', 'p.id', '=', 'od.producto_id')
                        ->select('od.id AS productoID', 'p.nombre', 'p.utiliza_imagen', 'p.imagen', 'od.precio', 'od.cantidad')
                        ->where('o.id', $request->ordenid)
                        ->get();
            
                        foreach($producto as $p){
                            $cantidad = $p->cantidad;
                            $precio = $p->precio;
                            $multi = $cantidad * $precio;
                            $p->multiplicado = number_format((float)$multi, 2, '.', '');
                        }

                        return ['success' => 1, 'productos' => $producto];
        }else{
                        return ['success' => 2];
        }
    }

     // ver producto individual de la orden
     public function ordenProductosIndividual(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenesid' => 'required' // id tabla orden_descripcion               
            );
        
            $mensajeDatos = array(                                      
                'ordenesid.required' => 'El id de orden descripcion es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  


            if(OrdenesDescripcion::where('id', $request->ordenesid)->first()){
               
                $producto = DB::table('ordenes_descripcion AS o')
                    ->join('producto AS p', 'p.id', '=', 'o.producto_id')
                    ->select('p.imagen', 'p.nombre', 'p.descripcion', 'p.utiliza_imagen', 'o.precio', 'o.cantidad', 'o.nota')
                    ->where('o.id', $request->ordenesid)
                    ->get();

                    foreach($producto as $p){
                        $cantidad = $p->cantidad;
                        $precio = $p->precio;
                        $multi = $cantidad * $precio;
                        $p->multiplicado = number_format((float)$multi, 2, '.', '');
                    }
            
                return ['success' => 1, 'producto' => $producto];
            }else{
                return ['success' => 2];
            }
        }
    }

  
     // cancelar una orden por el cliente 
     public function cancelarOrden(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenid' => 'required' 
            );
        
            $mensajeDatos = array(                                      
                'ordenid.required' => 'El id de orden es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            if($o = Ordenes::where('id', $request->ordenid)->first()){
               
                if($o->estado_8 == 0){

                    // seguro para evitar cancelar cuando servicio inicia a preparar orden
                    if($o->estado_4 == 1){ 
                        return ['success' => 3];
                    }

                    $fecha = Carbon::now('America/El_Salvador');
                    Ordenes::where('id', $request->ordenid)->update(['estado_8' => 1, 'cancelado_cliente' => 1, 'visible' => 0, 'fecha_8' => $fecha]);
                  
                    // notificar a los propietario de la orden cancelada
                    $propietarios = DB::table('propietarios AS p')
                    ->select('p.device_id')
                    ->where('p.servicios_id', $o->servicios_id)
                    ->where('p.disponibilidad', 1)
                    ->get();

                    $pilaUsuarios = array();
                        foreach($propietarios as $m){ 
                            if(!empty($m->device_id)){
                                if($m->device_id != "0000"){
                                    array_push($pilaUsuarios, $m->device_id); 
                                }
                            }
                        }

                    // enviar notificaciones a todos los propietarios asignados
                    $titulo = "Orden #".$o->id . " Cancelada";
                    $mensaje = "Orden cancelada por el cliente.";
        
                    if(!empty($pilaUsuarios)){
                        try {
                            $this->envioNoticacionPropietario($titulo, $mensaje, $pilaUsuarios);
                        } catch (Exception $e) {
                            
                        }
                        
                    }
                    return ['success' => 1]; // cancelado

                }else{
                    return ['success' => 2]; // ya cancelada
                }
            }else{
                return ['success' => 4]; // no encontrada
            }
        }
    }

    // borrar vista de la orden al cliente
    public function borrarVistaOrden(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenid' => 'required' 
            );
        
            $mensajeDatos = array(                                      
                'ordenid.required' => 'El id de orden es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  
            
            // oculta la orden al cliente
            if(Ordenes::where('id', $request->ordenid)->first()){               
                
                    Ordenes::where('id', $request->ordenid)->update(['visible' => 0]);                  
                    return ['success' => 1]; 
                
            }else{
                return ['success' => 2]; // no encontrada
            }
        }
    }

     // el usuario acepta el tiempo de espera *
     public function procesarOrdenEstado3(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenid' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'ordenid.required' => 'El id de la orden es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0,
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($or = Ordenes::where('id', $request->ordenid)->first()){

                if($or->estado_3 == 0){

                    $fecha = Carbon::now('America/El_Salvador');
                    Ordenes::where('id', $request->ordenid)->update(['estado_3' => 1,
                    'fecha_3' => $fecha]);
 
                    // mandar notificacion al propietario
                    $propietarios = DB::table('propietarios')
                    ->where('servicios_id', $or->servicios_id)
                    ->where('disponibilidad', 1)
                    ->where('activo', 1)
                    ->get();

                    // enviar notificaciones  
                    $pilaUsuarios = array();
                    foreach($propietarios as $p){     
                        if(!empty($p->device_id)){ 
                            if($p->device_id != "0000"){
                                array_push($pilaUsuarios, $p->device_id); 
                            }
                        }
                    }

                    $titulo = "Cliente acepto tiempo";
                    $mensaje = "El cliente desea esperar la orden";
            
                    if(!empty($pilaUsuarios)){
                        try {
                            $this->envioNoticacionPropietario($titulo, $mensaje, $pilaUsuarios);
                        } catch (Exception $e) {
                            
                        }
                       
                    }

                    $orden = DB::table('ordenes AS o')
                    ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
                    ->select('o.id', 's.nombre', 'o.precio_total',
                    'o.fecha_orden', 'o.precio_envio', 'o.estado_2', 'o.fecha_2',
                    'o.hora_2', 'o.estado_3', 'o.fecha_3', 'o.estado_4', 'o.fecha_4',
                    'o.estado_5', 'o.fecha_5', 'o.estado_6', 'o.fecha_6', 'o.estado_7',
                    'o.fecha_7', 'o.estado_8', 'o.fecha_8', 'o.mensaje_8')
                    ->where('o.id', $request->ordenid)
                    ->get();

                    foreach($orden as $o){
                        
                        if($o->estado_3 == 1){ // orden en preparacion
                            $fechaE3 = $o->fecha_3;
                            $hora3 = date("h:i A", strtotime($fechaE3));
                            $fecha3 = date("d-m-Y", strtotime($fechaE3));
                            $o->fecha_3 = $hora3 . " " . $fecha3;
                        }
                    }
                    
                    return ['success' => 1, 'ordenes' => $orden];
                }else{
                    return ['success' => 2];
                }
            }else{
                return ['success' => 3];
            }
        }
    }

     // ver motorista asignado a la orden
     public function motoristaAsignado(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenid' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'ordenid.required' => 'El ordenid de la orden es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0,
                    'message' => $validarDatos->errors()->all()
                ];
            }
 
            if($or = Ordenes::where('id', $request->ordenid)->first()){
                
                if($motoAsignado = DB::table('motorista_ordenes AS mo')
                ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')
                ->select('m.imagen', 'm.nombre', 'm.telefono', 'm.tipo_vehiculo AS vehiculo', 'm.numero_vehiculo AS placa')
                //->where('m.activo', 1)
                //->where('m.disponible', 1)
                ->where('mo.ordenes_id', $or->id)
                ->first()){
                $foto = $motoAsignado->imagen;
                $nombre = $motoAsignado->nombre;
                $telefono = "-"; // no mostrar numero de motorista al cliente
                $vehiculo = $motoAsignado->vehiculo;
                $placa = $motoAsignado->placa;
                return ['success' => 1, 'foto' => $foto, 'nombre' => $nombre, 'telefono' => $telefono, 'vehiculo' => $vehiculo, 'placa' => $placa];
                }else{
                    return ['success' => 2]; // motorista no encontrado
                }
            }else{
                return ['success' => 3];
            }
        }
    }

    // calificar motorista
    public function calificarMotorista(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenid' => 'required',
                'valor' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'ordenid.required' => 'El id de la orden es requerido',
                'valor.required' => 'La valoracion es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0,
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($or = Ordenes::where('id', $request->ordenid)->first()){
                
                if($or->estado_6 == 0){                    
                    return ['success' => 1]; // motorista no asignado aun
                }

                if(DB::table('motorista_experiencia AS me')
                ->where('me.ordenes_id', $or->id)
                ->first()){
                    Ordenes::where('id', $or->id)->update(['visible' => 0]);
                    return ['success' => 2]; // ya hay una valoracion
                }

                // sacar id del motorista de la orden
                $motoristaDato = DB::table('motorista_ordenes AS m')           
                ->where('m.ordenes_id', $or->id)
                ->first();
                
                $idMotorista = $motoristaDato->motoristas_id;
                $fecha = Carbon::now('America/El_Salvador');

                $men = $request->mensaje;
                if($men == null){
                    $men = "-";
                }

                $nueva = new MotoristaExperiencia;
                $nueva->ordenes_id = $or->id;
                $nueva->motoristas_id = $idMotorista;
                $nueva->experiencia = $request->valor;
                $nueva->mensaje = $men;
                $nueva->fecha = $fecha;
                $nueva->save();
                
                // ocultar orden al usuario
                Ordenes::where('id', $or->id)->update(['visible' => 0]);

                return ['success' => 3];                
            }else{
                return ['success' => 4];
            } 
        }
    }
 
    public function envioNoticacionCliente($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionCliente($titulo, $mensaje, $pilaUsuarios);
    }

    public function envioNoticacionPropietario($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionPropietario($titulo, $mensaje, $pilaUsuarios);
    }

    public function envioNoticacionAdministrador($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionAdministrador($titulo, $mensaje, $pilaUsuarios);
    } 
}
