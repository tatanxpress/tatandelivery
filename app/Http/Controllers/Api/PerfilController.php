<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Direccion;

class PerfilController extends Controller
{
     // cambiar contraseña con correo
     public function nuevaPassword(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'telefono' => 'required',
                'password' => 'required|min:8|max:16',
            );    

            $messages = array(                                      
                'telefono.required' => 'El telefono es requerido',
                'password.required' => 'La contraseña es requerida',
                'password.min' => 'Mínimo 8 caracteres',
                'password.max' => 'Máximo 16 caracteres',
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) 
                {
                    return [
                        'success' => 0, 
                        'message' => $validator->errors()->all()
                    ];
                }

            
            if(User::where('phone', $request->telefono)->first()){
                User::where('phone', $request->telefono)->update(['password' => Hash::make($request->password)]);
                return ['success' => 1]; 
            }
            return ['success' => 2];            
        }
    }

    // informacion del usuario
    public function infoPerfil(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'userid' => 'required'               
            );    

            $messages = array(                                      
                'userid.required' => 'El id es requerido'               
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) 
                {
                    return [
                        'success' => 0, 
                        'message' => $validator->errors()->all()
                    ];
                }
            
            if(User::where('id', $request->userid)->first()){
                $datos = User::where('id', $request->userid)->select('name AS nombre', 'email AS correo')->first();
                $nombre = $datos->nombre;                    
                $correo = $datos->correo;

                return ['success' => 1, 'nombre' => $nombre, 'correo' => $correo];
            }else{
                return ['success' => 2];
            }
        }
    }

    // cambiar contraseña con id
    
    public function cambiarPassword(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
                'password' => 'required|min:8|max:16',
            );    

            $messages = array(                                      
                'id.required' => 'El correo es requerido',  
                'password.required' => 'La contraseña es requerida',
                'password.min' => 'Mínimo 8 caracteres',
                'password.max' => 'Máximo 16 caracteres',
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) 
                {
                    return [
                        'success' => 0, 
                        'message' => $validator->errors()->all()
                    ];
                }

            if(User::where('id', $request->id)->first()){
                User::where('id', $request->id)->update(['password' => Hash::make($request->password)]);
                return ['success' => 1];
            }
            return ['success' => 2];            
        }
    }

    // cambiar datos del perfil
    public function editarPerfil(Request $request){
        if($request->isMethod('post')){  

            // validar datos en general sin fotografia       
             $reglaDatos = array(                
                'userid' => 'required',
                'nombre' => 'required|max:50',
                'correo' => 'required|max:100',
            );    
     
            $mensajeDatos = array(                                      
                'userid.required' => 'El id del usuario es requerido.',
                'nombre.required' => 'El nombre es requerido',
                'nombre.max' => 'El nombre necesita 50 caracteres máximo',
                'correo.required' => 'El correo electrónico es requerido',                                
                'correo.max' => 'El correo necesita 100 caracteres máximo',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if ( $validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            // entrara si enviamos imagen y la validara          
            if($request->hasFile('imagen')){                

                // validaciones para los datos
                $regla = array( 
                    'imagen' => 'required',
                );    
         
                $mensaje = array(
                    'imagen.required' => 'La imagen es requerida'                    
                    );
    
                $validar = Validator::make($request->all(), $regla, $mensaje );
    
                if ( $validar->fails()) 
                {
                    return [
                        'success' => 0, 
                        'message' => $validar->errors()->all()
                    ];
                }            
            }

            // validar correo si es unico
            if(User::where('email', $request->correo)->where('id', '!=', $request->userid)->first()){
                return [
                    'success' => 1 //correo ya registrado
                ];
            }

            // buscar usuario quien editara perfil
            if($usuario = User::where('id', $request->userid)->first()){                        

                if($request->hasFile('imagen')){    
                    // generar nombre para la imagen
                    $cadena = Str::random(20);
                    $tiempo = microtime(); 
                    $union = $usuario->id.$cadena.$tiempo;
                    // quitar espacios vacios
                    $nombreImagen = str_replace(' ', '_', $union);
                    
                    // guardar imagen en disco, solo podra ser accedida por aplicacion, no por navegador
                    $extension = '.'.$request->imagen->getClientOriginalExtension();
                    $avatar = $request->file('imagen'); 
                    $nombreFinal = $nombreImagen.$extension;
                    $upload = Storage::disk('usuario')->put($nombreFinal, \File::get($avatar));
                
                    if($upload){
                        // obtnener nombre foto anterior
                        $imagenOld = $usuario->imagen;       
                     
                        // guardar datos del perfil
                        $usuario->name = $request->nombre;
                        $usuario->email = $request->correo;
                        $usuario->imagen = $nombreFinal;

                        // comprobar si cambio el correo

                        if($usuario->save()){
                            
                            // si se guardo todo, borrar foto anterior
                            if(Storage::disk('usuario')->exists($imagenOld)){
                                Storage::disk('usuario')->delete($imagenOld);       
                            }

                            return [
                                'success' => 2 //datos guardados
                            ];
                        }else{
                            return [
                                'success' => 0 // error al guardar los datos                                
                            ];
                        }   
                    }else{
                        return [
                            'success' => 0 //error al subir la imagen
                        ];
                    }
                }// /if imagen
                else{

                    // no viene imagen asi que solo gurdar datos
                    $usuario->name = $request->nombre;
                    $usuario->email = $request->correo;

                    if($usuario->save()){

                        return [
                            'success' => 2 //datos guardados correctamente
                        ];
                    }
                }
            }else{
                return [
                    'success' => 0 //usuario no encontrado                    
                ];
            }   
        }
    }

    // lista de direcciones del usuario
    public function verDirecciones(Request $request){
        if($request->isMethod('post')){  

            $rules = array(                
                'userid' => 'required'                
            );    
     
            $messages = array(                                      
                'userid.required' => 'El id del usuario es requerido.',               
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            if(User::where('id', $request->userid)->first()){

             /*   $direccion = DB::table('direccion_usuario AS dir')            
            ->join('zonas', 'zonas.id', '=', 'dir.zonas_id',)
            ->select('dir.id', 'dir.nombre', 'dir.direccion', 'dir.numero_casa', 'dir.punto_referencia',
            'dir.telefono', 'dir.seleccionado', 'zonas.nombre AS nombreZona')
            ->where('user_id', $request->userid)
            ->get();*/

                return [
                    'success' => 1,
                    'direcciones' => $direccion,                
                ];
            }else{
                return ['succcess'=> 2];
            }
        }
    }




    // guardara las direcciones de los usuarios 
    public function guardarDireccion(Request $request){

        return 'entroo3';
      
        if($request->isMethod('post')){ 
            
            $reglaDatos = array(
                'userid' => 'required',
                'nombre' => 'required|max:100',
                'direccion' => 'required|max:400',
                'numero_casa' => 'max:30',
                'punto_referencia' => 'max:400',                
                'telefono' => 'required|max:20',
                'zona_id' => 'required',                
            );    
    
            $mensajeDatos = array(                                      
                'userid.required' => 'El id del usuario es requerido.',
                'nombre.required' => 'el nombre es requerida.',
                'nombre.max' => 'Máximo 75 caracteres.',
                'direccion.required' => 'La dirección es requerida.',
                'direccion.max' => '400 caracteres máximo direccion',
                'numero_casa.max' => 'Máximo 30 caracteres.',
                'punto_referencia.max' => 'Máximo 400 caracteres.',
                'zona_id.required' => 'El id de la zona es requerido.',                
                'telefono.required' => 'Teléfono es requerido.',
                'telefono.max' => '20 caracteres máximo para teléfono',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if ( $validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if(User::where('id', $request->userid)->first()){
        
                DB::beginTransaction();
            
                try {
                    $direccion = new Direccion();
                    $direccion->user_id = $request->userid;
                    $direccion->nombre = $request->nombre;
                    $direccion->direccion = $request->direccion;
                    if($request->numero_casa == null){
                        $direccion->numero_casa = "";    
                    }else{
                    $direccion->numero_casa = $request->numero_casa;
                    }

                    if($request->punto_referencia == null){
                        $direccion->punto_referencia = "";
                    }else{
                        $direccion->punto_referencia = $request->punto_referencia;
                    }
                    
                    $direccion->telefono = $request->telefono;
                    $direccion->zonas_id = $request->zona_id;                
                    $direccion->seleccionado = 1;
                    if($request->latitud == null){
                        $direccion->latitud = "";
                    }else{
                    $direccion->latitud = $request->latitud;
                    }
                    if($request->longitud == null){
                        $direccion->longitud = "";
                    }else{
                        $direccion->longitud = $request->longitud;
                    }
                    
                    if($direccion->save()){

                        // id de direccion que acaba de ser insertada
                        $id = $direccion->id;

                        try {
                            Direccion::where('user_id', $request->userid)->where('id', '!=', $id)->update(['seleccionado' => 0]);
                            User::where('id',$request->userid)->update(['zonas_id'=> $request->zona_id]);
                            DB::commit();

                            return [
                                'success' => 1 // direccion guardada correctamente
                            ];

                            }  catch (\Exception $ex) {
                                DB::rollback();
                                return [
                                    'success' => 2 // error                                    
                                ];
                            }                    
                    }else{
                        DB::rollback();
                        return [
                            'success' => 2 // error                            
                        ];
                    }
                } catch(\Throwable $e){
                    DB::rollback();
                    return [
                        'success' => 2 // error                        
                    ];
                }
            }else{
                return ['success' => 2];
            }
        }
    }

    

    // guardar direccion seleccionada
    public function seleccionarDireccion(Request $request){
        if($request->isMethod('post')){              
            
             $reglaDatos = array(                
                'dirid' => 'required',
                'userid' => 'required', 
            );    
      
            $mensajeDatos = array(                                      
                'dirid.required' => 'El id de la direccion es requerido.',
                'userid.required' => 'El id del usuario es requerido.',                
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if ( $validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }                      
               
            if(User::where('id', $request->userid)->first()){

                if(Direccion::where('id', $request->dirid)->first()){

                    try {

                        DB::beginTransaction();

                        // setear a 0
                        Direccion::where('user_id', $request->userid)->update(['seleccionado' => 0]);

                        // setear a 1 el id de la direccion que envia el usuario
                        Direccion::where('id', $request->dirid)->update(['seleccionado' => 1]);

                        // obtener el id de zonas_id que esta en la direccion seleccionada
                        $id = Direccion::where('id', $request->dirid)->pluck('zonas_id')->first();

                        // actualizar zona donde esta el usuario
                        User::where('id', $request->userid)->update(['zonas_id' => $id]);

                        DB::commit();

                        return [
                            'success' => 1 // direccion seleccionada
                        ];

                    }catch(Error $e) {
                        DB::rollback();
                        return [
                            'success' => 2 // error                                
                        ];
                    }catch(\Throwable $e){
                        DB::rollback();
                        return [
                            'success' => 0 // error                                
                        ];
                    }

                }else{
                    return ['success' => 2];
                }

            }else{
                return ['success' => 2];
            }           
        }
    }

    // eliminar direccion del usuario
    public function eliminarDireccion(Request $request){
        if($request->isMethod('post')){  
            $reglaDatos = array(                
                'dirid' => 'required',                
            );    
     
            $mensajeDatos = array(                                      
                'dirid.required' => 'El id de la direccion es requerido.',                
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if ( $validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }     
            
            if(Direccion::where('id', $request->dirid)->delete()){
                return [
                    'success' => 1 //eliminada                    
                ];
            }else{
                return [
                    'success' => 2 // error                    
                ];
            }
        }
    }



}
