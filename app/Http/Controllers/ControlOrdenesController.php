<?php

namespace App\Http\Controllers;

use App\CrediPuntos;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Ordenes;
use App\OrdenesDirecciones;
use App\OrdenesDescripcion;
use App\Motoristas;
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
use App\Zonas;
use OneSignal;
use Illuminate\Support\Facades\DB;
use App\Propietarios;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\User;
use Exception;
use App\Admin;
use Auth;
use App\Administradores;
use App\Producto;


class ControlOrdenesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

     // lista de ordenes
     public function indexHoy(){

        $fecha = Carbon::now('America/El_Salvador');
        $orden = DB::table('ordenes')
        ->where('estado_7', 1) // unicamente completadas
        ->whereDate('fecha_orden', $fecha)
        ->get();

        $total = 0.00;
        $total = collect($orden)->sum('precio_total');
        $total = number_format((float)$total, 2, '.', '');

        return view('backend.paginas.ordenes.listaordenhoy', compact('fecha', 'total'));
    }

    public function indexProductosHoy($id){ // id de orden
        return view('backend.paginas.ordenes.listaordenhoyproductos', compact('id'));
    }

    public function tablaProductosHoy($id){

        // obtener todos los productos
        $lista = OrdenesDescripcion::where('ordenes_id', $id)->orderBy('id', 'ASC')->get();

        foreach($lista as $l){

            $dato = Producto::where('id', $l->producto_id)->first();
            $nombre = $dato->nombre;
            $l->nombre = $nombre;
            $l->imagen = $dato->imagen;

            $l->total = number_format((float)$l->cantidad * $l->precio, 2, '.', '');
        }

        return view('backend.paginas.ordenes.tablas.tablaordenhoyproducto', compact('lista'));
    }

    public function indexNotiCliente(){

        $zonas = Zonas::all();

        return view('backend.paginas.notificacion.listanotificacionzona', compact('zonas'));
    }

    // tabla de lista de ordenes, ultimas 100
    public function tablaHoy(){

        $fecha = Carbon::now('America/El_Salvador');

        $orden = DB::table('ordenes AS o')
        ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
        ->select('o.id', 's.identificador', 'o.fecha_orden', 's.nombre', 'o.precio_total',
            'o.estado_2', 'o.estado_3', 'o.estado_4', 'o.estado_5', 'o.estado_6',
            'o.estado_7', 'o.estado_8', 'o.users_id', 'o.tipo_pago', 'o.nota_orden', 'o.precio_envio')
        ->whereDate('o.fecha_orden', $fecha)
        ->get();

        $estado = "";
        foreach($orden as $o){
            $o->fecha_orden = date("h:i A", strtotime($o->fecha_orden));

            $od = OrdenesDirecciones::where('ordenes_id', $o->id)->first();
            $o->zonaidenti = Zonas::where('id', $od->zonas_id)->pluck('identificador')->first();

            $verificado = "No. ";

            if($o->tipo_pago == 1){ // credi puntos
                $metodopago = " | Pagar con Credi Puntos";
            }else{
                $metodopago = " | Pagar con Efectivo";
            }

            if($od->revisado == 1){
                $verificado = "Si. ";
            }

            $area = User::where('id', $od->users_id)->pluck('area')->first();

            $cliente = $od->nombre . " | Área: " . $area;
            $nombre = $od->nombre . " | Área: " . $area;
            if($mm = MotoristaExperiencia::where('ordenes_id', $o->id)->first()){
                $cliente = $nombre . " | Califico: " . $mm->experiencia . " | " . $mm->mensaje;
            }

            $o->cliente = $cliente;

            $motorista = "";
            if($mo = MotoristaOrdenes::where('ordenes_id', $o->id)->first()){
                $motorista = Motoristas::where('id', $mo->motoristas_id)->pluck('nombre')->first();
            }
            $o->motorista = $motorista;

            $o->verificado = $verificado . $metodopago;

            if($o->estado_2 == 0){
                $estado = "Orden sin contestacion del propietario";
            }

            if($o->estado_2 == 1){
                $estado = "Orden contestada, esperando contestacion del cliente";
            }

            if($o->estado_3 == 1){
                $estado = "Orden contestada por cliente, esperando iniciar orden";
            }

            if($o->estado_4 == 1){
                $estado = "Orden inicio preparacion";
            }

            if($o->estado_5 == 1){
                $estado = "Orden termino prepararse";
            }

            if($o->estado_6 == 1){
                $estado = "Motorista va en camino";
            }

            if($o->estado_7 == 1){
                $estado = "Motorista completo la orden";
            }

            if($o->estado_8 == 1){
                $estado = "Orden cancelada";
            }

            $o->estado = $estado;


            // CUPONES
            $cupon = "";
            // buscar si aplico cupon
            if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()){

                // buscar tipo de cupon
                $tipo = Cupones::where('id', $oc->cupones_id)->first();

                // ver que tipo se aplico
                // el precio envio ya esta modificado
                if($tipo->tipo_cupon_id == 1){

                    $cupon = $tipo->texto_cupon . "| Envío Gratis";

                }else if($tipo->tipo_cupon_id == 2){

                    // modificar precio
                    $data = AplicaCuponDos::where('ordenes_id', $o->id)->first();

                    if($data->aplico_envio_gratis == 1){
                        $cupon = $tipo->texto_cupon . " | Descuento de: $" . $data->dinero . " Con Envío Gratis";
                    }else{
                        $cupon = $tipo->texto_cupon . " | Descuento de: $" . $data->dinero;
                    }


                }else if($tipo->tipo_cupon_id == 3){

                    $porcentaje = AplicaCuponTres::where('ordenes_id', $o->id)->pluck('porcentaje')->first();

                    $cupon = $tipo->texto_cupon . " | Rebaja de: " . $porcentaje . "%";


                }else if($tipo->tipo_cupon_id == 4){

                    $producto = AplicaCuponCuatro::where('ordenes_id', $o->id)->pluck('producto')->first();

                    $o->producto = $producto;

                    $cupon = $tipo->texto_cupon . " | Producto Gratis: " . $producto;

                }
                else if($tipo->tipo_cupon_id == 5){

                    // sumar sub total + envio + donacion
                    $data = AplicaCuponCinco::where('ordenes_id', $o->id)->first();
                    $ins = Instituciones::where('id', $data->instituciones_id)->pluck('nombre')->first();

                    $cupon = $tipo->texto_cupon . " | Donación de: $" . $data->dinero . " A: " . $ins;

                }

            }

            $o->cupon = $cupon;
        }

        return view('backend.paginas.ordenes.tablas.tablaordenhoy', compact('orden'));
    }

    // INFORMACION DEL CREDITO QUE GASTO, SI UTILIZO CUPON O NO
    public function infoCreditoGastado(Request $request){

        if($request->isMethod('post')){

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'
            );

            $mensajeDatos = array(
                'id.required' => 'Device id es requerido.'
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails())
            {
                return [
                    'success' => 0,
                    'message' => $validarDatos->errors()->all()
                ];
            }

            // verificar si utilizo cupon

            if($datos = Ordenes::where('id', $request->id)->first()){

                $final = $datos->precio_total + $datos->precio_envio;

                // buscar si aplico cupon
                if($oc = OrdenesCupones::where('ordenes_id', $request->id)->first()){
    
                    // buscar tipo de cupon
                    $tipo = Cupones::where('id', $oc->cupones_id)->first();
    
                    // ver que tipo se aplico
                    // el precio envio ya esta modificado
                    if($tipo->tipo_cupon_id == 1){
    
                        // NO HACER NADA
    
                    }else if($tipo->tipo_cupon_id == 2){
    
                        // modificar precio
                        $data = AplicaCuponDos::where('ordenes_id', $request->id)->first();

                        $total = $datos->precio_total - $data->dinero;
                        if($total <= 0){
                            $total = 0;
                        }

                        $final = $total + $datos->precio_envio; // si aplico envio gratis, este sera $0.00
    
                    }else if($tipo->tipo_cupon_id == 3){
    
                        $porcentaje = AplicaCuponTres::where('ordenes_id', $request->id)->pluck('porcentaje')->first();
                        $resta = $datos->precio_total * ($porcentaje / 100);
                        $total = $datos->precio_total - $resta;
    
                        if($total <= 0){
                            $total = 0;
                        }
    
                        $final = $total + $datos->precio_envio;       
    
                    }else if($tipo->tipo_cupon_id == 4){
    
                       // NO HACER NADA                       
    
                    }
                    else if($tipo->tipo_cupon_id == 5){
    
                        // sumar sub total + envio + donacion
                        $data = AplicaCuponCinco::where('ordenes_id', $request->id)->first();
                        $ins = Instituciones::where('id', $data->instituciones_id)->pluck('nombre')->first();
    
                      
    
                        $total = $datos->precio_total + $datos->precio_envio;
                        $final = $total + $data->dinero;                        
    
                    }
    
                }

                $final = number_format((float)$final, 2, '.', '');
                return ['success' => 1, 'credito' => $final];

            }else{
                return ['success' => 2];
            }

        }
    }



    // ver las ventas de hoy fecha
    public function totalVentasHoy(Request $request){

        $fecha = Carbon::now('America/El_Salvador');

        $orden = DB::table('ordenes')
        ->where('estado_7', 1) // unicamente completadas
        ->whereDate('fecha_orden', $fecha)
        ->get();

        $total = 0.00;
        $total = collect($orden)->sum('precio_total');
        $total = number_format((float)$total, 2, '.', '');

        return ['success' => 1, 'total' => $total];
    }

    // index notificaciones
    public function indexNotificacion(){

        $motoristas = Motoristas::all();
        $administradores = Administradores::all();

        return view('backend.paginas.notificacion.listanotificacion', compact('motoristas', 'administradores'));
    }

    public function tablaPropiNoti($id){
        // viene el identificador del servicio

        $noti = DB::table('servicios AS s')
        ->join('propietarios AS p', 'p.servicios_id', '=', 's.id')
        ->select('p.id', 'p.telefono', 'p.disponibilidad', 'p.activo', 'p.device_id')
        ->where('s.identificador', $id)
        ->get();

        foreach($noti as $n){

            $activo = "";
            $disponibilidad = "";

            if($n->disponibilidad == 1){
                $disponibilidad = "Si";
            }else{
                $disponibilidad = "No";
            }

            if($n->activo == 1){
                $activo = "Si";
            }else{
                $activo = "No";
            }

            $n->activo = $activo;
            $n->disponibilidad = $disponibilidad;
        }

        return view('backend.paginas.notificacion.tablas.tablapropinoti', compact('noti'));
    }

    public function enviarNotiPropi(Request $request){
        if($request->isMethod('post')){

            // validaciones para los datos
            $reglaDatos = array(
                'device' => 'required',
                'titulo' => 'required',
                'mensaje' => 'required'
            );

            $mensajeDatos = array(
                'device.required' => 'Device id es requerido.',
                'titulo.required' => 'Titulo es requerido',
                'mensaje.required' => 'Mensaje es requerido'
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
                $this->envioNoticacionPropietario($request->titulo, $request->mensaje, $request->device);
                } catch (Exception $e) {}

            return ['success' => 1];
        }
    }

    // envio de notificacion motorista
    public function envarNotificacionMotorista(Request $request){
        if($request->isMethod('post')){

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required',
                'titulo' => 'required',
                'descripcion' => 'required'
            );

            $mensajeDatos = array(
                'id.required' => 'Device id es requerido.',
                'titulo.required' => 'Titulo es requerido',
                'descripcion.required' => 'Descripcion es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails())
            {
                return [
                    'success' => 0,
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($m = Motoristas::where('id', $request->id)->first()){

                if($m->device_id == "0000" || $m->device_id == null){

                    return ['success' => 2];

                }else{

                    try {
                        $this->envioNoticacionMotorista($request->titulo, $request->descripcion, $m->device_id);
                    } catch (Exception $e) {}


                    return ['success' => 1]; // enviado

                }
            }else{
                return ['success' => 3]; // motorista no encontrado
            }
        }
    }

    // envio de notificacion administrador
    public function envarNotificacionAdministradores(Request $request){
        if($request->isMethod('post')){

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required',
                'titulo' => 'required',
                'descripcion' => 'required'
            );

            $mensajeDatos = array(
                'id.required' => 'Device id es requerido.',
                'titulo.required' => 'Titulo es requerido',
                'descripcion.required' => 'Descripcion es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails())
            {
                return [
                    'success' => 0,
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($m = Administradores::where('id', $request->id)->first()){

                if($m->device_id == "0000" || $m->device_id == null){

                    return ['success' => 2];

                }else{

                    try {
                        $this->envioNoticacionAdministrador($request->titulo, $request->descripcion, $m->device_id);
                    } catch (Exception $e) {}

                    return ['success' => 1]; // enviado
                }
            }else{
                return ['success' => 3]; // motorista no encontrado
            }
        }
    }


    public function devicePropietario(Request $request){
        if($request->isMethod('post')){

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'
            );

            $mensajeDatos = array(
                'id.required' => 'Device id es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails())
            {
                return [
                    'success' => 0,
                    'message' => $validarDatos->errors()->all()
                ];
            }


            $device = Propietarios::where('id', $request->id)->pluck('device_id')->first();

            return ['success' => 1, 'device' => $device];
        }
    }


    public function buscarClientes(Request $request){
        $info = DB::table('users')
        ->whereIn('zonas_id', $request->idzonas)
        ->count();

        return ['success' => 1, 'info' => $info];
    }

    public function EnviarNotiClientesZonas(Request $request){

        if($request->isMethod('post')){

            // validaciones para los datos
            $reglaDatos = array(
                'titulo' => 'required',
                'mensaje' => 'required'
            );

            $mensajeDatos = array(
                'titulo.required' => 'Titulo es requerido',
                'mensaje.required' => 'Mensaje es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails())
            {
                return [
                    'success' => 0,
                    'message' => $validarDatos->errors()->all()
                ];
            }

            $info = DB::table('users')
            ->whereIn('zonas_id', $request->idzonas)
            ->get();

            $pila = array();
            foreach($info as $m){

                if($m->activo == 1){ // usuarios activos
                    if(!empty($m->device_id)){
                        //EVITAR LOS NUEVOS REGISTRADOS
                        if($m->device_id != "0000"){
                            array_push($pila, $m->device_id);
                        }
                    }
                }
            }

            // comparar clave
            $password = Auth::user()->password;

            if (Hash::check($request->clave, $password)) {
                if(!empty($pila)){
                    try {
                        $this->envioNoticacionCliente($request->titulo, $request->mensaje, $pila);
                    } catch (Exception $e) {}
                }

                return ['success' => 1, 'info' => $pila];
            }else{
                return ['success' => 2];
            }
        }
    }

    public function buscarCliente(Request $request){

        if($request->isMethod('post')){

            // validaciones para los datos
            $reglaDatos = array(
                'numero' => 'required'
            );

            $mensajeDatos = array(
                'numero.required' => 'Numero es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails())
            {
                return [
                    'success' => 0,
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($dato = User::where('phone', $request->numero)->first()){

                return ['success' => 1, 'nombre' => $dato->name];
            }else{
                return ['success' => 2];
            }
        }
    }


    public function enviarNotiIndividual(Request $request){
        if($request->isMethod('post')){

            // validaciones para los datos
            $reglaDatos = array(
                'titulo' => 'required',
                'mensaje' => 'required',
                'numero' => 'required'
            );

            $mensajeDatos = array(
                'titulo.required' => 'Titulo es requerido',
                'mensaje.required' => 'Mensaje es requerido',
                'numero.required' => 'Numero es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails())
            {
                return [
                    'success' => 0,
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($dato = User::where('phone', $request->numero)->first()){

                if($dato->device_id != "0000"){
                    if($dato->activo == 1){
                        try {
                            $this->envioNoticacionCliente($request->titulo, $request->mensaje, $dato->device_id);
                        } catch (Exception $e) {}

                        return ['success' => 1];
                    }else{
                        return ['success' => 2]; // no esta activo
                    }
                }else{
                    return ['success' => 3]; // id es 0000
                }

            }else{
                return ['success' => 4]; // no encontrado
            }
        }
    }



    public function envioNoticacionPropietario($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionPropietario($titulo, $mensaje, $pilaUsuarios);
    }


    public function envioNoticacionCliente($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionCliente($titulo, $mensaje, $pilaUsuarios);
    }

    public function envioNoticacionMotorista($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionMotorista($titulo, $mensaje, $pilaUsuarios);
    }

    public function envioNoticacionAdministrador($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionAdministrador($titulo, $mensaje, $pilaUsuarios);
    }


}
