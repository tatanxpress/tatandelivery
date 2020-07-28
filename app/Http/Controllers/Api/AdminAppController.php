<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Propietarios;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\MotoristaOrdenes;
use App\Motoristas;
use App\Ordenes;
use App\OrdenesDirecciones;
use App\PagoPropietario;
use App\Producto;
use App\Servicios;
use App\User;
use Carbon\Carbon;
use App\Administradores;
use App\OrdenesPendiente;
use App\OrdenesUrgentes;
use App\OrdenesPendienteContestar;
use App\Zonas;
use App\OrdenesUrgentesDos;
use App\OrdenesUrgentesTres;
use App\OrdenesUrgentesCuatro;
use App\MotoristaExperiencia;
use App\EncargoAsignadoServicio;
use App\OrdenesEncargoDireccion;
use App\MotoristaOrdenEncargo;
use App\OrdenesEncargo;
use App\OrdenesCupones;
use App\Cupones;
use OneSignal;
use App\AplicaCuponCuatro;
use App\AplicaCuponTres;
use App\AplicaCuponDos; 
use App\AplicaCuponCinco;

class AdminAppController extends Controller
{
    // login
    public function loginRevisador(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'phone' => 'required',
                'password' => 'required|max:16',
            );

            $messages = array(                                      
                'phone.required' => 'El telefono es requerido.',
                
                'password.required' => 'La contraseña es requerida.',
                'password.max' => '16 caracteres máximo para contraseña',
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
         
            if($p = Administradores::where('telefono', $request->phone)->first()){

                if($p->activo == 0){
                    return ['success' => 1]; // revisador no activo
                }

                if (Hash::check($request->password, $p->password)) {

                    $id = $p->id;   
                    if($request->device_id != null){
                        Administradores::where('id', $p->id)->update(['device_id' => $request->device_id]);
                    }

                    return ['success' => 2, 'usuario_id' => $id]; // login correcto
                }    else{
                    return ['success' => 3]; // contraseña incorrecta
                }
            }else{
                return ['success' => 4]; // datos incorrectos
            }
        }
    }

    // reseteo de password
    public function reseteo(Request $request){
        if($request->isMethod('post')){   
            
            $regla = array(
                'id' => 'required',
                'password' => 'required'
            );

            $mensaje = array(
                'id.required' => 'id es requerido',
                'password.required' => 'password es requerida'
                );
                
            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }  

            if(Administradores::where('id', $request->id)->first()){
               
                Administradores::where('id', $request->id)->update([
                        'password' => bcrypt($request->password)
                    ]);

                        return ['success' => 1];
            
            }else{
                return ['success' => 2];  
            }
        }  
    }

    
    //*** ver solo ordenes de HOY */

    public function ordenesHoy(Request $request){

        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required' 
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.'                
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            } 

            if($aa = Administradores::where('id', $request->id)->first()){

                if($aa->activo == 0){
                    return ['success' => 1]; // desactivado
                }
             
                $fecha = Carbon::now('America/El_Salvador');

                $orden = DB::table('ordenes AS o')
                ->join('servicios AS s', 's.id', '=', 'o.servicios_id')       
                ->select('o.id', 's.identificador', 'o.fecha_orden', 's.nombre',
                    'o.estado_2', 'o.hora_2', 'o.estado_3', 'o.estado_4', 'o.fecha_4', 
                    'o.estado_5', 'o.estado_6', 'o.estado_7', 'o.estado_8', 'o.users_id',
                    'o.mensaje_8', 'o.cancelado_cliente', 'o.cancelado_propietario',
                    'o.fecha_8', 'o.pago_a_propi', 'o.precio_envio', 'o.precio_total')
                ->whereDate('o.fecha_orden', $fecha)
                ->orderBy('o.id', 'DESC')
                ->get();
      
                $estado = "";
                foreach($orden as $o){        
                    $o->fecha_orden = date("h:i A", strtotime($o->fecha_orden));
    
                    $od = OrdenesDirecciones::where('ordenes_id', $o->id)->first();
                    $o->zonanombre = Zonas::where('id', $od->zonas_id)->pluck('nombre')->first();
    
                    $estado4 = 0;
                    if($o->estado_4 == 1){
                        $estado4 = 1;
                          // estimada entrega cliente
                        $time2 = Carbon::parse($o->fecha_4);
                        $suma = $o->hora_2 + $od->copia_tiempo_orden;
                        $tiempoCliente = $time2->addMinute($suma)->format('h:i A d-m-Y');
                        $o->horaestimadacliente = $tiempoCliente;  
                    }

                    $cupon = "";
                    $pagaapropi = "";

                    if($o->pago_a_propi == 1){
                        $pagaapropi = "Orden se paga a propietario $".$o->precio_total;
                    }

                    $o->pagaapropi = $pagaapropi;

                    $envio = $o->precio_envio;
                    $o->subtotal = number_format((float)$o->precio_total, 2, '.', '');
                    
                    // verificar si utilizo algun cupon
                    if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()){
                        $tipo = Cupones::where('id', $oc->cupones_id)->first();

                        if($tipo->tipo_cupon_id == 1){
                            $cupon = "Envio Gratis (si aplicado al envio)";
                            $envio = 0;
                        }else if($tipo->tipo_cupon_id == 2){
                            $ac = AplicaCuponDos::where('ordenes_id', $o->id)->first();
                            $descuento = $ac->dinero;                            

                            if($ac->aplico_envio_gratis == 1){
                                $envio = 0;
                                $cupon = "Descuento Dinero de: $" . $descuento . " (no aplicado a sub total) + envio gratis (si aplicado al envio)";
                            }else{
                                $cupon = "Descuento Dinero de: $" . $descuento . " (no aplicado a sub total)";
                            }

                        }else if($tipo->tipo_cupon_id == 3){

                            $ac = AplicaCuponTres::where('ordenes_id', $o->id)->first();
                            $porcentaje = $ac->porcentaje;

                            $cupon = "Descuento Porcentaje de: " . $porcentaje . "% (no aplicado a subtotal)";
                        }
                        else if($tipo->tipo_cupon_id == 4){
                            $ac = AplicaCuponCuatro::where('ordenes_id', $o->id)->first();
                            $producto = $ac->producto;

                            $cupon = "Producto Gratis: " . $producto;
                        }
                        else if($tipo->tipo_cupon_id == 5){

                            $ac = AplicaCuponCinco::where('ordenes_id', $o->id)->first();
                            $dinero = $ac->dinero;

                            $cupon = "Donación de: $".$dinero . " (no sumado a subtotal)";
                        }
                    } 

                  
                    $o->envio = number_format((float)$envio, 2, '.', '');

                    $o->cupon = $cupon;

                    $o->estado4 = $estado4;

                    $nombremotorista = "";
    
                    if($motorista = MotoristaOrdenes::where('ordenes_id', $o->id)->first()){
    
                        $n = Motoristas::where('id', $motorista->motoristas_id)->first();
                        $nombremotorista = $n->nombre;
                    }
    
                    $o->motorista = $nombremotorista;
                    
                    if($o->estado_2 == 0){
                        $estado = "Orden sin contestacion del propietario";
                    }
    
                    if($o->estado_2 == 1){
                        $estado = "Orden contestada, esperando contestacion del cliente";
                    }
    
                    if($o->estado_3 == 1){
                        $estado = "Orden contestada por cliente, esperando iniciar orden";
                    }
    
                    $horaestimada = ""; // a esta hora estara la orden preparada
    
                    if($o->estado_4 == 1){
                        $estado = "Orden inicio preparacion";
    
                        $time1 = Carbon::parse($o->fecha_4);                                 
                        $horaestimada = $time1->addMinute($o->hora_2)->format('h:i A');                
                    }
    
                    $o->horaestimada = $horaestimada;
    
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

                    $phone = User::where('id', $o->users_id)->pluck('phone')->first();

                    // datos del cliente   $od
                    $o->nombrecliente = $od->nombre;
                    $o->telcliente = $phone;
                    $o->direccioncliente = $od->direccion;
                    $o->numerocasa = $od->numero_casa;
                    $o->puntoreferencia = $od->punto_referencia;

                    $o->latitud = $od->latitud;
                    $o->longitud = $od->longitud;
                    $o->latitudreal = $od->latitud_real;
                    $o->longitudreal = $od->longitud_real;


                    $comentario = "";

                    if($m = MotoristaExperiencia::where('ordenes_id', $o->id)->first()){
                        $mensaje = "";
                        if($m->mensaje != "-"){
                            $mensaje = $m->mensaje;
                        }

                        $comentario = "Calificó con: " . $m->experiencia . " Puntos y mensaje es: " . $mensaje;
                        
                    }else{
                        $comentario = "Sin calificar";
                    }

                    $o->comentario = $comentario;
                    
                    // quien cancelo
                    $mensajeCancelado = "";

                    if($o->estado_8 == 1){

                        $hora = date("h:i A", strtotime($o->fecha_8));

                        if($o->cancelado_cliente == 1){                            
                            $mensajeCancelado = "Cancelo Cliente a: " . $hora; 
                        }

                        if($o->cancelado_propietario == 1){
                            $mensajeCancelado = "Cancelo Propietario a: " . $hora . " - Mensaje: " . $o->mensaje_8; 
                        }                        
                    }

                    $o->mensajeCancelado = $mensajeCancelado;
                }
    
                return ['success' => 2, 'ordenes' => $orden];
                
            }else{
                return ['success' => 1];
            }          
        }
    }


    // ordenes encargos hoy
    public function ordenesEncargoHoy(Request $request){


        return ['success' => 2];

        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'                
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.'                
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            } 

            if($aa = Administradores::where('id', $request->id)->first()){

                $fecha = Carbon::now('America/El_Salvador');

                $orden = DB::table('encargos AS e')
                ->join('ordenes_encargo AS o', 'o.encargos_id', '=', 'e.id')       
                ->select('e.id AS idencargo', 'o.id', 'e.fecha_entrega', 'o.revisado', 'o.estado_0', 'o.fecha_0',
                            'o.estado_1', 'o.fecha_1', 'o.estado_2', 'o.fecha_2', 'o.estado_3', 'o.fecha_3',
                            'o.users_id', 'o.calificacion', 'o.mensaje', 'o.pago_a_propi', 'o.precio_subtotal',
                            'o.precio_envio')
                ->where('o.revisado', '!=', 5) // no ver cancelados
                ->whereDate('e.fecha_entrega', $fecha)                
                ->orderBy('o.id', 'DESC')
                ->get();
      
                // no iniciado, iniciado, terminado, motorista en camino, orden entregada.
                foreach($orden as $o){        
                    $o->fecha_entrega = date("h:i A", strtotime($o->fecha_entrega));

                    $pagar = "";
                    $p1 = number_format((float)$o->precio_subtotal, 2, '.', '');
                    $suma = $o->precio_subtotal + $o->precio_envio;
                    $suma = number_format((float)$suma, 2, '.', '');
                    if($o->pago_a_propi == 1){
                        
                        $pagar = "Pagar a Propietario: $" . $p1 . " Y cobrar al cliente (con envio) $" . $suma;
                    }else{
                        $pagar = "Cobrar al cliente: (Sub total + envio) $" . $suma;
                    }

                    $o->pagar = $pagar;

                    $estado = "";
                    if($o->revisado == 1){
                        $estado = "Pendiente";
                    } 
                    else if($o->revisado == 2){ // en proceso
                        $estado = "En proceso";
                        
                        if($o->estado_0 == 0){
                            $estado = "En proceso, propi no inicia la orden";
                        }else{
                            $f0 = date("h:i A", strtotime($o->fecha_0));
                            $estado = "En proceso, propi inicio orden: " . $f0;
                        }

                        if($o->estado_1 == 1){
                            $f1 = date("h:i A", strtotime($o->fecha_1));
                            $estado = "En proceso, propi finalizo orden: " . $f1;
                        }
                    }
                    else if($o->revisado == 3){
                       
                        $f2 = date("h:i A", strtotime($o->fecha_2));
                        $estado = "Motorista en camino: " . $f2;                      
                                               
                    }
                    else if($o->revisado == 4){
                        $f3 = date("h:i A", strtotime($o->fecha_3));
                        $estado = "Motorista completo: " . $f3; 
                    }else{
                        $estado = "Encargo Cancelado";
                    }

                    $o->estado = $estado;
                    
                    $servicio = "Ninguno";
                    if($ss = EncargoAsignadoServicio::where('encargos_id', $o->idencargo)->first()){
                        $servicio = Servicios::where('id', $ss->servicios_id)->pluck('nombre')->first();
                    }

                    $o->servicio = $servicio;

                    $motorista = "";
                    if($mm = MotoristaOrdenEncargo::where('ordenes_encargo_id', $o->id)->first()){
                        $motorista = Motoristas::where('id', $mm->motoristas_id)->pluck('nombre')->first();
                    }

                    $o->motorista = $motorista;
                                        
                    $data = OrdenesEncargoDireccion::where('ordenes_encargo_id', $o->id)->first();
                    $o->zona = Zonas::where('id', $data->zonas_id)->pluck('nombre')->first();                    

                    $o->cliente = $data->nombre;
                    $o->telefono = User::where('id', $o->users_id)->pluck('phone')->first();
                    $o->direccion = $data->direccion;
                    $o->numcasa = $data->numero_casa;
                    $o->puntoreferencia = $data->punto_referencia;
                    $o->latitud = $data->latitud;
                    $o->longitud = $data->longitud;
                    $o->latitudreal = $data->latitud_real;
                    $o->longitudreal = $data->longitud_real;
                    
                    $comentario = "";
                    if($o->calificacion != 0){
                        $comentario = "Califico con: " . $o->calificacion . " | mensaje es: " . $o->mensaje;
                    } 

                    $o->comentario = $comentario; 
                }
    
                return ['success' => 1, 'ordenes' => $orden];
                
            }else{
                return ['success' => 2];
            }          
        }
    }

 
    //*** ordenes_urgentes */

    // oordenes completadas, aun no sale motorista, ya paso la hora estimada de entrega que dio el propietario + 2 min extra. 
    public function verOrdenesUrgenteUno(Request $request){

        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'                
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.'                
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            } 
          
            if($p = Administradores::where('id', $request->id)->first()){
                
                if($p->activo == 0){
                    return ['success' => 1]; // revisador no activo
                }

                $orden = DB::table('ordenes_urgentes AS ou')
                ->join('ordenes AS o', 'o.id', '=', 'ou.ordenes_id')             
                ->select('o.id', 'o.users_id', 'o.servicios_id', 'o.precio_total',
                        'o.fecha_orden', 'o.hora_2', 'o.estado_4', 'o.fecha_4',
                        'o.estado_5', 'o.fecha_5', 'ou.activo', 'o.estado_8', 'ou.fecha')
                ->where('ou.activo', 1)
                ->orderBy('ou.id', 'ASC')
                ->get();
 
                foreach($orden as $o){
                    $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));
                    $o->fecha = date("h:i A d-m-Y", strtotime($o->fecha));
                                      
                    $dato = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                    // siempre tendra estado_4
                    $time1 = Carbon::parse($o->fecha_4);
                    $horaEstimada = $time1->addMinute($o->hora_2)->format('h:i A d-m-Y');
                    $o->horaestimada = $horaEstimada;  
                    
                    // estimada entrega cliente
                    $time2 = Carbon::parse($o->fecha_4);
                    $suma = $o->hora_2 + $dato->copia_tiempo_orden;
                    $tiempoCliente = $time2->addMinute($suma)->format('h:i A d-m-Y');
                    $o->horaestimadacliente = $tiempoCliente;  
                                        
                    $o->fechatermino = date("h:i A d-m-Y", strtotime($o->fecha_5));
                    
                    $datos = Servicios::where('id', $o->servicios_id)->first();
                    $o->nombre = $datos->nombre;
                    $o->telefono = $datos->telefono;
                    $o->identificador = $datos->identificador;
                    $o->privado = $datos->privado;

                    // info de la direccion                   
                    $o->direccion = $dato->direccion;
                    $zona = Zonas::where('id', $dato->zonas_id)->pluck('nombre')->first();
                    $o->zonanombre = $zona;

                    // buscar si hay motorista asignado ya esta orden
                    $dato = DB::table('motorista_ordenes AS mo')
                    ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')             
                    ->select('m.nombre', 'm.telefono', 'mo.ordenes_id')
                    ->where('mo.ordenes_id', $o->id)
                    ->first();

                    if($dato != null){
                        $o->nombremoto = $dato->nombre;
                        $o->telefonomoto = $dato->telefono;
                    }else{
                        $o->nombremoto = "";
                        $o->telefonomoto = "";
                    }
                }

                return ['success' => 1, 'orden' => $orden];

            }else{
                return ['success' => 3];
            }
        }
    }
    
    // ocultar de tabla ordenes_urgentes
    public function ocultarUrgenteUno(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'                
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.'                
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            } 
         
            if(OrdenesUrgentes::where('ordenes_id', $request->id)->first()){

                OrdenesUrgentes::where('ordenes_id', $request->id)->update(['activo' => 0]);

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }

   //*** orden_pendiente_contestacion */

    // propietario termino de preparar la orden y ningun motorista agarro la orden
    // tabla: ordenes_urgentes_dos
    public function verOrdenesSinContestacion(Request $request){

        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'                
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.'                
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            } 
        
            if($p = Administradores::where('id', $request->id)->first()){
                
                $orden = DB::table('orden_pendiente_contestar AS p')
                ->join('ordenes AS o', 'o.id', '=', 'p.ordenes_id')             
                ->select('o.id', 'o.users_id', 'o.servicios_id', 'o.precio_total',
                        'o.fecha_orden', 'o.estado_2', 'o.estado_8')
                ->where('p.activo', 1)
                ->orderBy('o.id', 'DESC')
                ->get();
 
                foreach($orden as $o){

                    $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));
                          
                    $datos = Servicios::where('id', $o->servicios_id)->first();
                    $o->nombre = $datos->nombre;
                    $o->telefono = $datos->telefono;
                    $o->identificador = $datos->identificador;
                    $o->privado = $datos->privado;

                    // info de la direccion
                    $dato = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                    $o->direccion = $dato->direccion;
                }

                return ['success' => 1, 'orden' => $orden];

            }else{
                return ['success' => 2];
            }
        }
    }

    // ocultar ordenes pendiente de contestacion
    public function ocultarOrdenSinContestacion(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'                
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.'                
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            } 
         
            if(OrdenesPendienteContestar::where('ordenes_id', $request->id)->first()){

                OrdenesPendienteContestar::where('ordenes_id', $request->id)->update(['activo' => 0]);
 
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }

        }
    }

    //*** ordenes_urgentes_dos */

    // propietario termino de preparar la orden y ningun motorista agarro la orden
    // tabla: ordenes_urgentes_dos    
    public function verOrdenesUrgenteDos(Request $request){

        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'                
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.'                
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            } 
         
            if($p = Administradores::where('id', $request->id)->first()){
                
                if($p->activo == 0){
                    return ['success' => 1]; // revisador no activo
                }

                $orden = DB::table('ordenes_urgentes_dos AS ou')
                ->join('ordenes AS o', 'o.id', '=', 'ou.ordenes_id')             
                ->select('o.id', 'o.users_id', 'o.servicios_id', 'o.precio_total',
                        'o.fecha_orden', 'o.hora_2', 'o.estado_4', 'o.fecha_4',
                        'o.estado_5', 'o.fecha_5', 'ou.activo', 'o.estado_8', 'ou.fecha')
                ->where('ou.activo', 1)
                ->orderBy('o.id', 'DESC')
                ->get();

                foreach($orden as $o){
                    $o->fecha_orden = date("h:i A", strtotime($o->fecha_orden));
                    $o->fecha = date("h:i A", strtotime($o->fecha));
        
                    // hora que termino de preparar la orden
                    $o->fechatermino = date("h:i A", strtotime($o->fecha_5));

                    // hora estimada de entrega
                    $time1 = Carbon::parse($o->fecha_4);                                
                    $o->horaestimada = $time1->addMinute($o->hora_2)->format('h:i A'); 
                                        
                    $datos = Servicios::where('id', $o->servicios_id)->first();
                    $o->nombre = $datos->nombre;
                    $o->telefono = $datos->telefono;
                    $o->identificador = $datos->identificador;
                    $o->privado = $datos->privado;

                    // info de la direccion
                    $dato = OrdenesDirecciones::where('ordenes_id', $o->id)->first();                    
                    $o->direccion = $dato->direccion;

                    $zona = Zonas::where('id', $dato->zonas_id)->pluck('nombre')->first();
                    $o->zonanombre = $zona;

                    // buscar si hay motorista asignado ya esta orden
                    $dato = DB::table('motorista_ordenes AS mo')
                    ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')             
                    ->select('m.nombre', 'm.telefono', 'mo.ordenes_id')
                    ->where('mo.ordenes_id', $o->id)
                    ->first();

                    if($dato != null){
                        $o->nombremoto = $dato->nombre;
                        $o->telefonomoto = $dato->telefono;
                    }else{
                        $o->nombremoto = "";
                        $o->telefonomoto = "";
                    }
                }

                return ['success' => 1, 'orden' => $orden];

            }else{
                return ['success' => 3];
            }
        }
    }

    // ocultar ordenes_urgentes_dos
    public function ocultarUrgenteDos(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'                
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.'                
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            } 
            
            if(OrdenesUrgentesDos::where('ordenes_id', $request->id)->first()){

                OrdenesUrgentesDos::where('ordenes_id', $request->id)->update(['activo' => 0]);

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }
 
    //*** ordenes_urgentes_tres */

    // pasaron 5+ de hora entrega al cliente (hora_2 + zona + 5+) y no se ha entregado su orden
    // tabla ordenes_urgentes_tres 
    public function verOrdenesUrgenteTres(Request $request){

        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'                
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.'                
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            } 
         
            if($p = Administradores::where('id', $request->id)->first()){
                
                if($p->activo == 0){
                    return ['success' => 1]; // revisador no activo
                }

                $orden = DB::table('ordenes_urgentes_tres AS ou')
                ->join('ordenes AS o', 'o.id', '=', 'ou.ordenes_id')             
                ->select('o.id', 'o.users_id', 'o.servicios_id', 'o.precio_total',
                        'o.fecha_orden', 'o.hora_2', 'o.estado_4', 'o.fecha_4',
                        'o.estado_5', 'o.fecha_5', 'ou.activo', 'o.estado_8', 'ou.fecha')
                ->where('ou.activo', 1)
                ->orderBy('o.id', 'DESC')
                ->get();

                foreach($orden as $o){
                    $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));

                    $o->fecha = date("h:i A d-m-Y", strtotime($o->fecha));
        
                    // hora que termino de preparar la orden
                    $o->fechatermino = date("h:i A d-m-Y", strtotime($o->fecha_5));
                    
                    $datos = Servicios::where('id', $o->servicios_id)->first();
                    $o->nombre = $datos->nombre;
                    $o->telefono = $datos->telefono;
                    $o->identificador = $datos->identificador;
                    $o->privado = $datos->privado;

                    // info de la direccion
                    $dato = OrdenesDirecciones::where('ordenes_id', $o->id)->first();                    
                    $o->direccion = $dato->direccion;

                    // hora estimada de entrega
                    $time1 = Carbon::parse($o->fecha_4);                                
                    $o->horaestimada = $time1->addMinute($o->hora_2)->format('h:i A d-m-Y'); 

                    // estimada entrega cliente
                    $time2 = Carbon::parse($o->fecha_4);
                    $suma = $o->hora_2 + $dato->copia_tiempo_orden;
                    $tiempoCliente = $time2->addMinute($suma)->format('h:i A d-m-Y');
                    $o->horaestimadacliente = $tiempoCliente;  

                    $zona = Zonas::where('id', $dato->zonas_id)->pluck('nombre')->first();
                    $o->zonanombre = $zona;

                    // buscar si hay motorista asignado ya esta orden
                    $dato = DB::table('motorista_ordenes AS mo')
                    ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')             
                    ->select('m.nombre', 'm.telefono', 'mo.ordenes_id')
                    ->where('mo.ordenes_id', $o->id)
                    ->first();

                    if($dato != null){
                        $o->nombremoto = $dato->nombre;
                        $o->telefonomoto = $dato->telefono;
                    }else{
                        $o->nombremoto = "";
                        $o->telefonomoto = "";
                    }
                }

                return ['success' => 1, 'orden' => $orden];

            }else{
                return ['success' => 3];
            }
        }
    }

    // ocultar ordenes_urgentes_tres
    public function ocultarUrgenteTres(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'                
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.'                
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            } 
            
            if(OrdenesUrgentesTres::where('ordenes_id', $request->id)->first()){

                OrdenesUrgentesTres::where('ordenes_id', $request->id)->update(['activo' => 0]);

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }

    //*** ordenes_urgentes_cuatro */

    // paso la mitad de tiempo que el propietario dijo que entregarian la orden
    // ningun motorista agarro la orden
    public function verOrdenesUrgenteCuatro(Request $request){

        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'                
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.'                
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            } 
         
            if($p = Administradores::where('id', $request->id)->first()){
                
                if($p->activo == 0){
                    return ['success' => 1]; // revisador no activo
                }

                $orden = DB::table('ordenes_urgentes_cuatro AS ou')
                ->join('ordenes AS o', 'o.id', '=', 'ou.ordenes_id')             
                ->select('o.id', 'o.users_id', 'o.servicios_id', 'o.precio_total',
                        'o.fecha_orden', 'o.hora_2', 'o.estado_4', 'o.fecha_4',
                        'o.estado_5', 'o.fecha_5', 'ou.activo', 'o.estado_8', 'ou.fecha')
                ->where('ou.activo', 1)
                ->orderBy('o.id', 'DESC')
                ->get();

                foreach($orden as $o){
                    $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));
                    $o->fecha = date("h:i A", strtotime($o->fecha));

                    // hora estimada de entrega
                    $time1 = Carbon::parse($o->fecha_4);                                
                    $o->horaestimada = $time1->addMinute($o->hora_2)->format('h:i A');   
                                                            
                    $datos = Servicios::where('id', $o->servicios_id)->first();
                    $o->nombre = $datos->nombre;
                    $o->telefono = $datos->telefono;
                    $o->identificador = $datos->identificador;
                    $o->privado = $datos->privado;

                    // info de la direccion
                    $dato = OrdenesDirecciones::where('ordenes_id', $o->id)->first();                    
                    $o->direccion = $dato->direccion;

                    $zona = Zonas::where('id', $dato->zonas_id)->pluck('nombre')->first();
                    $o->zonanombre = $zona;

                    // buscar si hay motorista asignado ya esta orden
                    $dato = DB::table('motorista_ordenes AS mo')
                    ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')             
                    ->select('m.nombre', 'm.telefono', 'mo.ordenes_id')
                    ->where('mo.ordenes_id', $o->id)
                    ->first();

                    if($dato != null){
                        $o->nombremoto = $dato->nombre;
                        $o->telefonomoto = $dato->telefono;
                    }else{
                        $o->nombremoto = "";
                        $o->telefonomoto = "";
                    }
                }

                return ['success' => 1, 'orden' => $orden];

            }else{
                return ['success' => 2];
            }
        }
    }

    // ocultar ordenes_urgentes_cuatro
    public function ocultarOrdenesCuatro(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'                
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.'                
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [ 
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            } 
            
            if(OrdenesUrgentesCuatro::where('ordenes_id', $request->id)->first()){

                OrdenesUrgentesCuatro::where('ordenes_id', $request->id)->update(['activo' => 0]);

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }


    // ver estado de problema de la orden
    public function verEstados(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'                
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.'                
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            } 
         
            if($p = Administradores::where('id', $request->id)->first()){
                
                if($p->activo == 0){
                    return ['success' => 1]; // revisador no activo
                } 

                $orden = DB::table('ordenes_problemas AS ou')
                ->join('ordenes AS o', 'o.id', '=', 'ou.ordenes_id')             
                ->select('o.id', 'ou.id AS idproblema', 'o.users_id', 'o.servicios_id', 'o.precio_total',
                        'o.fecha_orden', 'o.hora_2', 'o.estado_4', 'o.fecha_4',
                        'o.estado_5', 'o.fecha_5', 'ou.activo', 'ou.tipo', 'o.estado_8', 'ou.fecha')
                ->where('ou.activo', 1)
                ->orderBy('o.id', 'DESC')
                ->get();

                foreach($orden as $o){
                    $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));
                    $o->fecha = date("h:i A d-m-Y", strtotime($o->fecha));
                                                                               
                    $datos = Servicios::where('id', $o->servicios_id)->first();
                    $o->nombre = $datos->nombre;                   
                    $o->identificador = $datos->identificador;
                    $o->privado = $datos->privado;

                    // info de la direccion
                    $dato = OrdenesDirecciones::where('ordenes_id', $o->id)->first();    
                    $zona = Zonas::where('id', $dato->zonas_id)->pluck('nombre')->first();
                    $o->zonanombre = $zona;                    
                }

                return ['success' => 1, 'orden' => $orden];

            }else{
                return ['success' => 2];
            }
        }
    }

    // ver productos de las ordenes
    public function verProductosOrden(Request $request){
        // validaciones para los datos
        $reglaDatos = array(
            'ordenid' => 'required'               
        );

        $mensajeDatos = array(                                      
            'ordenid.required' => 'El id de la orden es requerido.'
            );

        $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

        if($validarDatos->fails()) 
        {
            return [
                'success' => 0, 
                'message' => $validarDatos->errors()->all()
            ];
        }

        if($or = Ordenes::where('id', $request->ordenid)->first()){

            // verificar si puede ver los productos el motorista

            $producto = DB::table('ordenes AS o')
                        ->join('ordenes_descripcion AS od', 'od.ordenes_id', '=', 'o.id')
                        ->join('producto AS p', 'p.id', '=', 'od.producto_id')
                        ->select('od.id AS productoID', 'p.nombre', 'od.nota', 'p.imagen', 'p.utiliza_imagen', 'od.precio', 'od.cantidad')
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
            return ['success' => 3];
        }
    }

    // ver productos del encargo
    public function verProductosOrdenEncargo(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'                
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id del motorista es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            if(OrdenesEncargo::where('id', $request->id)->first()){

                $producto = DB::table('ordenes_encargo_producto AS o')
                ->join('producto_categoria_negocio AS p', 'p.id', '=', 'o.producto_cate_nego_id')
                ->select('o.id', 'p.imagen', 'o.nombre', 'o.nota', 'o.descripcion', 'o.cantidad', 'o.precio')
                ->where('o.ordenes_encargo_id', $request->id)
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
    }


    public function verListaServicios(Request $request){

        $lista = Servicios::select('id', 'nombre')->orderBy('nombre')->get();
        return ['success' => 1, 'servicios' => $lista];
    } 

    public function verListaMotoristas(Request $request){

        $lista = Motoristas::select('id', 'nombre')
        ->whereNotIn('id', [1,2,3,5,6])
        ->orderBy('nombre')
        ->get();

        return ['success' => 1, 'servicios' => $lista];
    } 

    public function verListaServiciosPropietarios(Request $request){

        $lista = Propietarios::where('servicios_id', $request->id)
        ->select('id', 'nombre')      
        ->orderBy('nombre')
        ->get();
 
        return ['success' => 1, 'servicios' => $lista];
    }

    public function enviarNotificacionPropietario(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'                
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id del motorista es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            if($pp = Propietarios::where('id', $request->id)->first()){

                if($pp->device_id == "0000"){
                   return ['success' => 1];
                }
 
                $titulo = "Hola";
                $mensaje = "Esta es una prueba";

                try {
                    $this->envioNoticacionPropietario($titulo, $mensaje, $pp->device_id);
                    } catch (Exception $e) {                              
                } 
                
                return ['success' => 2];
            }else{
                return ['success' => 3];
            }
        }
    } 

    public function enviarNotificacionMotorista(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'                
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id del motorista es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            if($pp = Motoristas::where('id', $request->id)->first()){

                if($pp->device_id == "0000"){
                   return ['success' => 1];
                }
 
                $titulo = "Solicitud Nueva";
                $mensaje = "Se necesita motorista";

                try {
                    $this->envioNoticacionMotorista($titulo, $mensaje, $pp->device_id);
                    } catch (Exception $e) {                              
                } 
                
                return ['success' => 2];
            }else{
                return ['success' => 3];
            }
        }
    } 

    public function envioNoticacionMotorista($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionMotorista($titulo, $mensaje, $pilaUsuarios);
    }

    public function envioNoticacionPropietario($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionPropietario($titulo, $mensaje, $pilaUsuarios);
    }
    
}
