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
use App\NumeroSMS;
use Illuminate\Support\Carbon;
use App\DineroOrden;
use App\AreasPermitidas;
use Log;

class LoginController extends Controller
{
    // verificar si el numero esta registrado o no, envio SMS
    public function verificarNumero(Request $request){
        
        if($request->isMethod('post')){   
            $rules = array(                
                'telefono' => 'required',
                'area' => 'required'
            );      
            $messages = array(                          
                'telefono.required' => 'El telefono es requerido.',
                'area.required' => 'Area es requerido'
                ); 

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails()) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()                   
                ];
            }

            $unido = $request->area . $request->telefono;

            // ver si el numero ya esta registrado
            if(User::where('phone', $unido)->first()){
                
                return ['success' => 1 ]; // numero ya registrado
            }else{

                $datos = DineroOrden::where('id', 1)->first();
                $correo = $datos->correo;
                $sms = $datos->activo_sms;

                // si esta inactivo los sms
                if($sms == 0){
                    return ['success' => 5];
                }

                $codigo = '';
                $pattern = '1234567890';
                $max = strlen($pattern)-1; 
                for($i=0;$i <6; $i++){
                    $codigo .= $pattern{mt_rand(0,$max)};
                }

                DB::beginTransaction();
            
                // si no encuentra el registro se enviara el codigo sms, verificar su contador
                if($ns = NumeroSMS::where('area', $request->area)->where('numero', $request->telefono)->first()){
                                
                        // verificar contador si permite mas intentos
                        $limitecontador = 7;
                        $contador = $ns->contador;

                        if($contador >= $limitecontador){
                            // supero limite, contactar administracion
                        
                            return ['success' => 2, 'correo' => $correo];  
                        }else{
                            $contador = $contador + 1;
                            // aun tiene intentos sms, enviar codigo
                            NumeroSMS::where('id', $ns->id)->update(['contador' => $contador, 'codigo' => $codigo]);  
                            
                            DB::commit();                        
                        }                       

                }else{ 
                    // numero no registrado, guardar registro y enviar sms
                    $fecha = Carbon::now('America/El_Salvador');

                    $n = new NumeroSMS();
                    $n->area = $request->area;
                    $n->numero = $request->telefono;
                    $n->codigo = $codigo;
                    $n->codigo_fijo = $codigo;
                    $n->contador = 0;
                    $n->fecha = $fecha;
                    $n->save();                   
                }
                DB::commit();
                // DESACTIVADO SMS
                return ['success' => 3];
            
                // envio del mensaje
                $sid = "ACc68bf246c0d9be071f2367e81b686201";
                $token = "01990626f6e7fb813eb7317c06db6a47"; 
                $twilioNumber = "+12075012749"; 
                $client = new Client($sid, $token);
                $numero = $request->area . $request->telefono;
            
                try {
                    $client->account->messages->create(   
                        $numero,
                        array(                        
                            'from' =>  $twilioNumber,            
                            'body' =>'Tu código Tatan Express es: '.$codigo
                        )
                    );
                
                 return ['success' => 3];
                } catch (Exception  $e) {
                    return ['success' => 4, 'correo' => $correo];
                }
            }
        }
    }

    public function verificarCodigoTemporal(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'telefono' => 'required',
                'codigo' => 'required'
            );

            $messages = array(                                      
                'telefono.required' => 'El telefono es requerida.',
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

            // verificar codigo
            if($ns = NumeroSMS::where('numero', $request->telefono)->first()){

                if($request->codigo == $ns->codigo || $request->codigo == $ns->codigo_fijo){
                    return ['success' => 1];
                }else{
                    //codigo incorrecto
                    return ['success' => 2];
                }

            }else{
                //numero no encontrado
                return ['success' => 3];
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

            $unido = "+503".$request->phone; // por defecto para versiones anteriores

            // verificar credenciales
            if($u = User::where('phone', $unido)->first()){

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

            $unido = $request->telefono;

            // verificar si correo esta registrado
            if($datos = User::where('phone', $unido)->first()){
                
                $codigo = '';
                $pattern = '1234567890';
                $max = strlen($pattern)-1; 
                for($i=0;$i <6; $i++)           
                {
                    $codigo .= $pattern{mt_rand(0,$max)};
                }

                // cambiar el codigo del correo
                User::where('phone', $unido)->update(['codigo_correo' => $codigo]);
                
                // enviar correo, aunque no este validado              
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

            $unido = "+503" . $request->telefono;

            // buscar correo y codigo, si coincide, obtener token
            if($usuario = User::where('phone', $unido)->where('codigo_correo', $request->codigo)->first()){
                
                return ['success' => 1];

            }else{
                return ['success' => 2]; // codigo no coincide
            }
        }
    }


    // ACCESO PARA NUMEROS YA CON AREAS 

    // verificar si el numero esta registrado o no, envio SMS
    public function verificarNumeroArea(Request $request){
        
        if($request->isMethod('post')){   
            $rules = array(                
                'telefono' => 'required',
                'area' => 'required'
            );      
            $messages = array(                          
                'telefono.required' => 'El telefono es requerido.',
                'area.required' => 'Area es requerido'
                ); 

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails()) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()                   
                ];
            }
 
            $unido = $request->area . $request->telefono;
            // encontro numero de misma area
            if(User::where('phone', $unido)->first()){
                // si encontramos mismo numero y de la misma area
                return ['success' => 1 ]; // numero ya registrado                
            }

            // Como no se encontro el numero registrado con el area, se envia sms

            // tabla de solo 1 registro para obtener informacion
            $datos = DineroOrden::where('id', 1)->first();
            $correo = $datos->correo;
           
            // ** Ya no se podra desactivar los sms, en ese caso se tendra que registrar
            // el numero en el panel de control  29/08/2020
            
            $codigo = '';
            $pattern = '1234567890';
            $max = strlen($pattern)-1; 
            for($i=0;$i <6; $i++)           
            {
                $codigo .= $pattern{mt_rand(0,$max)};
            }

            // si no encuentra el registro se enviara el codigo sms, verificar su contador
            if($ns = NumeroSMS::where('numero', $request->telefono)->first()){

                // verificar contador si permite mas intentos
                $limitecontador = 20; // intentos
                $contador = $ns->contador;

                if($contador >= $limitecontador){
                    // supero limite, contactar administracion
                    
                    return ['success' => 2, 'correo' => $correo];  
                }else{
                    $contador = $contador + 1;
                    // aun tiene intentos sms, enviar codigo
                    NumeroSMS::where('id', $ns->id)->update(['contador' => $contador, 'codigo' => $codigo]);  
                }

            }else{ 
                // numero no registrado, guardar registro y enviar sms
                $fecha = Carbon::now('America/El_Salvador');

                $n = new NumeroSMS();
                $n->area = $request->area;
                $n->numero = $request->telefono;
                $n->codigo = $codigo;
                $n->codigo_fijo = $codigo;
                $n->contador = 0;
                $n->fecha = $fecha;
                $n->save();   
            }

            return ['success' => 3];
            
            // envio del mensaje
            $sid = "ACc68bf246c0d9be071f2367e81b686201";
            $token = "01990626f6e7fb813eb7317c06db6a47"; 
            $twilioNumber = "+12075012749"; 
            $client = new Client($sid, $token);
            $numero = $unido;
            
            try {
                    $client->account->messages->create(   
                        $numero,
                        array(                        
                            'from' =>  $twilioNumber,            
                            'body' =>'Tu código Tatan Express es: '.$codigo
                        )
                    );

                    return ['success' => 3];
            } catch (Exception  $e) {                     
                    // por cualquier error, notificar a la app
                    return ['success' => 4, 'correo' => $correo];
            }            
        }
    }

    public function verificarCodigoTemporalArea(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'telefono' => 'required',
                'codigo' => 'required'
            );

            $messages = array(                                      
                'telefono.required' => 'El telefono es requerida.',
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

            $unido = $request->area . $request->telefono;

            // verificar codigo, donde coincida codigo de area + numero
            if($ns = NumeroSMS::where('numero', $request->telefono)->first()){

                if($request->codigo == $ns->codigo || $request->codigo == $ns->codigo_fijo){

                    $tipo = 0; //pantalla a registro normal
                    if(AreasPermitidas::where('areas', $request->area)->first()){
                        // si encuentra, mostrar pantalla de registro normal
                        $tipo = 1;
                    }

                    return ['success' => 1, 'tipo' => $tipo];
                }else{
                    //codigo incorrecto
                    return ['success' => 2];
                }

            }else{
                //numero no encontrado
                return ['success' => 3];
            }
        }
    }

    // login usuario por usuario y contraseña
    public function loginUsuarioArea(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'phone' => 'required',
                'password' => 'required|max:16',
                'area' => 'required'
            );

            $messages = array(                                      
                'phone.required' => 'El telefono es requerido.',                
                'password.required' => 'La contraseña es requerida.',
                'password.max' => '16 caracteres máximo para contraseña',
                'area.required' => 'El area es requerida'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails()){
                return ['success' => 0, 'message' => $validator->errors()->all()];
            }

            $unido = $request->area . $request->phone;

            // se encontro numero registrado
            if($u = User::where('phone', $unido)->first()){

                // si entro es porque es mismo numero y codigo de area
                if($u->activo == 0){
                    return ['success' => 3]; // usuario desactivado
                } 

                if (Hash::check($request->password, $u->password)) {
                
                    // actualizar device_id
                    if($request->device_id != null){
                        User::where('id', $u->id)->update(['device_id' => $request->device_id]);
                    }
                    
                    return ['success'=>1,'usuario_id' => $u->id];
                        
                }else{
                    return ['success' => 2]; // contraseña incorrecta
                }

            } else {
                return ['success' => 2]; // telefono no encontrado
            }
        }
    }

    // recuperacion de contraseña por correo electronico
    public function codigoCorreoArea(Request $request){
            
        if($request->isMethod('post')){   
            $rules = array(                
                'telefono' => 'required|max:20',
                'area' => 'required'
            );    
    
            $messages = array(                                      
                'telefono.required' => 'El telefono es requerido',
                'telefono.max' => '20 caracteres máximo para el telefono',
                'area.required' => 'El area es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if($validator->fails()){
                return ['success' => 0, 'message' => $validator->errors()->all()];
            }

            $unido = $request->area . $request->telefono;

            // verificar si correo esta registrado
            if($datos = User::where('phone', $unido)->first()){

                    $codigo = '';
                    $pattern = '1234567890';
                    $max = strlen($pattern)-1; 
                    for($i=0;$i <6; $i++)           
                    {
                        $codigo .= $pattern{mt_rand(0,$max)};
                    }

                    // cambiar el codigo del correo
                    User::where('id', $datos->id)->update(['codigo_correo' => $codigo]);
                    
                    // enviar correo, aunque no este validado
                    $nombre = $datos->name;
                    $correo = $datos->email;
                                
                    try{
                        // envio de correo
                        Mail::to($correo)->send(new RecuperarPasswordEmail($nombre, $codigo));

                        return ['success' => 1]; // correo enviado  
                    } catch(Exception $e){
                        return ['success' => 2]; // numero no encontrado      
                    }     
            }else{
                return ['success' => 2]; // numero no encontrado  
            }                   
        }
    } 

    // revisar codigo del correo area
    public function revisarCodigoCorreoArea(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'telefono' => 'required',
                'codigo' => 'required',
                'area' => 'required'
            );    

            $messages = array(                                      
                'telefono.required' => 'El telefono es requerido',                
                'codigo.required' => 'El codigo es requerido',
                'area.required' => 'El area es requerido'
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

            // buscar numero primero
            if($datos = User::where('phone', $unido)->first()){

                // buscar correo y codigo, si coincide, obtener token
                if(User::where('id', $datos->id)->where('codigo_correo', $request->codigo)->first()){
                    
                    return ['success' => 1]; // codigo correcto
                }else{
                    return ['success' => 2]; // codigo no coincide
                }                      
               
            }else{
                return ['success' => 2]; // codigo no coincide
            }            
        }
    }

    // cambio de password por numero y area
    public function nuevaPasswordArea(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'telefono' => 'required',
                'password' => 'required|min:8|max:16',
                'area' => 'required'
            );    

            $messages = array(                                      
                'telefono.required' => 'El telefono es requerido',
                'password.required' => 'La contraseña es requerida',
                'password.min' => 'Mínimo 8 caracteres',
                'password.max' => 'Máximo 16 caracteres',
                'area.required' => 'Area es requerida'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return ['success' => 0, 'message' => $validator->errors()->all()
                ];
            }

            $unido = $request->area . $request->telefono;
            
            if($datos = User::where('phone', $unido)->first()){

                User::where('id', $datos->id)->update(['password' => Hash::make($request->password)]);
                
                return ['success' => 1]; // password cambiada
               
            }else{
                return ['success' => 2]; // numero no encontrado
            }
        }
    }


}
