<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Direccion;
use App\Zonas;
use Illuminate\Support\Facades\DB;
use Log; 

class RegisterController extends Controller
{
    // verificar usuario y correo si estan libres y registra usuario    
    public function registroUsuario(Request $request){
         if($request->isMethod('post')){ 

            $rules = array(        
                'nombre' => 'required|max:100',
                'telefono' => 'required|max:20',
                'password' => 'required|min:8|max:16',
                'correo' => 'required|max:100',
            );    

            $messages = array(                          
                'nombre.required' => 'El nombre es requerido',                                    
                'nombre.max' => '100 caracteres máximo para el nombre',             

                'telefono.required' => 'El telefono es requerido',                                    
                'telefono.max' => '20 caracteres máximo para el nombre',             

                'password.required' => 'La contraseña es requerida.',
                'password.min' => 'Mínimo 8 caracteres',
                'password.max' => 'Máximo 16 caracteres',   
                
                'correo.required' => 'El correo es requerido',
                'correo.max' => '100 caracteres máximo para correo',
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            $unido;

            if($request->area == null){
                $unido = "+503" . $request->telefono; // versiones viejas
            }else{
                $unido = $request->area . $request->telefono;
            }

            // verificar si existe el telefono
            if(User::where('phone', $unido)->first()){
                return ['success' => 1];
            }

            // verificar si existe el correo
            if(User::where('email', $request->correo)->first()){
                return ['success' => 2];
            }

            $fecha = Carbon::now('America/El_Salvador');

            // REGISTRO DE USUARIO NO HERMANO LEJANO
            $usuario = new User();
            $usuario->name = $request->nombre;
            $usuario->phone = $unido;
            $usuario->password = Hash::make($request->password);
            $usuario->email = $request->correo;

            if($request->device_id == null){
                $usuario->device_id = "0000";
            }else{
                $usuario->device_id = $request->device_id;
            } 
        
            $usuario->fecha = $fecha;
            $usuario->zonas_id = 2; // zona sin servicios, para que seleccionen una direccion
            $usuario->activo = 1;

            // campos para credi-puntos
            $usuario->monedero = 0; // credi puntos

            
            if($request->area == null){
                $usuario->area = "+503"; // versiones viejas
            }else{
                $usuario->area = $request->area; // nuevas actualizacion
            }
            $usuario->activo_tarjeta = 0; // bien
           
            if($usuario->save()){
                return ['success'=>3, 'usuario_id'=> $usuario->id];
                
            }else{
                return [
                    'success' => 4 // error al crear una cuenta                    
                ];
            }                      
        }   
    }

    // registro de usuario lejano, para uso de credi-puntos
    public function registroUsuarioLejano(Request $request){
       
        if($request->isMethod('post')){ 

           $rules = array(
               'nombre' => 'required|max:100',
               'telefono' => 'required|max:20',
               'password' => 'required|min:8|max:16',
               'correo' => 'required|max:100',
           );    

           $messages = array(                          
               'nombre.required' => 'El nombre es requerido',                                    
               'nombre.max' => '100 caracteres máximo para el nombre',             

               'telefono.required' => 'El telefono es requerido',                                    
               'telefono.max' => '20 caracteres máximo para el nombre',             

               'password.required' => 'La contraseña es requerida.',
               'password.min' => 'Mínimo 8 caracteres',
               'password.max' => 'Máximo 16 caracteres',   
               
               'correo.required' => 'El correo es requerido',
               'correo.max' => '100 caracteres máximo para correo',
               );

           $validator = Validator::make($request->all(), $rules, $messages );

           if ( $validator->fails() ) 
           {
               return [
                   'success' => 0, 
                   'message' => $validator->errors()->all()
               ];
           }

           $unido = $request->area . $request->telefono;
        
            // verificar si existe el telefono
            if(User::where('phone', $unido)->first()){
               return ['success' => 1];
            }

            // verificar si existe el correo
            if(User::where('email', $request->correo)->first()){
               return ['success' => 2];
            }
          
            DB::beginTransaction();
            try {

                // GUARDAR USUARIO NUEVO, Y SU DIRECCION

                $fecha = Carbon::now('America/El_Salvador');
 
                $usuario = new User();
                $usuario->name = $request->nombre;
                $usuario->phone = $unido;
                $usuario->password = Hash::make($request->password);
                $usuario->email = $request->correo;
                if($request->device_id == null){
                    $usuario->device_id = "";
                }else{
                    $usuario->device_id = $request->device_id;
                }
                
                $usuario->fecha = $fecha;
                $usuario->zonas_id = $request->idzona; // zona segun ciudad seleccionada
                $usuario->activo = 1;

                // campos para credi-puntos
                $usuario->monedero = 0;  // credi puntos
                $usuario->area = $request->area; // area de esta persona lejana
                $usuario->activo_tarjeta = 0; // bien
                
                if($usuario->save()){

                    $data = Zonas::where('id', $request->idzona)->first();

                    // hoy guardar una direccion de un solo
                    $d = new Direccion();
                    $d->user_id = $usuario->id;
                    $d->nombre = $request->nombrerecibe; // requerido
                    $d->direccion = $request->direccion; // requerido
                    
                    $d->numero_casa = $request->numero; // numero de quien recibe
    
                    // campo opcional para el cliente, pero aqui no puede ser null
                    if($request->punto_referencia == null){
                        $d->punto_referencia = "";
                    }else{
                        $d->punto_referencia = $request->punto_referencia;
                    }
                                        
                    $d->zonas_id = $request->idzona; // segun ciudad seleccionada                
                    $d->seleccionado = 1;

                    // latitud y longitud segun la zona ciudad seleccionada
                    $d->latitud = $data->latitud;
                    $d->longitud = $data->longitud;
                    $d->latitud_real = $data->latitud;
                    $d->longitud_real = $data->longitud;                
                    $d->revisado = 0;

                    // campos para credi-puntos

                    // 0: esperando confirmacion del administrador
                    // 1: verificada
                    // 2: rechazada
                    $d->estado = 0;
                    $d->precio_envio = 0;
                    //$d->mensaje_rechazo = ""; // null por defecto
                                        
                    if($d->save()){
                         // devolverle id al cliente, y mostrara ya todos los servicios 
                        // de la ciudad seleccionada
                        DB::commit();
                        return ['success'=> 3, 'usuario_id'=> $usuario->id];     
                    }else{
                        return [
                            'success' => 4 // error al crear una cuenta                    
                        ];
                    }
                                   
                }else{
                    return [
                        'success' => 4 // error al crear una cuenta                    
                    ];
                }    

            } catch(\Throwable $e){
                DB::rollback();
                
                return ['success' => 5];
            }                              
       }   
   }




}
