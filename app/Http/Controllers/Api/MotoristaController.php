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
use App\MotoristaOrdenes;
use App\Motoristas;
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
use App\Zonas;
use App\OrdenesDirecciones;

class MotoristaController extends Controller
{
     // login para propietario
     public function loginMotorista(Request $request){
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
         
            if($p = Motoristas::where('telefono', $request->phone)->first()){
 
                if($p->activo == 0){
                    return ['success' => 1]; // propietario no activo
                }

                if (Hash::check($request->password, $p->password)) {

                    $id = $p->id;   
                    if($request->device_id != null){
                        Motoristas::where('id', $p->id)->update(['device_id' => $request->device_id]);
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

            if($p = Motoristas::where('telefono', $request->telefono)->first()){

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
            if($p = Motoristas::where('telefono', $request->telefono)->first()){

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
                Motoristas::where('telefono', $request->telefono)->update(['codigo_correo' => $codigo]);
                
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
            if($p = Motoristas::where('telefono', $request->telefono)->where('codigo_correo', $request->codigo)->first()){
                
                return ['success' => 1]; // coincide, pasar a cambiar contraseña
            }else{
                return ['success' => 2]; // codigo incorrecto
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

            if($p = Motoristas::where('telefono', $request->telefono)->first()){

          

                Motoristas::where('telefono', $request->telefono)->update(['password' => Hash::make($request->password)]);
            
                return ['success' => 1];  // contraseña cambiada
            }else{
                return ['success' => 2];  // telefono no encontrado
            }
        }
    }

    // ver nuevas ordenes, cuando inicia la preparacion
    public function nuevaOrdenes(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
            );

            $messages = array(                                      
                'id.required' => 'El id motorista es requerido',
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }            

            if($m = Motoristas::where('id', $request->id)->first()){

                if($m->activo == 0){
                    return ['success' => 1];
                }

                $moto = DB::table('motoristas_asignados AS ms')
                ->join('motoristas AS m', 'm.id', '=', 'ms.motoristas_id')
                ->select('ms.servicios_id')
                ->where('motoristas_id', $m->id)
                ->get();

                $noquiero = DB::table('motorista_ordenes AS mo')                
                ->get();

                $pilaOrden = array();
                foreach($noquiero as $p){
                    array_push($pilaOrden, $p->ordenes_id);
                }

                $pilaUsuarios = array();
                foreach($moto as $p){
                    array_push($pilaUsuarios, $p->servicios_id);
                }
               
                $orden = DB::table('ordenes AS o')
                ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
                ->select('o.id', 'o.servicios_id', 's.nombre', 'o.estado_4', 
                'o.estado_8', 'o.precio_total', 'o.fecha_4', 'o.hora_2', 'o.estado_6')
                ->where('o.estado_6', 0) // nadie a seteado este
                ->where('o.estado_4', 1) // inicia la orden
                ->where('o.estado_8', 0) // orden no cancelada
                ->whereIn('o.servicios_id', $pilaUsuarios)
                ->whereNotIn('o.id', $pilaOrden)
                ->get();

                foreach($orden as $o){

                    $servicio = DB::table('zonas AS z')
                    ->join('zonas_servicios AS zs', 'zs.zonas_id', '=', 'z.id')
                    ->select('z.nombre')                    
                    ->where('zs.servicios_id', $o->servicios_id)
                    ->first();

                    $nombre = $servicio->nombre;
                    $o->zona = $nombre;
                                                
                    // HORARIO DE ENTREGA, YA SUMADO LOS 5 MINUTOS PARA CLIENTE

                    $time1 = Carbon::parse($o->fecha_4);
                    $horaEstimada = $time1->addMinute($o->hora_2)->format('h:i A d-m-Y');
                    $o->horaEntrega = $horaEstimada;
                }
                
                return ['success' => 2, 'ordenes' => $orden]; 
            }else{
                return ['success' => 3];
            }
        }
    }

    // ver orden por id
    public function verOrdenPorID(Request $request){
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

            if($or = Ordenes::where('id', $request->ordenid)->first()){

                //sacar direccion de la orden

                $orden = DB::table('ordenes_direcciones AS o')
                ->select('o.nombre', 'o.direccion',
                        'o.numero_casa', 'o.punto_referencia', 'o.telefono',
                        'o.latitud', 'o.longitud')
                ->where('o.ordenes_id', $request->ordenid)
                ->first();

                $servicioid = $or->servicios_id;

                $servicio = DB::table('servicios AS s')
                ->select('s.nombre', 's.telefono', 's.direccion', 's.latitud', 's.longitud', 's.producto_visible')
                ->where('s.id', $servicioid)
                ->first();

                $time1 = Carbon::parse($or->fecha_4);
                $restaHoraEstimada = $or->hora_2 - 5; // hora estimada recogida de producto
                $horaEstimada = $time1->addMinute($restaHoraEstimada)->format('h:i A d-m-Y');
                $horaEstimada = $horaEstimada;              
                
                return ['success' => 1, 'ordenes' => $orden, 'servicio' => $servicio, 'hora' => $horaEstimada];
            }else{
                return ['success' => 2];
            }
        }
    }

    // ver productos de la orden
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

            $datos = Servicios::where('id', $or->servicios_id)->first();

            if($datos->producto_visible == 0){
                return ['success' => 1];
            }

            $producto = DB::table('ordenes AS o')
                        ->join('ordenes_descripcion AS od', 'od.ordenes_id', '=', 'o.id')
                        ->join('producto AS p', 'p.id', '=', 'od.producto_id')
                        ->select('od.id AS productoID', 'p.nombre', 'p.imagen', 'p.utiliza_imagen', 'od.precio', 'od.cantidad')
                        ->where('o.id', $request->ordenid)
                        ->get();
            
                        foreach($producto as $p){
                            $cantidad = $p->cantidad;
                            $precio = $p->precio;
                            $multi = $cantidad * $precio;
                            $p->multiplicado = number_format((float)$multi, 2, '.', '');
                        }

            return ['success' => 2, 'productos' => $producto];                  
        }else{
            return ['success' => 3];
        }
    }

