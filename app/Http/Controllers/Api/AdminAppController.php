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
                    'o.estado_2', 'o.hora_2', 'o.estado_3', 'o.estado_4', 'o.fecha_4', 'o.estado_5', 'o.estado_6',
                    'o.estado_7', 'o.estado_8')
                ->whereDate('o.fecha_orden', $fecha)
                ->orderBy('o.id', 'DESC')
                ->get();
      
                $estado = "";
                foreach($orden as $o){        
                    $o->fecha_orden = date("h:i A", strtotime($o->fecha_orden));
    
                    $od = OrdenesDirecciones::where('ordenes_id', $o->id)->first();
                    $o->zonanombre = Zonas::where('id', $od->zonas_id)->pluck('nombre')->first();
    
                    $haymotorista = 0;
                    $nombremotorista = "";

                    $estado4 = 0;
                    if($o->estado_4 == 1){
                        $estado4 = 1;
                          // estimada entrega cliente
                        $time2 = Carbon::parse($o->fecha_4);
                        $suma = $o->hora_2 + $od->copia_tiempo_orden;
                        $tiempoCliente = $time2->addMinute($suma)->format('h:i A d-m-Y');
                        $o->horaestimadacliente = $tiempoCliente;  
                    }

                    $o->estado4 = $estado4;
    
                    if($motorista = MotoristaOrdenes::where('ordenes_id', $o->id)->first()){
                        $haymotorista = 1;
    
                        $n = Motoristas::where('id', $motorista->motoristas_id)->first();
                        $nombremotorista = $n->nombre;
                    }
    
                    $o->motorista = $nombremotorista;
                    $o->haymotorista = $haymotorista;
                    
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
                }
    
                return ['success' => 2, 'ordenes' => $orden];
                
            }else{
                return ['success' => 1];
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

    public function ocultarEstados(Request $request){
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
            
            if(OrdenesPendiente::where('id', $request->id)->first()){

                OrdenesPendiente::where('id', $request->id)->update(['activo' => 0]);

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }
    
}
