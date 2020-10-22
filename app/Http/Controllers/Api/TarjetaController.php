<?php

namespace App\Http\Controllers\Api;

use App\Ciudades;
use App\AreasPermitidas;
use App\DineroOrden;
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
use Log;
use App\EncargosZona;
use App\CarritoEncargo;
use App\Encargos;
use App\VersionesApp;
use App\CrediPuntos;



class TarjetaController extends Controller
{
    public function obtenerCiudades(Request $request){

        $ciudades = DB::table('ciudades AS c')
        ->join('zonas AS z', 'z.id', '=', 'c.zonas_id')
        ->select('z.id', 'c.nombre', 'z.latitud', 'z.longitud')
        ->get();

        return ['success' => 1, 'ciudades' => $ciudades];
    }

    // ver los metodos de pago, segun area
    public function verMetodosDePago(Request $request){
        if($request->isMethod('post')){
            $rules = array(
                'userid' => 'required',
            );

            $messages = array(
                'userid.required' => 'El id es requerido.',
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails()){
                return ['success' => 0, 'message' => $validator->errors()->all()];
            }

            if($uu = User::where('id', $request->userid)->first()){

                $tipo = 0; // unicamente credi puntos

                if(AreasPermitidas::where('areas', $uu->area)->first()){
                    // permitir pagar con efectivo y credi puntos
                    $tipo = 1;
                }

                return ['success' => 1, 'tipo' => $tipo, 'credito' => $uu->monedero];
            }else{
                return ['success' => 2];
            }
        }
    }