    // saver coordenadas del servicio 
    public function coordenadas(Request $request){
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

            $datos = Servicios::where('id', $or->servicios_id)->first();
            
            return ['success' => 1, 'latitud' => $datos->latitud, 'longitud' => $datos->longitud, 'nombre' => $datos->nombre];                  
        }else{
            return ['success' => 2];
        }
    }

    // saver coordenadas del cliente 
    public function coordenadascliente(Request $request){
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

            $datos = OrdenesDirecciones::where('ordenes_id', $or->id)->first();
            
            return ['success' => 1, 'latitud' => $datos->latitud, 'longitud' => $datos->longitud, 'nombre' => $datos->nombre];                  
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
                    ->select('p.imagen', 'p.nombre', 'p.descripcion', 'o.precio', 'o.cantidad', 'o.nota')
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

    // el motorista recoge la orden 
    public function obtenerOrden(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenid' => 'required',
                'id' => 'required'         
            ); 
        
            $mensajeDatos = array(
                'ordenid.required' => 'El id de la orden es requerido',
                'id.required' => 'El id del motorista es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0,
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($mo = Motoristas::where('id', $request->id)->first()){
                if($or = Ordenes::where('id', $request->ordenid)->first()){

                    // verificar si motorista puede seguir agarrando ordenes

                    $idservicio = $or->servicios_id;
                    $datosprivado = Servicios::where('id', $idservicio)->first();

                    // VER ORDENES PENDIENTE SIN ENTREGAR EL MOTORISTA


                     // sacar todos los id de ordenes revisadas
                    $revisada = DB::table('ordenes_revisadas')
                    ->get();

                    $pilaOrdenid = array();
                    foreach($revisada as $p){
                        array_push($pilaOrdenid, $p->ordenes_id);
                    }
                        
                    $ordenid = DB::table('motorista_ordenes AS mo')
                    ->join('ordenes AS o', 'o.id', '=', 'mo.ordenes_id')
                    ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')
                    ->select('mo.motoristas_id', 'mo.ordenes_id', 'mo.fecha_agarrada',
                    'o.estado_5', 'm.identificador', 'o.precio_total', 'o.precio_envio', 
                    'mo.fecha_agarrada', )
                    ->where('mo.motoristas_id', $mo->id)
                    ->where('o.estado_5', 1) // orden preparada                   
                    ->whereNotIn('mo.ordenes_id', $pilaOrdenid)
                    ->get(); 

                    
                    $sum = 0.0;
                    foreach($ordenid as $o){
                      
                        // sumar precio
                        $precio = $o->precio_total + $o->precio_envio;
                        $o->total = number_format((float)$precio, 2, '.', '');
                        $sum = $sum + $precio;
                    }   

                    $suma = number_format((float)$sum, 2, '.', '');


                    // LIMITAR ORDEN POR TOTAL DE DINERO

                    if($mo->privado == 0){
                        if($suma >= $mo->limite_dinero){
                            return ['success' => 1];
                        }                        
                    }

                    // esta libre aun, pero esto viene alguien inicio la entrega
                    if($or->estado_6 == 0){

                        if($or->estado_8 == 1){
                            return ['success' => 2];
                        }

                        DB::beginTransaction();
                        try {
                            // evitar orden con motorista ya asignado
                            if(MotoristaOrdenes::where('ordenes_id', $request->ordenid)->first()){
                                return ['success' => 3];    
                            } 

                            // ACTUALIZAR 
                            Ordenes::where('id', $request->ordenid)->update(['visible_m' => 1]);

                            $fecha = Carbon::now('America/El_Salvador');

                           
                            
                            $nueva = new MotoristaOrdenes;
                            $nueva->ordenes_id = $or->id;
                            $nueva->motoristas_id = $request->id;
                            $nueva->fecha_agarrada = $fecha;
                            

                            $nueva->save();

                            DB::commit();
                            
                            return ['success' => 4]; // guardado
                            
                        } catch(\Throwable $e){
                            DB::rollback();
                                return [
                                    'success' => 5 . $e // error
                                ];
                        }
                    }else{
                        return ['success' => 6]; // orden ya agarrada por otro motorista
                    }
                }else{
                    return ['success' => 5]; // orden no encontrada
                }
            }else{
                return ['success' => 5]; // motorista no encontrado
            }
        }
    }

    // ordenes en proceso
    public function verProcesoOrdenes(Request $request){
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

            if(Motoristas::where('id', $request->id)->first()){
                
                // mostrar si fue cancelada para despues setear visible_m

                $orden = DB::table('motorista_ordenes AS mo')
                ->join('ordenes AS o', 'o.id', '=', 'mo.ordenes_id')
                ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
                ->select('o.id', 'o.precio_total', 'o.fecha_4', 'o.hora_2', 
                'o.estado_5', 'o.estado_6', 'o.precio_envio', 's.nombre', 's.id AS servicioid', 'o.estado_8', 'o.visible_m')
                ->where('o.estado_7', 0) // aun sin entregar al cliente
                ->where('o.visible_m', 1) // para ver si una orden fue cancelada a los 10 minutos, y el motorista la agarro, asi ver el estado
                ->where('o.estado_6', 0) // aun no han salido a entregarse
                ->where('mo.motoristas_id', $request->id)
                ->get();

                // sumar mas envio
                foreach($orden as $p){
                    $total = $p->precio_total + $p->precio_envio;
                    $p->precio_total = number_format((float)$total, 2, '.', '');

                    $zona = DB::table('zonas AS z')
                    ->join('zonas_servicios AS zs', 'zs.zonas_id', '=', 'z.id')
                    ->select('z.nombre AS nombreZona')
                    ->where('zs.servicios_id', $p->servicioid)
                    ->first();
                    

                    $nombre = $zona->nombreZona;
                    $p->zona = $nombre;
                    
                    $fechaOrden = Carbon::parse($p->fecha_4);
                    $restaHoraEstimada = $p->hora_2 - 5;

                    $horaEstimadaEntrega = $fechaOrden->addMinute($restaHoraEstimada)->format('h:i A d-m-Y');
                    $p->fecharecoger = $horaEstimadaEntrega;
                }
                
                return ['success' => 1, 'ordenes' => $orden];
            }else{
                return ['success' => 2];
            }
        }
    }

    public function verProcesoOrdenesEntrega(Request $request){
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

            if(Motoristas::where('id', $request->id)->first()){
                
                // mostrar si fue cancelada para despues setear visible_m

                $orden = DB::table('motorista_ordenes AS mo')
                ->join('ordenes AS o', 'o.id', '=', 'mo.ordenes_id')
                ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
                ->select('o.id', 'o.precio_total', 'o.fecha_4', 'o.hora_2', 
                'o.estado_5', 'o.estado_6', 'o.precio_envio', 's.nombre', 's.id AS servicioid', 'o.estado_8', 'o.visible_m')
                ->where('o.estado_7', 0) // aun sin entregar al cliente
                ->where('o.visible_m', 1) // para ver si una orden fue cancelada a los 10 minutos, y el motorista la agarro, asi ver el estado
                ->where('o.estado_6', 1) // van a entregarse
                ->where('mo.motoristas_id', $request->id)
                ->get();

                // sumar mas envio
                foreach($orden as $p){
                    $total = $p->precio_total + $p->precio_envio;
                    $p->precio_total = number_format((float)$total, 2, '.', '');

                    $zona = DB::table('zonas AS z')
                    ->join('zonas_servicios AS zs', 'zs.zonas_id', '=', 'z.id')
                    ->select('z.nombre AS nombreZona')
                    ->where('zs.servicios_id', $p->servicioid)
                    ->first();
                    

                    $nombre = $zona->nombreZona;
                    $p->zona = $nombre;
                    
                    $fechaOrden = Carbon::parse($p->fecha_4);
                    $restaHoraEstimada = $p->hora_2; // HORA DE ENTREGA AL CLIENTE

                    $horaEstimadaEntrega = $fechaOrden->addMinute($restaHoraEstimada)->format('h:i A');
                    $p->fecharecoger = $horaEstimadaEntrega;
                }
                
                return ['success' => 1, 'ordenes' => $orden];
            }else{
                return ['success' => 2];
            }
        }
    }

     // ver orden proceso por id
     public function verOrdenProcesoPorID(Request $request){
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

            if($or = Ordenes::where('id', $request->ordenid)->first()){

                //sacar direccion de la orden

                $orden = DB::table('ordenes_direcciones AS o')
                ->select('o.nombre', 'o.direccion',
                        'o.numero_casa', 'o.punto_referencia', 'o.telefono',
                        'o.latitud', 'o.longitud')
                ->where('o.ordenes_id', $request->ordenid)
                ->first();

                $servicioid = $or->servicios_id;

                $servicio = DB::table('servicios AS s')
                ->select('s.nombre', 's.telefono', 's.direccion', 's.latitud', 's.longitud', 's.producto_visible')
                ->where('s.id', $servicioid)
                ->first();

                $time1 = Carbon::parse($or->fecha_4);
                $restaHoraEstimada = $or->hora_2 - 5; // hora estimada recogida de producto
                $horaEstimada = $time1->addMinute($restaHoraEstimada)->format('h:i A');
                $horaEstimada = $horaEstimada;              
                
                return ['success' => 1, 'ordenes' => $orden,
                 'servicio' => $servicio, 'hora' => $horaEstimada, 
                 'estado' => $or->estado_6, 'cancelado' => $or->estado_8];
            }else{
                return ['success' => 2];
            }
        }
    }

    // iniciar entrega de la orden 
    public function iniciarEntrega(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenid' => 'required'               
            );
        
            $mensajeDatos = array(                                       
                'ordenid.required' => 'El id de la orden es requerido'
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

                if($or->estado_8 == 1){
                    return ['success' => 3]; 
                }

                // orden ya fue preparada por el propietario
                if($or->estado_5 == 1 && $or->estado_6 == 0){
 
                    $fecha = Carbon::now('America/El_Salvador');
                    Ordenes::where('id', $request->ordenid)->update(['estado_6' => 1,
                    'fecha_6' => $fecha]);
                    
                    // notificacion al cliente
                    $usuario = User::where('id', $or->users_id)->first();
                    $device = $usuario->device_id;

                    $titulo = "Orden #". $or->id ." Preparada";
                    $mensaje = "El motorista va encamino";
                    $alarma = 2;
                    $color = 3;
                    $icono = 2;  
                    $tipo = 2;

                    // CLIENTE SIEMPRE TENDRA DEVICE_ID
                    if(!empty($device)){
                        $this->envioNoticacion($titulo, $mensaje, $device, $alarma, $color, $icono, $tipo); 
                    }

                    return ['success' => 1]; //orden va en camino
                }else{
                    return ['success' => 2]; // la orden aun no ha sido preparada
                }
            }else{
                return ['success' => 4];
            }
        } 
    }

    // finalizar entrega 
    public function finalizarEntrega(Request $request){
        
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenid' => 'required'               
            );
        
            $mensajeDatos = array(                                      
                'ordenid.required' => 'El id de la orden es requerido'
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

                if($or->estado_7 == 0){ 

                    $fecha = Carbon::now('America/El_Salvador');
                    Ordenes::where('id', $request->ordenid)->update(['estado_7' => 1,
                    'fecha_7' => $fecha, 'visible_m' => 0]);    
                    
                    // notificacion al cliente
                    $usuario = User::where('id', $or->users_id)->first();
                    $device = $usuario->device_id;

                    $titulo = "Orden Completada";
                    $mensaje = "Muchas gracias por su compra";
                    $alarma = 2; 
                    $color = 2;
                    $icono = 4;
                    $tipo = 2; //cliente 

                    if(!empty($device)){
                        $this->envioNoticacion($titulo, $mensaje, $device, $alarma, $color, $icono, $tipo); 
                    } 

                    return ['success' => 1]; // orden completada
                }else{ 
                    return ['success' => 2]; // ya habia seteado el campo
                }
            }else{
                return ['success' => 3];
            }
        }
    }

    // borrar orden, cuando fue cancelada extraordinariamente, y el motorista la acepto antes de los 10 minutos
    public function borrarOrdenCancelada(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenid' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'ordenid.required' => 'El id de la orden es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0,
                    'message' => $validarDatos->errors()->all()
                ];
            }

            // ocultar visibilidad
            if(Ordenes::where('id', $request->ordenid)->first()){
                Ordenes::where('id', $request->ordenid)->update(['visible_m' => 0]);

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }

    // informacion de la cuenta
    public function informacionCuenta(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
            );

            $messages = array(                                      
                'id.required' => 'El id motorista es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) 
                {
                    return [
                        'success' => 0, 
                        'message' => $validator->errors()->all()
                    ];
                }

            if($p = Motoristas::where('id', $request->id)->first()){

                $nombre = $p->nombre;
                $correo = $p->correo;

                return ['success'=> 1, 'nombre' => $nombre,
                'correo'=> $correo];
            }else{
                return ['success'=> 2];
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
                'id.required' => 'El id motorista es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) 
                {
                    return [
                        'success' => 0, 
                        'message' => $validator->errors()->all()
                    ];
                }

            if($p = Motoristas::where('id', $request->id)->first()){

               

                $pro = DB::table('motoristas AS m')
                ->select('m.disponible')
                ->where('m.id', $request->id)
                ->first();

                $disponibilidad = $pro->disponible;
                return ['success'=> 1, 'disponibilidad' => $disponibilidad]; //1: esta disponible

            }else{
                return ['success'=> 2];
            }
        }
    }

     // cambiar el correo al motorista
     public function cambiarCorreo(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
                'correo' => 'required'
            );

            $messages = array(                                      
                'id.required' => 'El id motorista es requerido',
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

            if($p = Motoristas::where('id', $request->id)->first()){

                // verificar si existe el correo
                if(Motoristas::where('correo', $request->correo)->where('id', '!=', $request->id)->first()){                
                    return [
                        'success' => 1              
                    ];
                }

                // actualizar correo
                Motoristas::where('id', $request->id)->update(['correo' => $request->correo]);
                
                return ['success'=> 2];
            }else{
                return ['success'=> 3];
            }
        }
    }

     // disponibilidad del servicio
     public function modificarDisponibilidad(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
                'valor1' => 'required'               
            );

            $messages = array(                                      
                'id.required' => 'El id motorista es requerido',
                'valor1.required' => 'El estado 1 es requerido'
            );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

           

            if(Motoristas::where('id', $request->id)->first()){

              
                
                Motoristas::where('id', $request->id)->update(['disponible' => $request->valor1]);
               
                return ['success'=> 1];                
            }else{
                return ['success'=> 2]; // motorista no encontrado
            }
        }
    }

     // cambia la contraseña el motorista
     public function actualizarPassword(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
                'password' => 'required'
            );

            $messages = array(                                      
                'id.required' => 'El id motorista es requerido',
                'password.required' => 'El password es requerida'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Motoristas::where('id', $request->id)->first()){
                
                Motoristas::where('id', $request->id)->update(['password' => Hash::make($request->password)]);
                                
                return ['success'=> 1];
            }else{
                return ['success'=> 2];
            }
        }
    }

    // ver historial
    public function verHistorial(Request $request){
        if($request->isMethod('post')){ 
            $reglaDatos = array(
                'id' => 'required', 
                'fecha1' => 'required',
                'fecha2' => 'required'
            );

            $mensajeDatos = array(                                      
                'id.required' => 'El id motorista es requerido.',
                'fecha1.required' => 'La fecha1 es requerido.',
                'fecha2.required' => 'La fecha2 es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }
 
            if($p = Motoristas::where('id', $request->id)->first()){

                $start = Carbon::parse($request->fecha1)->startOfDay(); 
                $end = Carbon::parse($request->fecha2)->endOfDay();
                
                $orden = DB::table('motorista_ordenes AS m')
                ->join('ordenes AS o', 'o.id', '=', 'm.ordenes_id')
                ->select('o.id', 'o.precio_total', 'o.fecha_orden', 
                'm.motoristas_id', 'o.ganancia_motorista', 'o.estado_7', 'o.servicios_id', 'o.envio_gratis')
                ->where('o.estado_7', 1) // solo completadas
                ->where('m.motoristas_id', $request->id) // del motorista
                ->whereBetween('o.fecha_orden', [$start, $end]) 
                ->orderBy('o.id', 'DESC')
                ->get();

                foreach($orden as $o){
                    $fechaOrden = $o->fecha_orden;
                    $hora = date("h:i A", strtotime($fechaOrden));
                    $fecha = date("d-m-Y", strtotime($fechaOrden));
                    $o->fecha_orden = $hora . " " . $fecha;
                    
                    // nombre servicio
                    $nombreservicio = Servicios::where('id', $o->servicios_id)->pluck('nombre')->first();
                    $o->servicio = $nombreservicio;

                    // sacar direccion guardada de la orden
                    $pack = OrdenesDirecciones::where('ordenes_id', $o->id)->first();
                    $o->direccion = $pack->direccion;

                    // sacar zona de envio
                    $zona = Zonas::where('id', $pack->zonas_id)->pluck('descripcion')->first();
                    $o->zona = $zona;
                }

                // sumar ganancia de esta fecha
                $suma = collect($orden)->sum('ganancia_motorista');
                $ganado = number_format((float)$suma, 2, '.', '');
                return ['success' => 1, 'histoorden' => $orden, 'ganado' => $ganado];
            }else{
                return ['success' => 2];
            }
        }
    }

    // ver zonas de pago
    public function verZonaPago(Request $request){
        if($request->isMethod('post')){ 
            $reglaDatos = array(
                'id' => 'required'
            );

            $mensajeDatos = array(                                      
                'id.required' => 'El id motorista es requerido.'
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

                // si puede ver las zonas de pago
                if($m->zona_pago == 1){
                    
                    $zona = DB::table('revisador')
                    ->where('activo', 1)
                    ->where('disponible', 1)
                    ->get();

                    return ['success' => 1, 'zonas' => $zona];
                }else{
                    return ['success' => 2];
                }
            }else{
                return ['success' => 3];
            }
        }
    }
 
       // ordene pediente de pago
       public function pendientePago(Request $request){
        if($request->isMethod('post')){ 
            $reglaDatos = array(
                'id' => 'required'
            );

            $mensajeDatos = array(                                      
                'id.required' => 'El id motorista es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if(Motoristas::where('id', $request->id)->first()){

                // estas ordenes ya fueron revisadas
                $noquiero = DB::table('ordenes_revisadas AS r')                
                ->get();
 
                $pilaOrden = array();
                foreach($noquiero as $p){
                    array_push($pilaOrden, $p->ordenes_id);
                }
 
                $orden = DB::table('motorista_ordenes AS mo')
                ->join('ordenes AS o', 'o.id', '=', 'mo.ordenes_id')
                ->select('o.id', 'mo.motoristas_id', 'o.precio_total', 'mo.fecha_agarrada', 
                'o.servicios_id', 'o.estado_8')
                ->where('mo.motoristas_id', $request->id)
               
                ->where('o.estado_8', 0)
                ->whereNotIn('o.id', $pilaOrden)
                ->get();

                foreach($orden as $o){
                    $fechaOrden = $o->fecha_agarrada;
                    $hora = date("h:i A", strtotime($fechaOrden));
                    $fecha = date("d-m-Y", strtotime($fechaOrden));
                    $o->fecha_orden = $hora . " " . $fecha;
                    
                    // nombre servicio
                    $nombreservicio = Servicios::where('id', $o->servicios_id)->pluck('nombre')->first();
                    $o->servicio = $nombreservicio;

                    // sacar direccion guardada de la orden
                    $pack = OrdenesDirecciones::where('ordenes_id', $o->id)->first();
                    $o->direccion = $pack->direccion;

                    // sacar zona de envio
                    $zona = Zonas::where('id', $pack->zonas_id)->pluck('nombre')->first();
                    $o->zona = $zona;
                }

                // sumar ganancia de esta fecha
                $suma = collect($orden)->sum('precio_total');
                $debe = number_format((float)$suma, 2, '.', '');
                return ['success' => 1, 'orden' => $orden, 'debe' => $debe];
            }
        }
    } 


    public function envioNoticacion($titulo, $mensaje, $pilaUsuarios, $alarma, $color, $icono, $tipo){
        OneSignal::sendNotificationToUser($titulo, $mensaje, $pilaUsuarios, $alarma, $color, $icono, $tipo);
    }
} 
