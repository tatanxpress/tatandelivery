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

class AdminAppController extends Controller
{

    public function reporte($idservicio, $fecha1, $fecha2){

        return 'llego';
    }

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

    // ver ordenes urgente
    public function verOrdenesUrgente(Request $request){
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

                $orden = DB::table('ordenes_pendiente AS p')
                ->join('ordenes AS o', 'o.id', '=', 'p.ordenes_id')             
                ->select('o.id', 'o.users_id', 'o.servicios_id', 'o.precio_total',
                        'o.fecha_orden', 'o.hora_2', 'o.estado_4', 'o.fecha_4',
                        'o.estado_5', 'o.fecha_5', 'p.activo', 'p.tipo', 'o.estado_8')
                ->where('p.activo', 1)
                ->orderBy('p.id', 'ASC')
                ->get();

                foreach($orden as $o){

                    // ingreso la orden
                    $fechaOrden = $o->fecha_orden;
                    $horaO = date("h:i A", strtotime($fechaOrden));
                    $fechaO = date("d-m-Y", strtotime($fechaOrden));
                    $o->fecha_orden = $horaO . " " . $fechaO;

                    // hora estimada de entrega
                    if($o->estado_4 == 1){
                        
                    }else{
                        $o->horaEstimada = 0; 
                    }
          
                    // hora inicio preparacion
                    if($o->estado_4 == 1){

                        $time1 = Carbon::parse($o->fecha_4);
                        $horaEstimada = $time1->addMinute($o->hora_2)->format('h:i A d-m-Y');
                        $o->horaEstimada = $horaEstimada; 

                        $fecha = $o->fecha_4;
                        $hora = date("h:i A", strtotime($fecha));
                        $fecha = date("d-m-Y", strtotime($fecha));                      
                        $o->iniciopreparar = $hora . " " . $fecha;
                    }else{
                        $o->iniciopreparar = 0;
                    }

                    // hora termino preparar la orden
                    if($o->estado_5 == 1){
                        $fecha5 = $o->fecha_5;
                        $hora5 = date("h:i A", strtotime($fecha5));
                        $fecha55 = date("d-m-Y", strtotime($fecha5));                      
                        $o->fechatermino = $hora5 . " " . $fecha55;
                    }else{
                        $o->fechatermino = 0;
                    }

                    $datos = Servicios::where('id', $o->servicios_id)->first();
                    $o->nombre = $datos->nombre;
                    $o->telefono = $datos->telefono;
                    $o->identificador = $datos->identificador;
                    $o->privado = $datos->privado;

                    // info de la direccion
                    $dato = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                    $o->direccion = $dato->direccion;

                    // buscar nombre y telefono del motorista por orden pendiente

                    // buscar si hay motorista asignado
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

                return ['success' => 2, 'orden' => $orden];

            }else{
                return ['success' => 3];
            }
        }
    }


    public function verOrdenesProgramada(Request $request){

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

                $orden = DB::table('ordenes_urgentes AS p')
                ->join('ordenes AS o', 'o.id', '=', 'p.ordenes_id')             
                ->select('o.id', 'o.users_id', 'o.servicios_id', 'o.precio_total',
                        'o.fecha_orden', 'o.hora_2', 'o.estado_4', 'o.fecha_4',
                        'o.estado_5', 'o.fecha_5', 'p.activo', 'p.tipo', 'o.estado_8')
                ->where('p.activo', 1)
                ->orderBy('p.id', 'ASC')
                ->get();

                foreach($orden as $o){

                    // ingreso la orden
                    $fechaOrden = $o->fecha_orden;
                    $horaO = date("h:i A", strtotime($fechaOrden));
                    $fechaO = date("d-m-Y", strtotime($fechaOrden));
                    $o->fecha_orden = $horaO . " " . $fechaO;

                    // hora estimada de entrega
                    if($o->estado_4 == 1){
                        
                    }else{
                        $o->horaEstimada = 0; 
                    }
          
                    // hora inicio preparacion
                    if($o->estado_4 == 1){

                        $time1 = Carbon::parse($o->fecha_4);
                        $horaEstimada = $time1->addMinute($o->hora_2)->format('h:i A d-m-Y');
                        $o->horaEstimada = $horaEstimada; 

                        $fecha = $o->fecha_4;
                        $hora = date("h:i A", strtotime($fecha));
                        $fecha = date("d-m-Y", strtotime($fecha));                      
                        $o->iniciopreparar = $hora . " " . $fecha;
                    }else{
                        $o->iniciopreparar = 0;
                    }

                    // hora termino preparar la orden
                    if($o->estado_5 == 1){
                        $fecha5 = $o->fecha_5;
                        $hora5 = date("h:i A", strtotime($fecha5));
                        $fecha55 = date("d-m-Y", strtotime($fecha5));                      
                        $o->fechatermino = $hora5 . " " . $fecha55;
                    }else{
                        $o->fechatermino = 0;
                    }

                    $datos = Servicios::where('id', $o->servicios_id)->first();
                    $o->nombre = $datos->nombre;
                    $o->telefono = $datos->telefono;
                    $o->identificador = $datos->identificador;
                    $o->privado = $datos->privado;

                    // info de la direccion
                    $dato = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                    $o->direccion = $dato->direccion;

                    // buscar nombre y telefono del motorista por orden pendiente

                    // buscar si hay motorista asignado
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

                return ['success' => 2, 'orden' => $orden];

            }else{
                return ['success' => 3];
            }
        }
    }


    // ocultar una orden
    public function ocultar(Request $request){
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
         
            if(OrdenesPendiente::where('ordenes_id', $request->id)->first()){

                OrdenesPendiente::where('ordenes_id', $request->id)->update(['activo' => 0]);

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }

        }
    }

    // ocultar de tabla ordenes_urgentes
    public function ocultarurgente(Request $request){
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


}
