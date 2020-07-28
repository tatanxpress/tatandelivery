<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

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

            // verificar si existe el telefono
            if(User::where('phone', $request->telefono)->first()){
                return [
                    'success' => 1, 
                    'message' => 'Teléfono ya registrado'
                ];
            }

            // verificar si existe el correo
            if(User::where('email', $request->correo)->first()){
                return [
                    'success' => 2, 
                    'message' => 'correo electrónico ya registrado'
                ];
            }


            $fecha = Carbon::now('America/El_Salvador');

            $usuario = new User();
            $usuario->name = $request->nombre;
            $usuario->phone = $request->telefono;
            $usuario->password = Hash::make($request->password);
            $usuario->email = $request->correo;
            if($request->device_id == null){
                $usuario->device_id = "";
            }else{
                $usuario->device_id = $request->device_id;
            }
            
            $usuario->fecha = $fecha;
            $usuario->zonas_id = 2; // zona sin servicios, para que seleccionen una direccion

            if($usuario->save())
            {
                $insertedId = $usuario->id; 

                $token = JWTAuth::fromUser($usuario);

                return ['success'=>3, 'usuario_id'=> $insertedId, 'token' => $token];
                
            }else{
                return [
                    'success' => 4 // error al crear una cuenta                    
                ];
            }                      
        }   
    }

}