    // ver proceso de orden, segun metodo de pago
    public function verProcesarOrdenPuntos(Request $request){
        if($request->isMethod('post')){
            $reglaDatos = array(
                'userid' => 'required',
                'metodo' => 'required'
            );

            $mensajeDatos = array(
                'userid.required' => 'El id del usuario es requerido.',
                'metodo.required' => 'Metodo de pago es requerido'
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

                    $data = User::where('id', $request->userid)->first();

                    $credipuntos = $data->monedero;


                    // sacar id del servicio del carrito
                    $servicioidC = $cart->servicios_id;
                    $zonaiduser = 0;
                    // sacar id zona del usuario
                    if($user = Direccion::where('user_id', $request->userid)
                    ->where('seleccionado', 1)->first()){
                        $zonaiduser = $user->zonas_id; // zona id donde esta el usuario
                    }

                    $envioPrecio = 0;
                    $resultado=0; // sub total del carrito de compras
                    $faltacredito = 0; // si es 1, falta credito

                    $direccion = "";
                    // obtener direccion
                    if($di = Direccion::where('user_id', $request->userid)->where('seleccionado', 1)->first()){
                        $direccion = $di->direccion;
                    }else{
                        // no hay direccion
                        return ['success' => 2];
                    }

                     // todo el producto del carrito de compras
                     $producto = DB::table('producto AS p')
                     ->join('carrito_extra AS c', 'c.producto_id', '=', 'p.id')
                     ->select('p.precio', 'c.cantidad')
                     ->where('c.carrito_temporal_id', $cart->id)
                     ->get();

                     $pila = array();

                     // multiplicar precio x cantidad
                     foreach($producto as $p){
                         $cantidad = $p->cantidad;
                         $precio = $p->precio;
                         $multi = $cantidad * $precio;
                         array_push($pila, $multi);
                     }

                     // sumar todo los sub totales de cada producto multiplicado
                     foreach ($pila as $valor){
                         $resultado=$resultado+$valor;
                     }

                    // precio de la zona
                    // aqui no importa si esta activo o inactivo, solo obtendra el precio
                    // para ver el proceso debe existir en zonas_servicios
                    $zz = DB::table('zonas_servicios')
                    ->where('zonas_id', $zonaiduser)
                    ->where('servicios_id', $servicioidC)
                    ->first();

                    // PREGUNTAR PRIMERO SI ES UN AREA PERMITIDA
                    if(AreasPermitidas::where('areas', $data->area)->first()){
                        // area permitida, continuar con los precio envio zona servicio

                        // obtiene precio envio de la zona servicio
                        // PRIORIDAD 1
                        $envioPrecio = $zz->precio_envio;

                        // PRIORIDAD 2
                        // mitad de precio al envio
                        if($zz->mitad_precio == 1){
                            if($envioPrecio != 0){
                                $dividir = $envioPrecio;
                                $envioPrecio = $dividir / 2;
                            }
                        }

                        // PRIORIDAD 3
                        // envio gratis a esta zona servicio
                        if($zz->zona_envio_gratis == 1){
                            $envioPrecio = 0;
                        }


                        $datosInfo = DB::table('zonas_servicios')
                        ->where('zonas_id', $zonaiduser)
                        ->where('servicios_id', $servicioidC)
                        ->first();



                        // PRIORIDAD 4
                        // esta zona tiene un minimo de $$ para aplicar nuevo tipo de cargo
                        if($datosInfo->min_envio_gratis == 1){
                            $costo = $datosInfo->costo_envio_gratis;

                            // verificar
                            if($resultado >= $costo){
                                //aplicar nuevo tipo cargo
                                $envioPrecio = $datosInfo->nuevo_cargo;
                            }
                        }

                    }else{

                        // Areas no permitidas, se tomara precio envio dado por administrador

                        $infoDirec = Direccion::where('user_id', $request->userid)->where('seleccionado', 1)->first();

                        // No tomar en cuenta si direccion es verificada, ya que a esta pantalla
                        // entrara si direccion ha sido verificada unicamente

                        if($infoDirec->precio_envio == null){
                            $envioPrecio = 0;
                        }else{
                            $envioPrecio = $infoDirec->precio_envio;
                        }
                    }



                    // total de carrito de compras
                    $total = $resultado;

                    // sumar a total
                    $total = $resultado + $envioPrecio;

                    // ver si estara visible el boton cupones
                    $bntcupon = DineroOrden::where('id', 1)->pluck('ver_cupones')->first();

                    // si no es un area permitida, no podra aplicar cupon
                    if(!AreasPermitidas::where('areas', $data->area)->first()){
                        $bntcupon = 0;
                    }

                    if($request->metodo == 1){ // utiliza credi puntos
                        $credipuntosDescontado = 0;
                        $credipuntosDescontado = $credipuntos - $total; // lo que tiene el usuario - total (sub total + cargo de envio)
                        // los credi puntos son insuficientes
                        if($credipuntosDescontado < 0){
                            $faltacredito = 1;
                        }
                    }

                    $total = number_format((float)$total, 2, '.', '');
                    $envioPrecio = number_format((float)$envioPrecio, 2, '.', '');
                    $credipuntos = number_format((float)$credipuntos, 2, '.', '');

                    return [
                        'success' => 1,
                        'total' => $total,
                        'subtotal' => number_format((float)$resultado, 2, '.', ''),
                        'envio' => $envioPrecio,
                        'direccion' => $direccion,
                        'btncupon' => $bntcupon,
                        'credipuntos' => $credipuntos, // lo que tiene el cliente
                        'faltacredito' => $faltacredito, // 0: no falta, 1: si falta
                    ];

                }else{
                    // no tiene carrito de compras
                    return ['success' => 2];
                }
            }catch(\Error $e){
                return ['success' => 3];
            }
        }
    }



     // enviar la primer orden, version 2
     public function procesarOrdenEstado1V2(Request $request){
        if($request->isMethod('post')){

            // validaciones para los datos
            $reglaDatos = array(
                'userid' => 'required',
                'aplicacupon' => 'required',
                'metodo' => 'required'
            );

            $mensajeDatos = array(
                'userid.required' => 'El id del usuario es requerido.',
                'aplicacupon.required' => 'Aplica cupon es requerido.',
                'metodo.required' => 'Metodo de pago es requerido'
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

                    $dataUser = User::where('id', $request->userid)->first();

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

                    // PARA EXTRANJEROS LIMITAR HORARIO SEGUN DIRECCION

                    // verificar si es direccion extranjero, para evitar que ordene por horario tarde
                    $area = User::where('id', $dataUser->id)->pluck('area')->first();
                    if(AreasPermitidas::where('areas', $area)->first()){
                        // no hacer nada
                    }else{
                        // obtener direccion seleccionada

                        $data1 = DB::table('direccion_usuario')
                            ->where('user_id', $dataUser->id)
                            ->where('seleccionado', 1)
                            ->where('hora_inicio', '<=', $hora)
                            ->where('hora_fin', '>=', $hora)
                            ->get();

                        // verificar

                        if(count($data1) >= 1){
                            $horaDelivery = 1; // abierto
                        }else{
                            $horaDelivery = 0; // cerrado
                        }
                    }


                    // saver si el usuario esta activo
                    $usuarioActivo = $dataUser->activo;

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


                    //****** HOY VERIFICAR SI NO ES UN AREA PERMITIDA, PARA APLICAR CARGO DE ENVIO     ***********/
                    if(!AreasPermitidas::where('areas', $dataUser->area)->first()){
                        $dataDir = Direccion::where('user_id', $request->userid)->where('seleccionado', 1)->first();

                        $tipocargo = 5; // compro un extranjero
                        $envioPrecio = $dataDir->precio_envio;
                        $gananciamotorista = $dataDir->ganancia_motorista;

                    }

                    //****** CUPONES *********/

                    // ya verificado que cupon es valido y exista, ingresar registros
                    // setear precio envio si es cupon envio gratis, o el de descuento dinero

                    $resultadoCupon = $resultado; // el sub total que se descontara al monedero

                    if($request->aplicacupon == 1){
                        if($ccs = Cupones::where('texto_cupon', $request->cupon)->first()){

                            if($ccs->tipo_cupon_id == 1){
                                $envioPrecio = 0;
                            }
                            else if($ccs->tipo_cupon_id == 2){ // dinero $$

                                if($cdd = CuponDescuentoDinero::where('cupones_id', $ccs->id)->first()){
                                    if($cdd->aplica_envio_gratis){
                                       $envioPrecio = 0;
                                    }

                                    $resultadoCupon = $resultadoCupon - $cdd->dinero;
                                    if($resultadoCupon <= 0){
                                        $resultadoCupon = 0;
                                    }
                                }
                            }
                            else if($ccs->tipo_cupon_id == 3){ // descuento %

                                if($cdd = CuponDescuentoPorcentaje::where('cupones_id', $ccs->id)->first()){

                                    $resta1 = $resultado * ($cdd->porcentaje / 100);
                                    $resultadoCupon = $resultado - $resta1;

                                    if($resultadoCupon <= 0){
                                        $resultadoCupon = 0;
                                    }
                                }

                            }else if($ccs->tipo_cupon_id == 5){ // donacion dinero

                                if($cd = CuponDonacion::where('cupones_id', $ccs->id)->first()){
                                    $resultadoCupon = $resultadoCupon + $cd->dinero;
                                }
                            }
                        }
                    }

                    $tipopago = 0; // efectivo
                    if($request->metodo == 1){
                        $tipopago = 1; // credi puntos
                    }

                    $descontado = 0; // variable aqui, porque abajo solicito credito que va a quedar
                    //** SUMAR SUB TOTAL + CARGO DE ENVIO */
                    // PREGUNTAR SI PAGARA CON CREDIPUNTOS
                    if($request->metodo == 1){
                        $crediusuario = $dataUser->monedero;
                        $suma1 = $resultadoCupon + $envioPrecio; // aqui ya viene aplicado cualquier cupon

                        if($crediusuario >= $suma1){
                            $descontado = $crediusuario - $suma1;
                            if($descontado <= 0){
                                $descontado = 0;
                            }
                            // puedo comprar, descontar credito
                            User::where('id', $request->userid)->update(['monedero' => $descontado]);
                        }else{
                            // no puedo comprar, credito insuficiente
                            $crediusuario = number_format((float)$crediusuario, 2, '.', '');
                            return ['success' => 24, 'total' => $crediusuario];
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
                        'pago_a_propi' => $pagoPropi,
                        'tipo_pago' => $tipopago
                        ]
                    );


                    // guardar registro de los cupones unicamente
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

                        $descontado = number_format((float)$descontado, 2, '.', '');

                    return ['success' => 14, 'total' => $descontado];

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

    // ver informacion del encargo
    public function verInformacionDelEncargo(Request $request){

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

            if($uu = User::where('id', $request->userid)->first()){

                try {

                    // preguntar si usuario ya tiene un carrito de encargo
                    if($cart = CarritoEncargo::where('users_id', $request->userid)->first()){

                        $producto = DB::table('producto_categoria_negocio AS p')
                        ->join('carrito_encargo_pro AS c', 'c.producto_cate_nego_id', '=', 'p.id')
                        ->select('p.id AS productoID', 'p.nombre', 'p.imagen', 'c.cantidad', 'p.precio', 'c.id AS idFila')
                        ->where('c.carrito_encargo_id', $cart->id)
                        ->get();

                        // sub total de la orden

                        foreach($producto as $p){
                            $cantidad = $p->cantidad;
                            $precio = $p->precio;
                            $multi = $cantidad * $precio;
                            $p->multiplicado = number_format((float)$multi, 2, '.', '');
                        }

                        // verificar unidades de cada producto
                        foreach ($producto as $pro) {
                            $pro->precio = number_format((float)$pro->precio, 2, '.', '');
                        }

                        $subTotal = collect($producto)->sum('multiplicado');
                        $subTotal = number_format((float)$subTotal, 2, '.', '');


                        // Preguntar que tipo de pago tiene acceso
                        $tipo = 0; // unicamente credi puntos
                        $cargoenvio = 0; // cargo de envio
                        $verificado = 0; // si es 1: direccion verificada al extranjero
                        $alcanza = 0; // si es 1: si alcanza para pagar con credito

                        $dataDir = Direccion::where('user_id', $request->userid)->where('seleccionado', 1)->first();

                        // obtener cargo de envio de un area permitida
                        if(AreasPermitidas::where('areas', $uu->area)->first()){

                            $pp = EncargosZona::where('encargos_id', $cart->encargos_id)
                            ->where('zonas_id', $cart->zonas_id )
                            ->pluck('precio_envio')
                            ->first();

                            $cargoenvio = number_format((float)$pp, 2, '.', '');
                            $tipo = 1; // 2 metodos de pago

                        }else{
                            $cargoenvio = $dataDir->precio_envio;
                        }

                        $sumado = $subTotal + $cargoenvio;

                        if($uu->monedero >= $sumado){
                            $alcanza = 1;
                        }

                        $monedero = number_format((float)$uu->monedero, 2, '.', '');
                        $total = number_format((float)$sumado, 2, '.', '');

                        $datae = Encargos::where('id', $cart->encargos_id)->first();
                        $requiere = $datae->requiere_nota;
                        $nota = $datae->nota_encargo;
                        $botonTexto = $datae->texto_boton;

                        return [
                            'success' => 1,
                            'subtotal' => $subTotal,
                            'cargo' => $cargoenvio,
                            'total' => $total,
                            'tipo' => $tipo,
                            'direccion' => $dataDir->direccion,
                            'credito' => $monedero,
                            'alcanza' => $alcanza,
                            'requiere' => $requiere,
                            'nota' => $nota,
                            'mensaje' => $botonTexto
                        ];

                    }else{
                         // no tiene carrito de compras
                        return ['success' => 2];
                    }
                }catch(\Error $e){
                    return ['success' => 4, 'error' => "dd " . $e];
                }
            }
            else{
                return ['success' => 4]; // error
            }
        }
    }

    public function verInfoCrediPuntos(Request $request){

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

            if($uu = User::where('id', $request->userid)->first()){

                $data = VersionesApp::where('id', 1)->first();

                $mensaje = "En estos momentos nos encontramos con problemas técnicos, intenta más tarde por favor.";

                if($data->activo_tarjeta == 1){
                    return ['success' => 2, 'mensaje' => $mensaje];
                }

                $permitido = 0;

                $comision = $data->comision;
                $activotarjeta = $data->activo;
                $conver = intval($comision);
                $hastafecha = "";
                // verificar a que horas agrego un registro, se puede comprar cada 24 horas
                //verificar que este cliente tenga un registro
                //TOMAR UNICAMENTE EL ESTADO 1, ES CUANDO SE COMPRA CREDITO DESDE LA API
                if($cp = CrediPuntos::where('usuario_id', $uu->id)->where('estado', 1)->latest('fecha')->first()){

                    // si tiene registro, asi que comparar si ya pago 24 horas para que pueda volver a comprar
                    $hastafecha = "Puede volver agregar Credi Puntos hasta el: ";
                    $time1 = Carbon::parse($cp->fecha);
                    $horaEstimada = $time1->addHour(24)->format('Y-m-d H:i:s');
                    $today = Carbon::now('America/El_Salvador')->format('Y-m-d H:i:s');

                    $d1 = new DateTime($horaEstimada); // sumado 24 horas
                    $d2 = new DateTime($today); // tiempo actual
                    if ($d1 >= $d2){
                        // no puede comprar credi puntos
                        $permitido = 1;
                        $hastafecha = $hastafecha . date("d-m-Y h:i A", strtotime($horaEstimada));;
                    }
                }

                return ['success' => 1, 'comision' => $comision, 'convertido' => $conver,
                 'credito' => $uu->monedero, 'activo' => $activotarjeta, 'tipo' => $permitido,
                  'activo_tarjeta' => $uu->activo_tarjeta, 'hasta' => $hastafecha];
            }
        }
    }

    // INGRESO DE CREDI PUNTOS
    public function ingresarCrediPuntosCliente(Request $request){

       if($request->isMethod('post')){
            $reglaDatos = array(
                'userid' => 'required',
                'comprar' => 'required',
                'nombre' => 'required',
                'numero' => 'required',
                'mes' => 'required',
                'anio' => 'required',
                'cvv' => 'required'
            );

            $mensajeDatos = array(
                'userid.required' => 'El id del usuario es requerido.',
                'comprar.required' => 'El monto es requerido.',
                'nombre.required' => 'El nombre es requerido.',
                'numero.required' => 'El numero es requerido.',
                'mes.required' => 'El id mes es requerido.',
                'anio.required' => 'El anio es requerido.',
                'cvv.required' => 'El cvv es requerido.',
                );
            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );
            if($validarDatos->fails())
            {
                return [
                    'success' => 0,
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($uu = User::where('id', $request->userid)->first()){

                $dataversion = VersionesApp::where('id', 1)->first();

                $time1 = Carbon::parse($dataversion->fecha_token);
                $horaEstimada = $time1->addMinute(50)->format('Y-m-d H:i:s'); // agregar 50 minutos
                $today = Carbon::now('America/El_Salvador')->format('Y-m-d H:i:s');

                $d1 = new DateTime($horaEstimada);
                $d2 = new DateTime($today);

                $comision = $dataversion->comision;

                $resultado = ($comision * $request->comprar) / 100;
                $pagara = $request->comprar + $resultado;

                $data = array (
                    'tarjetaCreditoDebido' =>
                    array (
                        'numeroTarjeta' => $request->numero,
                        'cvv' => $request->cvv,
                        'mesVencimiento' => $request->mes,
                        'anioVencimiento' => $request->anio,
                    ),
                    'monto' => $pagara,
                    'emailCliente' => $uu->email,
                    'nombreCliente' => $request->nombre,
                    "formaPago" => "PagoNormal",
                );

                $convertido = json_encode($data);

                $tokenactual = $dataversion->token_wompi;

                DB::beginTransaction();

                try {

                    if ($d1 > $d2){
                        // hay tiempo de token, generar compra

                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api.wompi.sv/TransaccionCompra",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_POSTFIELDS => $convertido,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_HTTPHEADER => array(
                            "authorization: Bearer $tokenactual",
                            "content-type: application/json"
                        ),
                        ));

                        $response = curl_exec($curl);
                        $err = curl_error($curl);
                        $code = curl_getinfo($curl);
                        curl_close($curl);
                        if ($err) {
                           return ['success' => 1]; // problema de peticion
                        }else {
                            if(empty($response)){
                                return ['success' => 2]; // problemas internos
                            }

                            if($code["http_code"] == 200){ // peticion correcta
                                $arrayjson = json_decode($response,true);

                                $idtransaccion = $arrayjson["idTransaccion"]; // guardar, string
                                $esreal = $arrayjson["esReal"]; // guardar, bool
                                $esaprobada = $arrayjson["esAprobada"]; // guardar, bool
                                $monto = $arrayjson["monto"]; // decimal
                                $codigo = $arrayjson["codigoAutorizacion"];

                                if($esaprobada == false){
                                    return ['success' => 5]; // reprobada, no pudo ser efectuada
                                }

                                $fechahoy = Carbon::now('America/El_Salvador');

                                // guardar datos
                                $reg = new CrediPuntos;
                                $reg->usuario_id = $uu->id;
                                $reg->credi_puntos = $request->comprar;
                                $reg->pago_total = $pagara; // lo que pago al final
                                $reg->fecha = $fechahoy;
                                $reg->nota = "";
                                $reg->idtransaccion = $idtransaccion;
                                $reg->codigo = $codigo;
                                $reg->esreal = (int)$esreal;
                                $reg->esaprobada = (int)$esaprobada;
                                $reg->comision = $comision;
                                $reg->revisada = 0;
                                //$reg->estado = 1; // por ingreso api
                                $reg->save();
                                DB::commit();

                                return ['success' => 3]; // compra exitosa
                            }else{
                                // revisar los datos de su tarjeta
                                return ['success' => 4];
                            }
                        }

                    }else{

                        // supero tiempo
                        // generar token nuevo
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://id.wompi.sv/connect/token",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => "grant_type=client_credentials&client_id=b91a76c6-4936-47cc-a2d6-152d0908c826&client_secret=2bcd13c6-0561-40d7-92ad-a3d4b3c043f4&audience=wompi_api",
                            CURLOPT_HTTPHEADER => array(
                                "content-type: application/x-www-form-urlencoded"
                            ),
                        ));

                        $response = curl_exec($curl);
                        $err = curl_error($curl);

                        curl_close($curl);

                        if ($err) {
                            return ['success' => 1]; // error al obtener token
                        } else {
                            $jsonArray = json_decode($response,true);
                            $key = "access_token";
                            $tokennuevo = $jsonArray[$key];
                            $fechahoy = Carbon::now('America/El_Salvador');
                            // guardar token nuevo
                            VersionesApp::where('id', 1)->update(['token_wompi' => $tokennuevo, 'fecha_token' => $fechahoy]);
                            DB::commit();
                            // GENERAR COMPRA

                            $curl = curl_init();

                            curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://api.wompi.sv/TransaccionCompra",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_POSTFIELDS => $convertido,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_HTTPHEADER => array(
                                "authorization: Bearer $tokennuevo",
                                "content-type: application/json"
                            ),
                            ));

                            $response = curl_exec($curl);
                            $err = curl_error($curl);
                            $code = curl_getinfo($curl);
                            curl_close($curl);
                            if ($err) {
                               return ['success' => 1]; // problema realizar cobro
                            }else {
                                if(empty($response)){
                                    return ['success' => 2]; // problemas
                                }

                                if($code["http_code"] == 200){ // peticion correcta
                                    $arrayjson = json_decode($response,true);

                                    $idtransaccion = $arrayjson["idTransaccion"]; // guardar, string
                                    $codigo = $arrayjson["codigoAutorizacion"];
                                    $esreal = $arrayjson["esReal"]; // guardar, bool
                                    $esaprobada = $arrayjson["esAprobada"]; // guardar, bool
                                    $monto = $arrayjson["monto"]; // decimal

                                    if($esaprobada == false){
                                        return ['success' => 5]; // reprobada, no pudo ser efectuada
                                    }

                                    $fechahoy = Carbon::now('America/El_Salvador');

                                    // guardar datos
                                    $reg = new CrediPuntos;
                                    $reg->usuario_id = $uu->id;
                                    $reg->credi_puntos = $request->comprar;
                                    $reg->pago_total = $monto;
                                    $reg->fecha = $fechahoy;
                                    $reg->nota = "";
                                    $reg->idtransaccion = $idtransaccion;
                                    $reg->codigo = $codigo;
                                    $reg->esreal = (int)$esreal;
                                    $reg->esaprobada = (int)$esaprobada;
                                    $reg->comision = $comision;
                                    $reg->revisada = 0;
                                    //$reg->estado = 1; // por ingreso api
                                    $reg->save();
                                    DB::commit();

                                    return ['success' => 3]; // compra exitosa
                                }else{
                                    // revisar los datos de su tarjeta
                                    return ['success' => 4];
                                }
                            }

                        }
                    }

                } catch(\Throwable $e){
                    DB::rollback();

                    // error
                    return [
                        'success' => 5
                    ];
                }
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
