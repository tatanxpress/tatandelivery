<?php

namespace App\Http\Controllers\Api\Auth;

use App\CodigoTemporal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\User;
use Mail;
use App\Mail\RecuperarPasswordEmail;
use Exception;
use Illuminate\Support\Facades\DB;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Hash;
use App\ActivoSms;

class LoginController extends Controller
{


    // verificar si el numero esta registrado o no, envio SMS
    public function verificarNumero(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'telefono' => 'required',
            );      
            $messages = array(                          
                'telefono.required' => 'El telefono es requerido.'
                ); 

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails()) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()                   
                ];
            }

           

        DB::beginTransaction();

        try{
            // ver si el numero ya esta registrado
            if(User::where('phone', $request->telefono)->first()){
                return [
                    'success' => 1 // numero ya verificado                    
                ];
            }else{
               
                // generar codigo de 6 digitos
                $codigo = '';
                $pattern = '1234567890';
                $max = strlen($pattern)-1; 
                for($i=0;$i <6; $i++)           
                {
                    $codigo .= $pattern{mt_rand(0,$max)};
                }
                
                // verificar si existe el numero en tabla codigo temporales, sino se creara
                if($valor = CodigoTemporal::where('telefono', $request->area)->first()){
                   
                        // Verificar maximo permitido
                        $maximo = 3; 
                        (int)$contador = $valor->contador;   
                                
                        if($contador >= $maximo){

                            CodigoTemporal::where('telefono', $request->area)->update(['codigo' => $codigo]);
                            DB::commit();
                            return [
                                'success' => 3,
                                'codigo' => $codigo // mostrar codigo al usuario si supera limite sms
                            ];
                        }else{
                            
                            // actualizar su contador a + 1
                            $suma = $contador + 1;
                            // actualiza el contador 
                            CodigoTemporal::where('telefono', $request->area)
                            ->update(['contador' => $suma, 'codigo' => $codigo]);

                            DB::commit();
                        }
                }else{
                   
                        // crear nuevo campo
                        $temporal = new CodigoTemporal();
                        $temporal->telefono = $request->area;
                        $temporal->codigo = $codigo;
                        $temporal->contador = 1;
                        $temporal->save();
                        DB::commit();
                }

                    //$activosms = ActivoSms::where('id', 1)->pluck('activo')->first();
                    // esto dara el codigo de una vez

                    // YA NO SE UTILIZARA API SMS

                    return [
                        'success' => 3,
                        'codigo' => $codigo // mostrar codigo al usuario si supera limite sms
                    ];

                    // YA NO LLEGARA A ESTO

                    //$sid = 'ACc68bf246c0d9be071f2367e81b686201';
                   // $token = '01990626f6e7fb813eb7317c06db6a47'; 
                   // $twilioNumber = '+12074668219';
                   // $client = new Client($sid, $token);
                  
                    try {
                       /* $client->account->messages->create(   
                            $request->numero,
                            array(                        
                                'from' =>  $twilioNumber,            
                                'body' =>'Tu código Tatan Express es: '.$codigo
                            )
                        ); */ 

                      
                        return [                            
                            'success' => 2 //codigo enviado                            
                        ];                
                    } catch (TwilioException $e) {                     
                        // por cualquier error, notificar a la app y no guardar el contador
                        DB::rollback();
                        return [
                            'success' => 4 //error al enviar el codigo                            
                        ];
                    }
                }
            }catch(\Throwable $e){
                DB::rollback();
                return [
                    'success' => 4 . $e //error                    
                ];
            }
        }
    }

    // verificacion de numero + codigo en tabla temporal para verificar numero telefonico al registrarse en la app 
    public function verificarCodigoTemporal(Request $request){

        if($request->isMethod('post')){   
            $rules = array(                
                'telefono' => 'required',
                'codigo' => 'required',
            );    

            $messages = array(                                      
                'telefono.required' => 'El teléfono es requerido.',                                                
                'codigo.required' => 'El código es requerido.',   
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            // si lo anterior esta bien, procede a vericar el codigo. 
            if(CodigoTemporal::where('telefono', $request->telefono)->where('codigo', $request->codigo)->first()){
                return [
                    'success' => 1 // autentificacion correcta                    
                ];
            }else{
                return [
                    'success' => 2 // codigo es incorrecto                    
                ];
            }
        }
    }

    // login usuario por usuario y contraseña
    public function loginUsuario(Request $request){
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

            // verificar credenciales
            if($u = User::where('phone', $request->phone)->first()){

                if($u->activo == 0){
                    return ['success' => 3]; // usuario desactivado
                }
                
                if (Hash::check($request->password, $u->password)) {
                    
                    $id = $u->id;

                    // actualizar device_id
                    if($request->device_id != null){
                        User::where('id', $id)->update(['device_id' => $request->device_id]);
                    }
                    
                    return ['success'=>1,'usuario_id' => $id];
                    
                }else{
                    return ['success' => 2]; // contraseña incorrecta
                }
            } else {
                return ['success' => 2]; // telefono no encontrado
            }
        }
    }

    // recuperacion de contraseña por correo electronico
    public function codigoCorreo(Request $request){
        
        if($request->isMethod('post')){   
            $rules = array(                
                'telefono' => 'required|max:20'
            );    
     
            $messages = array(                                      
                'telefono.required' => 'El telefono es requerido',
                'telefono.max' => '20 caracteres máximo para el telefono'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            // verificar si correo esta registrado
            if(User::where('phone', $request->telefono)->first()){
                
                $codigo = '';
                $pattern = '1234567890';
                $max = strlen($pattern)-1; 
                for($i=0;$i <6; $i++)           
                {
                    $codigo .= $pattern{mt_rand(0,$max)};
                }

                // cambiar el codigo del correo
                User::where('phone', $request->telefono)->update(['codigo_correo' => $codigo]);
                
                // enviar correo, aunque no este validado
                $datos = User::where('phone', $request->telefono)->first();
                $nombre = $datos->name;
                $correo = $datos->email;
                              
               try{
                // envio de correo
                Mail::to($correo)->send(new RecuperarPasswordEmail($nombre, $codigo));

                return [
                    'success' => 1 // correo enviado                    
                ]; 
                }   catch(Exception $e){
                    return [
                        'success' => 2 // correo no encontrado                        
                    ];       
                }
            }else{
                return [
                    'success' => 2 // correo no encontrado                    
                ];  
            }                   
        }  
    }   
    
    // revisar codigo del correo
    public function revisarCodigoCorreo(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'telefono' => 'required',
                'codigo' => 'required',
            );    

            $messages = array(                                      
                'telefono.required' => 'El telefono es requerido',                
                'codigo.required' => 'El codigo es requerido',
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            // buscar correo y codigo, si coincide, obtener token
            if($usuario = User::where('phone', $request->telefono)->where('codigo_correo', $request->codigo)->first()){
                
                return ['success' => 1];

            }else{
                return ['success' => 2]; // codigo no coincide
            }
        }
    }
}
