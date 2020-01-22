<?php

namespace App\Http\Controllers\Api;

use App\AdminOrdenes;
use App\HorarioServicio;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Propietarios;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Mail;
use App\Mail\RecuperarPasswordEmail;
use App\Ordenes;
use App\OrdenesDescripcion;
use App\PagoPropietario;
use App\Producto;
use App\Servicios;
use App\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use OneSignal;
use App\OrdenesPendiente;

class PropietarioController extends Controller
{
    // login para propietario
    public function loginPropietario(Request $request){
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
         
            if($p = Propietarios::where('telefono', $request->phone)->first()){

                if($p->activo == 0){
                    return ['success' => 1]; // propietario no activo
                }

                if (Hash::check($request->password, $p->password)) {

                    $id = $p->id;
                    $pro = DB::table('servicios AS s')
                    ->join('propietarios AS p', 'p.servicios_id', '=', 's.id')
                    ->select('s.nombre')
                    ->where('p.id', $id)
                    ->first();
    
                    $nombre = $pro->nombre;

                    if($request->device_id != null){
                        Propietarios::where('id', $p->id)->update(['device_id' => $request->device_id]);
                    }
                    

                    return ['success' => 2, 'usuario_id' => $id, 'nombreservicio' => $nombre]; // login correcto
                }    else{
                    return ['success' => 3]; // contraseña incorrecta
                }
            }else{
                return ['success' => 4]; // datos incorrectos
            }
        }
    }

    // verificar si existe el telefono
    public function buscarTelefono(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'telefono' => 'required'
            );    

            $messages = array(                                      
                'telefono.required' => 'El telefono es requerido'             
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            if($p = Propietarios::where('telefono', $request->telefono)->first()){

                if($p->activo == 0){
                    return ['success'=>1];
                }
                return ['success'=>2]; // telefono encontrado
            }else{
                return ['success'=>3]; // numero no encontrado
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
            if($p = Propietarios::where('telefono', $request->telefono)->first()){

                if($p->activo == 0){
                    return ['success' => 1];
                }
                
                $codigo = '';
                $pattern = '1234567890';
                $max = strlen($pattern)-1; 
                for($i=0;$i <6; $i++)           
                {
                    $codigo .= $pattern{mt_rand(0,$max)};
                }

                // cambiar el codigo del correo
                Propietarios::where('telefono', $request->telefono)->update(['codigo_correo' => $codigo]);
                
                // enviar correo, aunque no este validado
                
                $nombre = $p->nombre;
                $correo = $p->correo;
                              
               try{
                // envio de correo
                Mail::to($correo)->send(new RecuperarPasswordEmail($nombre, $codigo));

                return [
                    'success' => 2,
                    'message' => 'Correo enviado'
                ]; 
                }   catch(Exception $e){
                    return [
                        'success' => 3 // correo error                        
                    ];       
                }
            }else{
                return [
                    'success' => 3 // telefono no encontrado
                ];
            }                   
        }  
    }
    
    // revisar codigo recibido del correo
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

            // buscar correo y codigo
            if($p = Propietarios::where('telefono', $request->telefono)->where('codigo_correo', $request->codigo)->first()){                

                if($p->activo == 0){
                    return ['success' => 1];
                }
                return ['success' => 2]; // coincide, pasar a cambiar contraseña
            }else{
                return ['success' => 3]; // codigo incorrecto
            }
        }
    }

    // cambio de contraseña
    public function nuevaPassword(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'telefono' => 'required',
                'password' => 'required|min:8|max:16',
            );

            $messages = array(                                      
                'telefono.required' => 'El correo es requerido',  
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

            if($p = Propietarios::where('telefono', $request->telefono)->first()){

                if($p->activo == 0){
                    return ['success' => 1];
                }

                Propietarios::where('telefono', $request->telefono)->update(['password' => Hash::make($request->password)]);
            
                return ['success' => 2];  // contraseña cambiada
            }else{
                return ['success' => 3];  // telefono no encontrado
            }
        }
    }

    // ver nuevas ordenes
    public function nuevaOrdenes(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
            );

            $messages = array(                                      
                'id.required' => 'El id propietario es requerido',
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Propietarios::where('id', $request->id)->first()){

                if($p->activo == 0){
                    return ['success'=> 1];
                }

                $orden = DB::table('ordenes AS o')                
                ->select('o.id', 'o.precio_total', 'o.precio_envio', 'o.nota_orden', 'o.fecha_orden', 'o.envio_gratis')
                ->where('o.servicios_id', $p->servicios_id)
                ->where('o.visible_p', 1)
                ->get();
            
                foreach($orden as $o){
                    $fechaOrden = $o->fecha_orden;
                    $hora = date("h:i A", strtotime($fechaOrden));
                    $fecha = date("d-m-Y", strtotime($fechaOrden));
                    $o->fecha_orden = $hora . " " . $fecha;

                    // resta para total del propietario
                    $precio = $o->precio_total;
                    $envio = $o->precio_envio;
                    $total = $precio - $envio;
                    $o->total = $total;
                }

                return ['success' => 2, 'ordenes' => $orden]; 
            }else{
                return ['success' => 3]; // propietario no encontrado
            }
        }
    }

    // horarios del servicio
    public function verHorarios(Request $request){

        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
            );

            $messages = array(                                      
                'id.required' => 'El id propietario es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) 
                {
                    return [
                        'success' => 0, 
                        'message' => $validator->errors()->all()
                    ];
                }

            if($p = Propietarios::where('id', $request->id)->first()){

                if($p->activo == 0){
                    return ['success' => 1];
                }

                $servicios = HorarioServicio::where('servicios_id', $p->servicios_id)->get();

                foreach($servicios as $s){
                    $s->hora1 = date("h:i A", strtotime($s->hora1));
                    $s->hora2 = date("h:i A", strtotime($s->hora2));
                    $s->hora3 = date("h:i A", strtotime($s->hora3));
                    $s->hora4 = date("h:i A", strtotime($s->hora4));                    
                }

                return ['success' =>2, 'horario' => $servicios];

            }else{
                return ['success'=> 3];
            }
        }
    }

    // informacion de disponibilidad
    public function informacionDisponibilidad(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
            );

            $messages = array(                                      
                'id.required' => 'El id propietario es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) 
                {
                    return [
                        'success' => 0, 
                        'message' => $validator->errors()->all()
                    ];
                }

            if($p = Propietarios::where('id', $request->id)->first()){

                if($p->activo == 0){
                    return ['success' => 1];
                }

                $pro = DB::table('servicios AS s')
                ->join('propietarios AS p', 'p.servicios_id', '=', 's.id')
                ->select('s.cerrado_emergencia', 'p.disponibilidad')
                ->where('p.id', $request->id)
                ->first();

                $emergencia = $pro->cerrado_emergencia;
                $disponibilidad = $pro->disponibilidad;
                return ['success'=> 2, 'cerrado' => $emergencia, //0: no esta cerrado
                'disponibilidad'=>$disponibilidad]; //1: si esta disponible

            }else{
                return ['success'=> 3];
            }
        }
    }

    // configuracion para ordenes automatica
    public function guardarTiempo(Request $request){
        if($request->isMethod('post')){   
            $rules = array(
                'id' => 'required',
                'valor1' => 'required', 
                'tiempo' => 'required'
            );

            $messages = array(                                      
                'id.required' => 'El id propietario es requerido',
                'valor1.required' => 'El estado 1 es requerido',
                'tiempo.required' => 'El tiempo es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) 
                {
                    return [
                        'success' => 0, 
                        'message' => $validator->errors()->all()
                    ];
                }

            if($p = Propietarios::where('id', $request->id)->first()){

               
                if($se = Servicios::where('id', $p->servicios_id)->first()){

                    // guardar valor1 y tiempo
                    if($request->valor1 == 1){
                        Servicios::where('id', $se->id)->update(['orden_automatica' => 1, 'tiempo' => $request->tiempo]);
                    }else{
                        // desactivar estado
                        Servicios::where('id', $se->id)->update(['orden_automatica' => 0]);
                    }

                    // actualizar estados
                   
                    return ['success'=> 1];
                }else{
                    return ['success'=> 2]; // servicio no encontrado    
                }
            }else{
                return ['success'=> 2]; // propietario no encontrado
            }
        }
    }

    // disponibilidad del servicio
    public function modificarDisponibilidad(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
                'valor1' => 'required',
                'valor2' => 'required'
            );

            $messages = array(                                      
                'id.required' => 'El id propietario es requerido',
                'valor1.required' => 'El estado 1 es requerido',
                'valor2.required' => 'El estado 2 es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) 
                {
                    return [
                        'success' => 0, 
                        'message' => $validator->errors()->all()
                    ];
                }

            if($p = Propietarios::where('id', $request->id)->first()){

                if($p->activo == 0){
                    return ['success' => 1];
                }
                
                if(Servicios::where('id', $p->servicios_id)->first()){

                    // actualizar estados
                    Servicios::where('id', $p->servicios_id)->update(['cerrado_emergencia' => $request->valor1]);
                    Propietarios::where('id', $request->id)->update(['disponibilidad' => $request->valor2]);

                    return ['success'=> 2];
                }else{
                    return ['success'=> 3]; // servicio no encontrado    
                }
            }else{
                return ['success'=> 3]; // propietario no encontrado
            }
        }
    }

    // informacion del perfil
    public function informacionCuenta(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
            );

            $messages = array(                                      
                'id.required' => 'El id propietario es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) 
                {
                    return [
                        'success' => 0, 
                        'message' => $validator->errors()->all()
                    ];
                }

            if($p = Propietarios::where('id', $request->id)->first()){

                if($p->activo == 0){
                    return ['success'=> 1];    
                }

                $nombre = $p->nombre;
                $correo = $p->correo;

                return ['success'=> 2, 'nombre' => $nombre,
                'correo'=> $correo];
            }else{
                return ['success'=> 2];
            }
        }
    }

    // cambiar el correo el propietario
    public function cambiarCorreo(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
                'correo' => 'required'
            );

            $messages = array(                                      
                'id.required' => 'El id propietario es requerido',
                'correo.required' => 'El correo es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) 
                {
                    return [
                        'success' => 0, 
                        'message' => $validator->errors()->all()
                    ];
                }

            if($p = Propietarios::where('id', $request->id)->first()){

                if($p->activo == 0){
                    return [
                        'success' => 1                        
                    ];
                }

                // verificar si existe el correo
                if(Propietarios::where('correo', $request->correo)->where('id', '!=', $request->id)->first()){                
                    return [
                        'success' => 2              
                    ];
                }

                // actualizar correo
                Propietarios::where('id', $request->id)->update(['correo' => $request->correo]);
                
                return ['success'=> 3];
            }else{
                return ['success'=> 4];
            }
        }
    }

    // cambia la contraseña el propietario
    public function actualizarPassword(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
                'password' => 'required'
            );

            $messages = array(                                      
                'id.required' => 'El id propietario es requerido',
                'password.required' => 'El password es requerida'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Propietarios::where('id', $request->id)->first()){

                if($p->activo == 0){
                    return ['success'=> 1];    
                }

                Propietarios::where('id', $request->id)->update(['password' => Hash::make($request->password)]);
                                
                return ['success'=> 2];
            }else{
                return ['success'=> 3];
            }            
        }
    }

    // estado adomicilio
    public function estadoAdomicilio(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
            );

            $messages = array(                                      
                'id.required' => 'El id propietario es requerido',
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Propietarios::where('id', $request->id)->first()){

                if($p->activo == 0){
                    return ['success'=> 1];
                }

                $estado = DB::table('servicios AS o')
                ->join('propietarios AS p', 'p.servicios_id', '=', 'o.id')
                ->select('o.envio_gratis')
                ->where('p.id', $p->id)
                ->first();

                $envio = $estado->envio_gratis;

                // utilizara motorista del servicio
                if($envio == 1){
                    $motorista = DB::table('motoristas_asignados AS ma')
                    ->join('motoristas AS m', 'm.id', '=', 'ma.motoristas_id')
                    ->select('m.nombre', 'ma.servicios_id')
                    ->where('ma.servicios_id', $p->servicios_id)
                    ->get();

                    return ['success'=> 2, 'motorista' => $motorista];
                }
                                
                return ['success'=> 3];
            }else{
                return ['success'=> 4];
            }            
        }
    }

     // estado contestacion de ordenes por tiempo
     public function estadoAutomatico(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
            );

            $messages = array(                                      
                'id.required' => 'El id propietario es requerido',
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Propietarios::where('id', $request->id)->first()){

                $estado = DB::table('servicios AS s')
                ->join('propietarios AS p', 'p.servicios_id', '=', 's.id')
                ->select('s.orden_automatica', 's.tiempo', 'p.id')
                ->where('p.id', $p->id)
                ->first();

                $automatica = $estado->orden_automatica;
                $tiempo = $estado->tiempo;
                
                return ['success'=> 1, 'automatica' => $automatica, 'tiempo' => $tiempo];
            }else{
                return ['success'=> 2];
            }            
        }
    } 

    // ver productos
    public function verProductos(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
            );
 
            $messages = array(                                      
                'id.required' => 'El id propietario es requerido',
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Propietarios::where('id', $request->id)->first()){

                if($p->activo == 0){
                    return ['success' => 1];
                }

                // buscar lista de productos
                $tipo = DB::table('servicios_tipo AS st')    
                ->join('servicios AS s', 's.id', '=', 'st.servicios_1_id')
                ->select('st.id AS tipoId', 'st.nombre AS nombreSeccion')
                ->where('st.servicios_1_id', $p->servicios_id)
                ->orderBy('st.posicion', 'ASC')
                ->get();
    
                $resultsBloque = array();
                $index = 0;
    
                foreach($tipo  as $secciones){
                    array_push($resultsBloque,$secciones);          
                
                    $subSecciones = DB::table('producto AS p')  
                    ->select('p.id AS idProducto','p.nombre AS nombreProducto', 
                            'p.descripcion AS descripcionProducto',
                            'p.imagen AS imagenProducto', 'p.precio AS precioProducto',
                            'p.unidades', 'p.utiliza_cantidad', 'p.utiliza_imagen')
                    ->where('p.servicios_tipo_id', $secciones->tipoId)
                    ->where('p.activo', 1) // para inactivarlo solo administrador
                    ->orderBy('p.posicion', 'ASC')
                    ->get();
                    
                    $resultsBloque[$index]->productos = $subSecciones;
                    $index++;
                }
                                
                return ['success'=> 2, 'productos'=> $tipo];
            }else{
                return ['success'=> 3];
            }            
        }
    }

    // ver producto individual
    public function verProductosIndividual(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'productoid' => 'required',
            );
 
            $messages = array(                                      
                'productoid.required' => 'El id producto es requerido',
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if(Producto::where('id', $request->productoid)->first()){
                                
                $producto = DB::table('servicios AS s')
                ->join('servicios_tipo AS st', 'st.servicios_1_id', '=', 's.id')
                ->join('producto AS p', 'p.servicios_tipo_id', '=', 'st.id')
                ->select('p.id', 'p.nombre', 'p.descripcion', 'p.precio', 
                'p.unidades', 'p.imagen', 'p.activo', 'p.disponibilidad', 'p.utiliza_cantidad', 'p.utiliza_imagen')
                ->where('p.id', $request->productoid)
                ->where('p.activo', 1)
                ->get();
                
                return ['success'=> 1, 'producto' => $producto];

            }else{
                return ['success'=> 2];
            }            
        }
    }

    // actualizar producto
    public function actualizarProducto(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
                'productoid' => 'required',
                'estado1' => 'required',
                'estado2' => 'required',
                'precio' => 'required',
                'unidades' => 'required'
            );
 
            $messages = array(                                      
                'id.required' => 'El id propietario es requerido',
                'productoid.required' => 'El id producto es requerido',
                'estado1.required' => 'El estado 1 es requerido',
                'estado2.required' => 'El estado 2 es requerido',
                'precio.required' => 'El precio es requerido',
                'unidades.required' => 'La unidad es requerido',
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if(Producto::where('id', $request->productoid)->first()){

                if($p = Propietarios::where('id', $request->id)->first()){
                    if($p->activo == 0){
                        return ['success'=> 1];
                    }
                }
                                
                Producto::where('id', $request->productoid)->update(['precio' => $request->precio,
                'unidades' => $request->unidades, 'disponibilidad' => $request->estado1,
                'utiliza_cantidad'=>$request->estado2, 'unidades' => $request->unidades]);
                
                return ['success'=> 2];

            }else{
                return ['success'=> 3];
            }            
        }
    }

    // buscador de productos
    public function buscarProducto(Request $request){
        
        if($request->isMethod('post')){ 
            $reglaDatos = array(                
                'id' => 'required',   
                'nombre' => 'required',             
            );    
                  
            $mensajeDatos = array(                                      
                'id.required' => 'El id del propietario es requerido',
                'nombre.required' => 'El nombre es requerido'
                );
            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );
            if($validarDatos->fails())
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            if($p = Propietarios::where('id', $request->id)->first()){
                $productos = DB::table('servicios AS s')
                ->join('servicios_tipo AS st', 'st.servicios_1_id', '=', 's.id')
                ->join('producto AS p', 'p.servicios_tipo_id', '=', 'st.id')
                ->select('p.id', 'p.nombre', 'p.imagen', 'p.precio', 'p.disponibilidad', 'p.es_promocion', 'p.utiliza_imagen')
                ->where('s.id', $p->servicios_id)                
                ->where('p.activo', 1)                
                ->where('p.nombre', 'like', '%' . $request->nombre . '%')
                ->get();

                return ['productos' => $productos];
            }
            else{
                return ['success'=>2];
            }            
        }
    }

    // ver producto de la orden
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

        // buscar la orden
        if(Ordenes::where('id', $request->ordenid)->first()){
            $producto = DB::table('ordenes AS o')
                        ->join('ordenes_descripcion AS od', 'od.ordenes_id', '=', 'o.id')
                        ->join('producto AS p', 'p.id', '=', 'od.producto_id')
                        ->select('od.id AS productoID', 'p.nombre', 'p.utiliza_imagen', 'p.imagen', 'od.precio', 'od.cantidad')
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
                        return ['success' => 2];
        }
    }

    // ver producto individual de la orden
    public function ordenProductosIndividual(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenesid' => 'required' // id tabla orden_descripcion               
            );
        
            $mensajeDatos = array(                                      
                'ordenesid.required' => 'El id de orden descripcion es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  
            
            // producto descripcion
            if(OrdenesDescripcion::where('id', $request->ordenesid)->first()){
            
                $producto = DB::table('ordenes_descripcion AS o')
                    ->join('producto AS p', 'p.id', '=', 'o.producto_id')
                    ->select('p.imagen', 'p.nombre', 'p.descripcion', 'p.utiliza_imagen', 'o.precio', 'o.cantidad', 'o.nota')
                    ->where('o.id', $request->ordenesid)
                    ->get();

                    foreach($producto as $p){
                        $cantidad = $p->cantidad;
                        $precio = $p->precio;
                        $multi = $cantidad * $precio;
                        $p->multiplicado = number_format((float)$multi, 2, '.', '');
                    }
            
                return ['success' => 1, 'producto' => $producto];
            }else{
                return ['success' => 2];
            }
        }
    }

    // ver orden estados, cuando aun es nueva orden
    public function verOrdenPorID(Request $request){
        if($request->isMethod('post')){

            // validaciones para los datos
            $reglaDatos = array(
                'ordenid' => 'required'               
            ); 
        
            $mensajeDatos = array(                                      
                'ordenid.required' => 'El id del usuario es requerido.'            
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            if(Ordenes::where('id', $request->ordenid)->first()){
            
                $orden = DB::table('ordenes AS o')
                    ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
                    ->select('o.id', 's.nombre', 'o.precio_total',
                    'o.fecha_orden', 'o.precio_envio', 'o.estado_2', 'o.fecha_2',
                    'o.hora_2', 'o.estado_3', 'o.fecha_3', 'o.estado_8', 'o.fecha_8',
                     'o.mensaje_8', 's.orden_automatica', 's.tiempo', 'o.cancelado_cliente')
                    ->where('o.id', $request->ordenid)
                    ->get();
               
                // obtener fecha orden y sumarle tiempo si estado es igual a 2
                foreach($orden as $o){
                    
                    if($o->estado_2 == 1){ // propietario da el tiempo de espera
                        
                        $fechaE2 = $o->fecha_2;
                        $hora2 = date("h:i A", strtotime($fechaE2));
                        $fecha2 = date("d-m-Y", strtotime($fechaE2));
                    
                        $o->fecha_2 = $hora2 . " " . $fecha2;
                    }

                    if($o->estado_3 == 1){
                        $fechaE3 = $o->fecha_3;
                        $hora3 = date("h:i A", strtotime($fechaE3));
                        $fecha3 = date("d-m-Y", strtotime($fechaE3));                      
                        $o->fecha_3 = $hora3 . " " . $fecha3;
                    }
                
                    if($o->estado_8 == 1){
                        $fechaE8 = $o->fecha_8;
                        $hora8 = date("h:i A", strtotime($fechaE8));
                        $fecha8 = date("d-m-Y", strtotime($fechaE8));
                        $o->fecha_8 = $hora8 . " " . $fecha8;
                    }

                        $fechaOrden = $o->fecha_orden;
                        $hora = date("h:i A", strtotime($fechaOrden));
                        $fecha = date("d-m-Y", strtotime($fechaOrden));
                        $o->fecha_orden = $hora . " " . $fecha;  
                }
            
                return ['success' => 1, 'ordenes' => $orden];
            }else{
                return ['success' => 2];
            }
        }
    }

    // el propietario responde con tiempo de espera
    // verificar si es orden automatica hasta estado 4
    public function procesarOrdenEstado2(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenid' => 'required',
                'tiempo' => 'required',
                'tipo' => 'required' // seguridad para elegir bien la hora
            );
        
            $mensajeDatos = array(                                      
                'ordenid.required' => 'El id de la orden es requerido',
                'tiempo.required' => 'El tiempo es requerido',
                'tipo.required' => 'El tipo es requerido',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0,
                    'message' => $validarDatos->errors()->all()
                ];
            }  
            
            if($or = Ordenes::where('id', $request->ordenid)->first()){

                // aun no se ha establecido tiempo de espera
                if($or->estado_2 == 0){
                   
                    $fecha = Carbon::now('America/El_Salvador');

                    // verificar si sera orden automatica, iniciar preparacion de orden
                    $servicioid = $or->servicios_id;
                    $datosServicio = Servicios::where('id', $servicioid)->first();

                    // el tipo de orden automatica o no, cambio al momento de agregar la hora
                    if($request->tipo != $datosServicio->orden_automatica){
                        return ['success' => 1];
                    }

                    $tiempo = 0;
                    if($datosServicio->orden_automatica == 1){
                        $tiempo = $datosServicio->tiempo + 5;
                    }else{
                        $tiempo = $request->tiempo + 5;
                    }

                    $titulo = "";
                    $mensaje = "";

                    // contestacion hasta estado 4
                    if($datosServicio->orden_automatica == 1){
                        $titulo = "Orden iniciada";
                        $mensaje = "Seguir el estado de su orden";
                        Ordenes::where('id', $request->ordenid)->update(['estado_2' => 1,
                        'fecha_2' => $fecha, 'hora_2' => $tiempo, 'estado_3' => 1, 'fecha_3' => $fecha,
                        'estado_4' => 1, 'fecha_4' => $fecha, 'visible_p' => 0, 'visible_p2' => 1, 'visible_p3' => 1]);

    
                    }else{
                        $titulo = "Tiempo de preparación";
                        $mensaje = "Revisar tiempo de espera";
                        Ordenes::where('id', $request->ordenid)->update(['estado_2' => 1,
                        'fecha_2' => $fecha, 'hora_2' => $tiempo]);
                    }

                    // mandar notificacion al cliente
                    $usuario = User::where('id', $or->users_id)->first();
                    $pilaUsuarios = $usuario->device_id;
                    
                    $alarma = 2;
                    $color = 3;
                    $icono = 3;

                    if(!empty($pilaUsuarios)){
                        $this->envioNoticacion($titulo, $mensaje, $pilaUsuarios, $alarma, $color, $icono);
                    }

                    return ['success' => 2]; 
                }else{
                    return ['success'=> 3];
                }
            }else{
                return ['success'=> 4];
            }
        }
    }
    
    // calcelar la orden
    public function cancelarOrden(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenid' => 'required' 
            );
        
            $mensajeDatos = array(                                      
                'ordenid.required' => 'El id de orden es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($o = Ordenes::where('id', $request->ordenid)->first()){
               
                // cancelar si aun no ha sido cancelada
                // esta orden aun no ha iniciado su preparacion
                if($o->estado_8 == 0 && $o->estado_4 == 0){

                    $mensaje = $request->mensaje;
                    if(empty($mensaje) || $mensaje == null){
                        $mensaje = "";
                    }

                    $fecha = Carbon::now('America/El_Salvador');
                    Ordenes::where('id', $request->ordenid)->update(['estado_8' => 1, 'visible_p' => 0, 
                    'cancelado_propietario' => 1, 'fecha_8' => $fecha, 'mensaje_8' => $mensaje]);
                  
                    // notificar a los propietario de la orden cancelada
                    $usuario = User::where('id', $o->users_id)->first();
                    $pilaUsuarios = $usuario->device_id;

                    $titulo = "Orden cancelada";
                    $mensaje = "Lo sentimos, su orden fue cancelada";
                    $alarma = 2;
                    $color = 1;
                    $icono = 5;                
                    if(!empty($pilaUsuarios)){
                        $this->envioNoticacion($titulo, $mensaje, $pilaUsuarios, $alarma, $color, $icono);
                    }
            
                    return ['success' => 1];

                }else{
                    return ['success' => 2]; // ya cancelada
                }
            }else{
                return ['success' => 3]; // no encontrada
            }
        }
    }

    // borrar orden el propietario, quitara de su vista
    public function borrarOrden(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenid' => 'required' 
            );
        
            $mensajeDatos = array(                                      
                'ordenid.required' => 'El id de orden es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            // buscar orden
            if(Ordenes::where('id', $request->ordenid)->first()){               
                
                // propietario ya no vera la orden
                Ordenes::where('id', $request->ordenid)->update(['visible_p' => 0]);
                return ['success' => 1];
                
            }else{
                return ['success' => 2]; // no encontrada
            }
        }
    }

     // el propietario inicia a preparar la orden
     public function procesarOrdenEstado4(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenid' => 'required',
            );
        
            $mensajeDatos = array(                                      
                'ordenid.required' => 'El id de la orden es requerido',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0,
                    'message' => $validarDatos->errors()->all()
                ];
            }  
            

            if($or = Ordenes::where('id', $request->ordenid)->first()){               

                if($or->estado_4 == 0 && $or->estado_8 == 0){

                    $fecha = Carbon::now('America/El_Salvador');

                    Ordenes::where('id', $request->ordenid)->update(['estado_4' => 1,
                    'fecha_4' => $fecha, 'visible_p' => 0, 'visible_p2' => 1, 'visible_p3' => 1]);

                   
                     // mandar notificacion al cliente
                    $usuario = User::where('id', $or->users_id)->first();
                    $fcm = $usuario->device_id;

                    $titulo = "Orden iniciada";
                    $mensaje = "Su orden empieza a prepararse";
                    $alarma = 2;
                    $color = 3;
                    $icono = 4;
                    if(!empty($fcm)){                       
                        // COMO ES DEL CLIENTE, AL REGISTRARSE ESTE OBTIENE SU DEVICE_ID
                        $this->envioNoticacion($titulo, $mensaje, $fcm, $alarma, $color, $icono);
                    } 

                    // mandar notificacion a los motoristas asignados al servicio
                    $moto = DB::table('motoristas_asignados AS ms')
                    ->join('motoristas AS m', 'm.id', '=', 'ms.motoristas_id')
                     ->select('m.activo', 'm.disponible', 'ms.servicios_id')            
                    ->where('m.activo', 1)
                    ->where('m.disponible', 1)
                    ->where('ms.servicios_id', $or->servicios_id)
                    ->get();

                     $pilaUsuarios = array();
                    foreach($moto as $p){  
                        if(!empty($p->device_id)){
                            if($p->device_id != "0000"){
                                array_push($pilaUsuarios, $p->device_id); 
                            }                        
                        }
                    }

                    $titulo1 = "Solicitud Nueva";
                    $mensaje1 = "Se necesita motorista";
                    $alarma1 = 1;
                    $color1 = 3;
                    $icono1 = 2;
                    if(!empty($pilaUsuarios)){
                       
                        $this->envioNoticacion($titulo1, $mensaje1, $pilaUsuarios, $alarma1, $color1, $icono1);
                    }else{
                       
                        // SINO HAY MOTORISTA DISPONIBLE A ESE SERVICIO, MANDAR AVISO A ADMINISTRADORES
                        // GUARDAR REGISTROS
                        
                        $osp = new OrdenesPendiente;
                        $osp->ordenes_id = $request->ordenid; 
                        $osp->fecha = $fecha;
                        $osp->activo = 1;
                        $osp->tipo = 3;
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
                            $titulo = "Orden sin Propietario";
                            $mensaje = "Servicio: ".$nombreServicio;
                            $alarma = 1; //sonido alarma
                            $color = 1; // color rojo
                            $icono = 1; // campana

                                $this->envioNoticacion($titulo, $mensaje, $pilaAdministradores, $alarma, $color, $icono);
                            
                        }

                    } 
                    return ['success' => 1]; // inicia preparacion

                }else{
                    return ['success'=> 2]; 
                }
            }else{
                return ['success'=> 3];
            }
        }
    }

      // ver ordenes preparando
      public function preparandoOrdenes(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
            );
 
            $messages = array(                                      
                'id.required' => 'El id propietario es requerido',
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Propietarios::where('id', $request->id)->first()){
                $orden = DB::table('ordenes AS o')                
                ->select('o.id', 'o.precio_total', 'o.estado_8', 'o.precio_envio', 'o.nota_orden', 
                'o.fecha_4', 'o.hora_2')
                ->where('o.estado_8', 0) // ordenes no canceladas
                ->where('o.servicios_id', $p->servicios_id)
                ->where('o.visible_p2', 1) // estan en preparacion
                ->where('o.visible_p3', 1) // aun sin terminar de preparar 
                ->where('o.estado_4', 1) // orden estado 4 preparacion
                ->get();
           
                foreach($orden as $o){
                    $fechaOrden = $o->fecha_4;
                    $hora = date("h:i A", strtotime($fechaOrden));
                    $fecha = date("d-m-Y", strtotime($fechaOrden));
                    $o->fecha_orden = $hora . " " . $fecha;

                  
                    $precio = $o->precio_total;
                  
                    $o->total = number_format((float)$precio, 2, '.', '');

                    $time1 = Carbon::parse($o->fecha_4);
                    $horaEstimada = $time1->addMinute($o->hora_2)->format('h:i A d-m-Y');
                    $o->horaEstimada = $horaEstimada; 
                }

                return ['success' => 1, 'ordenes' => $orden]; 
            }else{
                return ['success' => 2];
            }
        }
    }

     // ver orden estados cuando 
     public function verOrdenPreparandoPorID(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenid' => 'required'               
            );
        
            $mensajeDatos = array(                                      
                'ordenid.required' => 'El id orden es requerido.'            
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            if(Ordenes::where('id', $request->ordenid)->first()){
            
                $orden = DB::table('ordenes AS o')                    
                    ->select('o.id', 'o.fecha_4', 'o.hora_2', 'o.tardio', 'o.fecha_tardio')
                    ->where('o.id', $request->ordenid)
                    ->get();
                               
                foreach($orden as $o){

                    $inicio = Carbon::parse($o->fecha_4);
                    // SERVICIO TIENE EL TIEMPO de 5 MINUTOS MAXIMO PARA PODER CANCELAR LA ORDEN CUANDO YA LA HAYA ACEPTADO
                    $sumado = $inicio->addMinute(5)->format('Y-m-d H:i:s');                    
                    $today = Carbon::now('America/El_Salvador')->format('Y-m-d H:i:s');
                                    
                    $d1 = new DateTime($sumado);
                    $d2 = new DateTime($today);

                     if ($d1 > $d2){
                         $o->excedido = 1; // aun puede cancelar orden
                     }else{
                         $o->excedido = 0; // ya no puede cancelar la orden
                     }

                    if($o->tardio == 1){
                    $fechat = $o->fecha_tardio;
                    $horat = date("h:i A", strtotime($fechat));
                    $fechat = date("d-m-Y", strtotime($fechat));
                    $o->fecha_tardio = $horat . " " . $fechat;
                    }

                    $tiempoExedido = Carbon::parse($o->fecha_4);
                    $horaEstimada = $tiempoExedido->addMinute($o->hora_2)->format('H:i:s d-m-Y');
                    $horaEstimadaFe = $tiempoExedido->addMinute($o->hora_2)->format('h:i A d-m-Y');
                    $o->horaEstimada = $horaEstimadaFe;
                    $d3 = new DateTime($horaEstimada);

                    // ver si orden supera el tiempo estimado
                    if ($d2 > $d3){
                        $o->estarde = 1; // ya es tarde
                    }else{
                        $o->estarde = 0; // aun hay tiempo para terminarla
                    }
                }
            
                return ['success' => 1, 'ordenes' => $orden];
            }else{
                return ['success' => 2];
            }
        }
    }

    // cancelar orden extraordinariamente
    public function cancelarOrdenExtraordinariamente(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenid' => 'required'               
            );
        
            $mensajeDatos = array(                                      
                'ordenid.required' => 'El id orden es requerido.'            
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            if(Ordenes::where('id', $request->ordenid)->first()){
               
                $orden = DB::table('ordenes AS o')                    
                ->select('o.id', 'o.fecha_4', 'o.hora_2', 'o.estado_5', 'o.estado_8', 
                'o.tardio', 'o.fecha_tardio', 'users_id')
                ->where('o.id', $request->ordenid)
                ->first();

                $fecha4 = $orden->fecha_4;
                $excedido = 0;

                $inicio = Carbon::parse($fecha4);
                // 10 MINUTOS MAXIMOS PARA PODER CANCELAR LA ORDEN    
                $sumado = $inicio->addMinute(10)->format('Y-m-d H:i:s');                    
                $today = Carbon::now('America/El_Salvador')->format('Y-m-d H:i:s');
                                
                $d1 = new DateTime($sumado);
                $d2 = new DateTime($today);

                 if ($d1 > $d2){
                    $excedido = 1; // aun puede cancelar orden
                 }else{
                    $excedido = 0; // ya no puede cancelar la orden
                 }

                // aun puede cancelar orden
                if($excedido == 1){

                    // evitar cancelar si ya dijo que estaba completada
                    if($orden->estado_5 == 1){
                        return ['success' => 2]; // ya no puede ser cancelado
                    }

                    // aun no ha sido cancelada
                    if($orden->estado_8 == 0){

                        $mensaje = $request->mensaje;
                        if(empty($mensaje) || $mensaje == null){
                            $mensaje = "";
                        }

                        $fechahoy = Carbon::now('America/El_Salvador');
                        Ordenes::where('id', $request->ordenid)->update(['estado_8' => 1, 'fecha_8' => $fechahoy,
                        'mensaje_8' => $mensaje, 'cancelado_propietario' => 1]);
    
                        // mandar notificacion al cliente
                        $usuario = User::where('id', $orden->users_id)->first();
                        $pilaUsuarios = $usuario->device_id;

                        $titulo = "Orden cancelada";
                        $mensaje = "El servicio cancelo su orden";
                        $alarma = 2;
                        $color = 3;
                        $icono = 3;
    
                        if(!empty($pilaUsuarios)){
                            $this->envioNoticacion($titulo, $mensaje, $pilaUsuarios, $alarma, $color, $icono);
                        }

                        return ['success' => 1]; // cancelado
                    }else{
                        return ['success' => 1]; // cancelado anteriormente
                    }
                }else{
                    return ['success' => 2]; // ya no puede ser cancelado
                }
            }else{
                return ['success' => 3];
            }
        }
    }

     // finaliza la orden en preparacion
     public function finalizarOrdenFinal(Request $request){
        if($request->isMethod('post')){ 
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

            if($o = Ordenes::where('id', $request->ordenid)->first()){
                $fechahoy = Carbon::now('America/El_Salvador');

                if($o->estado_5 == 0){            
                    Ordenes::where('id', $request->ordenid)->update(['visible_p2' => 0, 'visible_p3' => 0,
                    'estado_5' => 1, 'fecha_5' => $fechahoy]);
                }

                // buscar si esta orden aun no tiene motorista
                $motoasignado = DB::table('motorista_ordenes AS m')
                ->where('m.ordenes_id', $o->id)
                ->get();
                
                // mandar notificacion
                if(count($motoasignado) == 0){
                
                    $moto = DB::table('motoristas_asignados AS ms')
                    ->join('motoristas AS m', 'm.id', '=', 'ms.motoristas_id')                    
                    ->where('m.activo', 1)
                    ->where('m.disponible', 1)
                    ->where('ms.servicios_id', $o->servicios_id)
                    ->get();

                    $pilaUsuarios = array();
                    foreach($moto as $p){      
                        if(!empty($p->device_id)){
                            if($p->device_id != "0000"){
                                array_push($pilaUsuarios, $p->device_id); 
                            }                            
                        }
                    }

                    $administradores = DB::table('administradores')
                    ->where('activo', 1)
                    ->where('disponible', 1)
                    ->get();
                        
                    foreach($administradores as $p){
                        if(!empty($p->device_id)){
                            if($p->device_id != "0000"){
                                array_push($pilaUsuarios, $p->device_id);
                            }                            
                        }
                    }

                    // GUARDAR REGISTRO

                    $osp = new OrdenesPendiente;
                    $osp->ordenes_id = $request->ordenid; 
                    $osp->fecha = $fechahoy;
                    $osp->activo = 1;
                    $osp->tipo = 4;
                    $osp->save();
                    

                    $texto = "Orden #". $o->id;

                    $titulo1 = "MOTORISTA URGENTE";
                    $mensaje1 = $texto;
                    $alarma1 = 1;
                    $color1 = 3;
                    $icono1 = 2;

                    if(!empty($pilaUsuarios)){
                        $this->envioNoticacion($titulo1, $mensaje1, $pilaUsuarios, $alarma1, $color1, $icono1);
                    }    
                }

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }

    // ver historial 
    public function verHistorial(Request $request){
        if($request->isMethod('post')){ 
            $reglaDatos = array(
                'id' => 'required',
                'tipo' => 'required'               
            );

            $mensajeDatos = array(                                      
                'id.required' => 'El id propiestrio es requerido.',
                'tipo.required' => 'El tipo a buscar es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($p = Propietarios::where('id', $request->id)->first()){

                // buscar fecha hoy
                if($request->tipo == 0){
                    
                    $orden = DB::table('ordenes AS o')
                    ->select('o.id', 'o.precio_total', 'o.nota_orden', 
                    'o.fecha_orden', 'o.estado_8', 'o.tardio', 'o.estado_7')
                    ->where('o.servicios_id', $p->servicios_id)
                    ->whereDate('o.fecha_orden', '=', Carbon::today('America/El_Salvador')->toDateString())
                    ->orderBy('o.id', 'DESC')
                    ->get();
               
                    foreach($orden as $o){
                        $fechaOrden = $o->fecha_orden;
                        $hora = date("h:i A", strtotime($fechaOrden));
                        $fecha = date("d-m-Y", strtotime($fechaOrden));
                        $o->fecha_orden = $hora . " " . $fecha;    
                    }
                    
                    return ['success' => 1, 'histoorden' => $orden];
                }else{

                    // buscar con la fecha dada
                    $orden = DB::table('ordenes AS o')
                    ->select('o.id', 'o.precio_total', 'o.nota_orden', 
                    'o.fecha_orden', 'o.estado_8', 'o.tardio', 'o.estado_7')
                    ->where('o.servicios_id', $p->servicios_id)
                    ->whereDate('o.fecha_orden', '=', Carbon::parse($request->fecha)->toDateString())
                    ->orderBy('o.id', 'DESC')
                    ->get();
                               
                    foreach($orden as $o){
                        $fechaOrden = $o->fecha_orden;
                        $hora = date("h:i A", strtotime($fechaOrden));
                        $fecha = date("d-m-Y", strtotime($fechaOrden));
                        $o->fecha_orden = $hora . " " . $fecha;
    
                    }
                    
                    return ['success' => 1, 'histoorden' => $orden];
                }
            }
        }
    }

    // ORDENES COMPLETADAS POR EL SERVICIO
    public function verPagosCompletos(Request $request){
        if($request->isMethod('post')){ 
            $reglaDatos = array(
                'id' => 'required',
                'fecha1' => 'required',
                'fecha2' => 'required'               
            );

            $mensajeDatos = array(                                      
                'id.required' => 'El id propiestrio es requerido.',
                'fecha1.required' => 'Fecha1 es requerido.',
                'fecha2.required' => 'Fecha2 es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($p = Propietarios::where('id', $request->id)->first()){
              
                $date1 = Carbon::parse($request->fecha1)->format('Y-m-d');
                $date2 = Carbon::parse($request->fecha2)->addDays(1)->format('Y-m-d'); 
   
                $orden = DB::table('ordenes')
                ->select('id', 'servicios_id', 'nota_orden', 'fecha_orden', 'precio_total', 'estado_5',
                        'estado_8', 'tardio', 'fecha_tardio', 'fecha_4', 'hora_2', 'cancelado_cliente', 'cancelado_propietario')
                ->where('estado_5', 1) // orden completada
                ->where('servicios_id', $p->servicios_id)
                ->whereBetween('fecha_orden', array($date1, $date2))
                ->get();

                $dinero = 0;
                foreach($orden as $o){
                    $fechaOrden = $o->fecha_orden;
                    $hora = date("h:i A", strtotime($fechaOrden));
                    $fecha = date("d-m-Y", strtotime($fechaOrden));
                    $o->fecha_orden = $fecha;

                    $dinero = $dinero + $o->precio_total;

                    $time1 = Carbon::parse($o->fecha_4);
                    $horaEstimada = $time1->addMinute($o->hora_2 - 5)->format('h:i A');
                    $o->horaEstimada = $horaEstimada; 
                }

                $dineroTotal = number_format((float)$dinero, 2, '.', '');
                    
                return ['success' => 1, 'orden' => $orden, 'dinero' => $dineroTotal];                
            }else{
                return ['success' => 2];
            }
        }
    }

    // ORDENES CANCELADAS 
    public function verPagosCancelados(Request $request){
        if($request->isMethod('post')){ 
            $reglaDatos = array(
                'id' => 'required',
                'fecha1' => 'required',
                'fecha2' => 'required'               
            );

            $mensajeDatos = array(                                      
                'id.required' => 'El id propiestrio es requerido.',
                'fecha1.required' => 'Fecha1 es requerido.',
                'fecha2.required' => 'Fecha2 es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($p = Propietarios::where('id', $request->id)->first()){
              
                $date1 = Carbon::parse($request->fecha1)->format('Y-m-d');
                $date2 = Carbon::parse($request->fecha2)->addDays(1)->format('Y-m-d'); 
   
                $orden = DB::table('ordenes')
                ->select('id', 'servicios_id', 'nota_orden', 'fecha_orden', 'precio_total', 'estado_5',
                        'estado_8', 'tardio', 'fecha_tardio', 'fecha_4', 'hora_2', 'cancelado_cliente', 'cancelado_propietario')
                ->where('estado_8', 1) // orden cancelada
                ->where('servicios_id', $p->servicios_id)
                ->whereBetween('fecha_orden', array($date1, $date2))
                ->get();

                $dinero = 0;
                foreach($orden as $o){
                    $fechaOrden = $o->fecha_orden;
                    $hora = date("h:i A", strtotime($fechaOrden));
                    $fecha = date("d-m-Y", strtotime($fechaOrden));
                    $o->fecha_orden = $fecha;

                    $dinero = $dinero + $o->precio_total;
                }

                $dineroTotal = number_format((float)$dinero, 2, '.', '');
                    
                return ['success' => 1, 'orden' => $orden, 'dinero' => $dineroTotal];                
            }else{
                return ['success' => 2];
            }
        }
    }

    //ORDENES TARDIA
    public function verPagosTardio(Request $request){
        if($request->isMethod('post')){ 
            $reglaDatos = array(
                'id' => 'required',
                'fecha1' => 'required',
                'fecha2' => 'required'               
            );

            $mensajeDatos = array(                                      
                'id.required' => 'El id propiestrio es requerido.',
                'fecha1.required' => 'Fecha1 es requerido.',
                'fecha2.required' => 'Fecha2 es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($p = Propietarios::where('id', $request->id)->first()){
              
                $date1 = Carbon::parse($request->fecha1)->format('Y-m-d');
                $date2 = Carbon::parse($request->fecha2)->addDays(1)->format('Y-m-d'); 
   
                $orden = DB::table('ordenes')
                ->select('id', 'servicios_id', 'nota_orden', 'fecha_orden', 'precio_total', 'estado_5',
                        'estado_8', 'tardio', 'fecha_tardio', 'fecha_4', 'hora_2', 'cancelado_cliente', 'cancelado_propietario')
                ->where('tardio', 1) // orden tarde
                ->where('servicios_id', $p->servicios_id)
                ->whereBetween('fecha_orden', array($date1, $date2))
                ->get();

                $dinero = 0;
                foreach($orden as $o){
                    $fechaOrden = $o->fecha_orden;
                    $hora = date("h:i A", strtotime($fechaOrden));
                    $fecha = date("d-m-Y", strtotime($fechaOrden));
                    $o->fecha_orden = $hora . " " . $fecha;

                    $fechaOrden1 = $o->fecha_tardio;
                    $hora1 = date("h:i A", strtotime($fechaOrden1));
                    $fecha1 = date("d-m-Y", strtotime($fechaOrden1));
                    $o->fecha_tardio = $hora1 . " " . $fecha1;

                    $dinero = $dinero + $o->precio_total;

                    $time1 = Carbon::parse($o->fecha_4);
                    $horaEstimada = $time1->addMinute($o->hora_2)->format('h:i A d-m-Y');
                    $o->horaEstimada = $horaEstimada; 
                }

                $dineroTotal = number_format((float)$dinero, 2, '.', '');
                    
                return ['success' => 1, 'orden' => $orden, 'dinero' => $dineroTotal];                
            }else{
                return ['success' => 2];
            }
        }
    }


    public function ocultarPago(Request $request){
        if($request->isMethod('post')){ 
            $reglaDatos = array(
                'idpago' => 'required',                     
            );

            $mensajeDatos = array(                                      
                'idpago.required' => 'El id pago es requerido.',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if(PagoPropietario::where('id', $request->idpago)->first()){

                PagoPropietario::where('id', $request->idpago)->update(['visible' => 0]);
                return ['success' => 1];
            }
            else {
                return ['success' => 2];
            }
        }
    }

    public function envioNoticacion($titulo, $mensaje, $pilaUsuarios, $alarma, $color, $icono){
        OneSignal::sendNotificationToUser($titulo, $mensaje, $pilaUsuarios, $alarma, $color, $icono);
    }
}
