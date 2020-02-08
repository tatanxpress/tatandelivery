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

class ProcesadorOrdenesController extends Controller
{
    // enviar la primer orden
    public function procesarOrdenEstado1(Request $request){
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
                    $zon = DB::table('zonas') 
                    ->select('saturacion')
                    ->where('id', $zonaidd)
                    ->first();
                
                    // zona saturacion para no envio adomicilio
                    $zonaSaturacion = $zon->saturacion;    

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
                
                    // validaciones
                    if($excedido == 1){ // producto excedido en cantidad*
                        return ['success' => 1];
                    }
                    if($activo == 1){ // un producto no esta disponible*
                        return ['success' => 2];
                    }
                    if($coincideZona == 0){ //l a zona de envio no coincide de donde solicito este servicio 
                        return ['success' => 3]; 
                    }                 // direccion siempre tiene que haber, sino se dispara success 3
                    if($zonaSaturacion == 1 ){ // no hay entregas para esta zona por el momento*
                        return ['success' => 5];
                    }
                    if($cerradoEmergencia == 1){ // local cerrado por emergencia*
                        return ['success' => 6];
                    }
                    if($horaDelivery == 0){ // horario de entrega a esta zona a finalizado
                        return ['success' => 7, 'hora1' => $hora1, 'hora2' => $hora2];
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
                    if($utilizaMinimo == 1){ // utiliza minimo de ventas
                        if($minimoConsumido == 0){ //lo consumible no supera el minimo de venta
                            return ['success' => 12, 'minimo' => number_format((float)$minimoString, 2, '.', '')];
                        }                        
                    }                    
                    if($limitePromocion == 1){
                        // un producto excedio limite de promocion por orden
                        return ['success' => 13];
                    }

                    if($hayProducto == 0){
                        return ['success' => 17];
                    }

                    // servicio no activo
                    if($servicionoactivo == 0){ // 0 es inactivo
                        return ['success' => 18];
                    }
                
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
                    
                    // precio de la zona, aqui ya verificamos que si existe y esta activo
                    if($zz = DB::table('zonas_servicios AS z')
                    ->select('z.precio_envio', 'ganancia_motorista')
                    ->where('z.zonas_id', $cart->zonas_id)
                    ->where('z.servicios_id', $servicioid)
                    ->first()){                       
                        $envioPrecio = $zz->precio_envio;
                        $gananciamotorista = $zz->ganancia_motorista;
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
                   
                    // comprobar si hay envio gratis
                    $envioGratis = $servicioConsumo->envio_gratis;
                    $nombreServicio = $servicioConsumo->nombre;

                    // al tener envio gratis, solo caeran a los motoristas asignados a ese servicio
                    if($envioGratis == 1){
                        $envioPrecio = 0;
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
                        'envio_gratis' => $envioGratis,
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
                        'ganancia_motorista' => $gananciamotorista                      
                        ]
                    );
                     
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
                    $dTelefono = $datoDir->telefono;
                    $dLati = $datoDir->latitud;
                    $dLong = $datoDir->longitud;
                    $dZona = $datoDir->zonas_id;
                    $dUser = $datoDir->user_id;

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
                   
                    $nuevaDir = new OrdenesDirecciones;
                    $nuevaDir->users_id = $dUser;
                    $nuevaDir->ordenes_id = $idOrden;
                    $nuevaDir->zonas_id = $dZona;
                    $nuevaDir->nombre = $dNombre;
                    $nuevaDir->direccion = $dDireccion;
                    $nuevaDir->numero_casa = $dNumero;
                    $nuevaDir->punto_referencia = $dPunto;
                    $nuevaDir->telefono = $dTelefono;
                    $nuevaDir->latitud = $dLati;
                    $nuevaDir->longitud = $dLong;
                    
                    $nuevaDir->save();

                    // BORRAR CARRITO TEMPORAL DEL USUARIO
                    
                    CarritoExtraModelo::where('carrito_temporal_id', $cart->id)->delete();
                    CarritoTemporalModelo::where('users_id', $request->userid)->delete();
                    
                    // NOTIFICACIONES AL PROPIETARIO
                    // obtener todos los propietarios registrado al servicio
                    $propietarios = DB::table('propietarios AS p')
                    ->where('p.servicios_id', $cart->servicios_id)
                    ->where('p.disponibilidad', 1)
                    ->where('p.activo', 1)
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
                        $alarma = 1; //sonido alarma
                        $color = 1; // color rojo
                        $icono = 1; // campana
                        $tipo = 1; // es propietario
                       
                        if(!empty($pilaPropietarios)){     
                                
                            $this->envioNoticacion($titulo, $mensaje, $pilaPropietarios, $alarma, $color, $icono, $tipo);                            
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
                            $mensaje = "Servicio: ".$nombreServicio;
                            $alarma = 1; //sonido alarma
                            $color = 1; // color rojo
                            $icono = 1; // campana
                            $tipo = 4;

                                $this->envioNoticacion($titulo, $mensaje, $pilaAdministradores, $alarma, $color, $icono, $tipo);
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
                                $mensaje = "Servicio: ".$nombreServicio;
                                $alarma = 1; //sonido alarma
                                $color = 1; // color rojo
                                $icono = 1; // campana
                                $tipo = 4;

                                $this->envioNoticacion($titulo, $mensaje, $pilaAdministradores, $alarma, $color, $icono, $tipo);
                                
                            }
                        }
                    
                        DB::commit();

                    return ['success' => 14];

                }else{
                    return [
                        'success' => 16 // carrito de compras no encontrado
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
                    ->select('o.id', 's.nombre', 'o.precio_total', 'o.nota_orden', 'o.fecha_orden', 'o.precio_envio', 'o.envio_gratis')
                    ->where('o.users_id', $request->userid)
                    ->where('o.visible', 1)
                    ->get();
                 
                foreach($orden as $o){
                    $fechaOrden = $o->fecha_orden;
                    $hora = date("h:i A", strtotime($fechaOrden));
                    $fecha = date("d-m-Y", strtotime($fechaOrden));
                    $o->fecha_orden = $hora . " " . $fecha;

                    $total = $o->precio_total;
                    $envio = $o->precio_envio;
                    $t = $total + $envio;
                    $sumado = number_format((float)$t, 2, '.', '');

                    $o->precio_total = $sumado;
                }

                return ['success' => 1, 'ordenes' => $orden];
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
            
                $orden = DB::table('ordenes AS o')
                    ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
                    ->select('o.id', 'o.servicios_id', 's.nombre', 'o.precio_total',
                    'o.fecha_orden', 'o.precio_envio', 'o.estado_2', 'o.fecha_2',
                    'o.hora_2', 'o.estado_3', 'o.fecha_3', 'o.estado_4', 'o.fecha_4',
                    'o.estado_5', 'o.fecha_5', 'o.estado_6', 'o.fecha_6', 'o.estado_7',
                    'o.fecha_7', 'o.estado_8', 'o.fecha_8', 'o.mensaje_8')
                    ->where('o.id', $request->ordenid)                
                    ->get();
                

                $excedido = 0; // para ver si el cliente puede cancelar la orden por tardio
                            

                // obtener fecha orden y sumarle tiempo si estado es igual a 2
                foreach($orden as $o){

                    // sumar precio de envio + producto
                    $sumado = $o->precio_total + $o->precio_envio;
                    $total = number_format((float)$sumado, 2, '.', '');

                    $o->precio_total = $total;
                     
                    if($o->estado_2 == 1){ // propietario da el tiempo de espera
                        
                        $fechaE2 = $o->fecha_2;
                        $hora2 = date("h:i A", strtotime($fechaE2));
                        $fecha2 = date("d-m-Y", strtotime($fechaE2));
                    
                        $o->fecha_2 = $hora2 . " " . $fecha2;
                    }

                    if($o->estado_3 == 1){                        
                        $fechaE3 = $o->fecha_3;
                        $hora3 = date("h:i A", strtotime($fechaE3));
                        $fecha3 = date("d-m-Y", strtotime($fechaE3));                      
                        $o->fecha_3 = $hora3 . " " . $fecha3;
                    }
                
                    if($o->estado_4 == 1){ // orden en ppreparacion
                        $time1 = Carbon::parse($o->fecha_4);
                        
                        $horaEstimada = $time1->addMinute($o->hora_2)->format('h:i A d-m-Y');
                        $o->horaEstimada = $horaEstimada;

                    }
                    
                    if($o->estado_5 == 1){                        
                        $fechaE5 = $o->fecha_5;
                        $hora5 = date("h:i A", strtotime($fechaE5));
                        $fecha5 = date("d-m-Y", strtotime($fechaE5));                      
                        $o->fecha_5 = $hora5 . " " . $fecha5;
                    }

                    if($o->estado_6 == 1){
                        $fechaE6 = $o->fecha_6;
                        $hora6 = date("h:i A", strtotime($fechaE6));
                        $fecha6 = date("d-m-Y", strtotime($fechaE6));                      
                        $o->fecha_6 = $hora6 . " " . $fecha6;
                    }

                    if($o->estado_7 == 1){
                        $fechaE7 = $o->fecha_7;
                        $hora7 = date("h:i A", strtotime($fechaE7));
                        $fecha7 = date("d-m-Y", strtotime($fechaE7));
                        $o->fecha_7 = $hora7 . " " . $fecha7;
                    }

                    if($o->estado_8 == 1){
                        $fechaE8 = $o->fecha_8;
                        $hora8 = date("h:i A", strtotime($fechaE8));
                        $fecha8 = date("d-m-Y", strtotime($fechaE8));
                        $o->fecha_8 = $hora8 . " " . $fecha8;
                    }

                 

                        $fechaOrden = $o->fecha_orden;
                        $hora = date("h:i A", strtotime($fechaOrden));
                        $fecha = date("d-m-Y", strtotime($fechaOrden));
                        $o->fecha_orden = $hora . " " . $fecha;  
                }
            
                return ['success' => 1, 'excedido' => $excedido, 'ordenes' => $orden];
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
                    $alarma = 1;
                    $color = 1;
                    $icono = 5;
                    $tipo = 1; //propietarios

                    if(!empty($pilaUsuarios)){
                        $this->envioNoticacion($titulo, $mensaje, $pilaUsuarios, $alarma, $color, $icono, $tipo);
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
                        array_push($pilaUsuarios, $p->device_id); 
                        }
                    }

                    $titulo = "Cliente acepto tiempo";
                    $mensaje = "El cliente desea esperar la orden";
                    $alarma = 1;
                    $color = 3;
                    $icono = 1;
                    $tipo = 1; // propietarios

                    if(!empty($pilaUsuarios)){
                        $this->envioNoticacion($titulo, $mensaje, $pilaUsuarios, $alarma, $color, $icono, $tipo);
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
                
                if($motoAsignado = DB::table('motorista_ordenes AS mo')
                ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')
                ->select('m.imagen', 'm.nombre', 'm.telefono', 'm.tipo_vehiculo AS vehiculo', 'm.numero_vehiculo AS placa')
                //->where('m.activo', 1)
                //->where('m.disponible', 1)
                ->where('mo.ordenes_id', $or->id)
                ->first()){
                $foto = $motoAsignado->imagen;
                $nombre = $motoAsignado->nombre;
                $telefono = $motoAsignado->telefono;
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

                $nueva = new MotoristaExperiencia;
                $nueva->ordenes_id = $or->id;
                $nueva->motoristas_id = $idMotorista;
                $nueva->experiencia = $request->valor;
                $nueva->mensaje = $request->mensaje;
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


    public function envioNoticacion($titulo, $mensaje, $pilaUsuarios, $alarma, $color, $icono, $tipo){
        OneSignal::sendNotificationToUser($titulo, $mensaje, $pilaUsuarios, $alarma, $color, $icono, $tipo);
    }
}
