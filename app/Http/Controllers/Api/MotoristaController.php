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
use App\OrdenesCupones;
use App\Cupones;
use App\AplicaCuponCuatro;
use App\AplicaCuponTres;
use App\AplicaCuponDos;
use App\AplicaCuponCinco;
use App\MotoristaOrdenEncargo;
use App\OrdenesEncargoDireccion;
use App\OrdenesEncargoProducto;
use App\OrdenesEncargo;
use App\EncargoAsignadoServicio;
use App\Encargos;

class MotoristaController extends Controller
{
     // login para motorista
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
                    return ['success' => 1]; // motorista no activo
                }

                if (Hash::check($request->password, $p->password)) {

                    $id = $p->id;   
                    if($request->device_id != null){
                        Motoristas::where('id', $p->id)->update(['device_id' => $request->device_id]);
                    }

                    // disponible
                    Motoristas::where('id', $p->id)->update(['disponible' => 1]);

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
            if($p = Motoristas::where('telefono', $request->telefono)
            ->where('codigo_correo', $request->codigo)->first()){
                
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

                $noquiero = DB::table('motorista_ordenes AS mo')->get();

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
                'o.estado_8', 'o.precio_total', 'o.precio_envio', 'o.fecha_4', 
                'o.hora_2', 'o.estado_6', 'o.pago_a_propi')
                ->where('o.estado_6', 0) // nadie a seteado este
                ->where('o.estado_4', 1) // inicia la orden
                ->where('o.estado_8', 0) // orden no cancelada
                ->whereIn('o.servicios_id', $pilaUsuarios)
                ->whereNotIn('o.id', $pilaOrden)
                ->get();

                foreach($orden as $o){
                    
                    $o->direccion = OrdenesDirecciones::where('ordenes_id', $o->id)->pluck('direccion')->first();

                    $pagarPropi = "";  // para decile si paga a propietario o no                 
                    $cupon = ""; // texto para decir que tipo de cupon aplico
                    
                    if($o->pago_a_propi == 1){
                        // pagar a propietario
                        $pagarPropi = "Pagar a Propietario $" . $o->precio_total; // sub total
                    }

                    $o->tipo = $pagarPropi;

                    $time1 = Carbon::parse($o->fecha_4);
                    $horaEstimada = $time1->addMinute($o->hora_2)->format('h:i A d-m-Y');
                    $o->horaEntrega = $horaEstimada;

                     // buscar si aplico cupon
                     if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()){
                       
                        // buscar tipo de cupon
                        $tipo = Cupones::where('id', $oc->cupones_id)->first();

                        // ver que tipo se aplico
                        // el precio envio ya esta modificado
                        if($tipo->tipo_cupon_id == 1){
                            $cupon = "Envío Gratis";
                            
                            // no sumara precio envio, ya que esta seteado a $0.00 por cupon envio gratis 
                            // cobrar a cliente                          
                            $o->precio_total = number_format((float)$o->precio_total, 2, '.', '');

                        }else if($tipo->tipo_cupon_id == 2){
                           
                            // modificar precio
                            $dd = AplicaCuponDos::where('ordenes_id', $o->id)->first();

                            $total = $o->precio_total - $dd->dinero;
                            if($total <= 0){
                                $total = 0;
                            }

                            // si aplico envio gratis
                            if($dd->aplico_envio_gratis == 1){
                                $cupon = "Descuento dinero: $" . $dd->dinero . " + Envío Gratis";
                                
                            }else{
                                $cupon = "Descuento dinero: $" . $dd->dinero;
                            }

                            //** NO importa sumar el envio, ya que si aplico envio gratis, el precio_envio sera $0.00 */
                            // sumar el precio de envio
                            $suma = $total + $o->precio_envio; 
 
                            // precio modificado con el descuento dinero
                            $o->precio_total = number_format((float)$suma, 2, '.', '');

                        }else if($tipo->tipo_cupon_id == 3){                          

                            $porcentaje = AplicaCuponTres::where('ordenes_id', $o->id)->pluck('porcentaje')->first();
                            $resta = $o->precio_total * ($porcentaje / 100);
                            $total = $o->precio_total - $resta;

                            if($total <= 0){
                                $total = 0;
                            }

                            $cupon = "Descuento de: " . $porcentaje . "%";

                            // sumar el precio de envio
                            $suma = $total + $o->precio_envio;

                            $o->precio_total = number_format((float)$suma, 2, '.', '');

                        }else if($tipo->tipo_cupon_id == 4){
                          
                            $producto = AplicaCuponCuatro::where('ordenes_id', $o->id)->pluck('producto')->first();

                            $sumado = $o->precio_total + $o->precio_envio;
                            $sumado = number_format((float)$sumado, 2, '.', '');

                            $cupon = "Producto Gratis: " . $producto;
    
                            $o->precio_total = $sumado;
                        }
                        else if($tipo->tipo_cupon_id == 5){ // donacion
                           
                            $donacion = AplicaCuponCinco::where('ordenes_id', $o->id)->pluck('dinero')->first();

                            $cupon = "Donación de: $" . $donacion;

                            // sumar
                            $suma = $o->precio_total + $o->precio_envio + $donacion;

                            $o->precio_total = number_format((float)$suma, 2, '.', '');
                        }else{
                            $total = $o->precio_total + $o->precio_envio;
                            $o->precio_total = number_format((float)$total, 2, '.', '');     
                        }

                    }else{                                            
                        $total = $o->precio_total + $o->precio_envio;
                        $o->precio_total = number_format((float)$total, 2, '.', '');                        
                    }

                    $o->cupon = $cupon;


                } //end foreach


                 // actualizar id, cada vez
             
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
                        'o.numero_casa', 'o.punto_referencia',
                        'o.latitud', 'o.longitud')
                ->where('o.ordenes_id', $request->ordenid)
                ->first();

                $servicioid = $or->servicios_id;

                $servicio = DB::table('servicios AS s')
                ->select('s.nombre', 's.telefono', 's.direccion', 's.latitud', 's.longitud', 's.producto_visible')
                ->where('s.id', $servicioid)
                ->first();

                $time1 = Carbon::parse($or->fecha_4);
                
                $horaEstimada = $time1->addMinute($or->hora_2)->format('h:i A d-m-Y');
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
                        ->select('od.id AS productoID', 'p.nombre', 'od.nota', 'p.imagen', 'p.utiliza_imagen', 'od.precio', 'od.cantidad')
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

                    // VERIFICARA LIMITE DE DINERO SOLO SI ES MOTORISTA PUBLICO
                    if($mo->privado == 0){

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
                        'mo.fecha_agarrada')
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

                        

                        // LIMITAR ORDEN POR TOTAL DE DINERO
                        // UNICAMENTE SERVICIOS
                                             

                        if($sum >= $mo->limite_dinero){
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
                'o.estado_5', 'o.estado_6', 'o.precio_envio', 's.nombre', 
                's.id AS servicioid', 'o.estado_8', 'o.visible_m', 'o.pago_a_propi')
                ->where('o.estado_7', 0) // aun sin entregar al cliente
                ->where('o.visible_m', 1) // para ver si una orden fue cancelada a los 10 minutos, y el motorista la agarro, asi ver el estado
                ->where('o.estado_6', 0) // aun no han salido a entregarse
                ->where('mo.motoristas_id', $request->id)
                ->get();

                // sumar mas envio
                foreach($orden as $o){

                    $fechaOrden = Carbon::parse($o->fecha_4);
                    $horaEstimadaEntrega = $fechaOrden->addMinute($o->hora_2)->format('h:i A d-m-Y');
                    $o->fecharecoger = $horaEstimadaEntrega;

                    $pagarPropi = "";  // para decile si paga a propietario o no                 
                    $cupon = ""; // texto para decir que tipo de cupon aplico

                    if($o->pago_a_propi == 1){
                        // pagar a propietario
                        $pagarPropi = "Pagar a Propietario $" . $o->precio_total; // sub total
                    }

                    $o->tipo = $pagarPropi;


                     // buscar si aplico cupon
                     if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()){
                        $o->aplicacupon = 1;
                        // buscar tipo de cupon
                        $tipo = Cupones::where('id', $oc->cupones_id)->first();

                        // ver que tipo se aplico
                        // el precio envio ya esta modificado
                        if($tipo->tipo_cupon_id == 1){
                           
                            $cupon = "Envío Gratis";
                            // no sumara precio envio, ya que esta seteado a $0.00 por cupon envio gratis                           
                            $o->precio_total = number_format((float)$o->precio_total, 2, '.', '');

                        }else if($tipo->tipo_cupon_id == 2){
                           
                            $dd = AplicaCuponDos::where('ordenes_id', $o->id)->first();

                            $total = $o->precio_total - $dd->dinero;
                            if($total <= 0){
                                $total = 0;
                            }

                            // si aplico envio gratis
                            if($dd->aplico_envio_gratis == 1){
                                $cupon = "Descuento dinero: $" . $dd->dinero . " + Envío Gratis";
                            }else{
                                $cupon = "Descuento dinero: $" . $dd->dinero;
                            }

                            //** NO importa sumar el envio, ya que si aplico envio gratis, el precio_envio sera $0.00 */

                            // sumar el precio de envio
                            $suma = $total + $o->precio_envio;

                            // precio modificado con el descuento dinero
                            $o->precio_total = number_format((float)$suma, 2, '.', '');

                        }else if($tipo->tipo_cupon_id == 3){
                            
                            $porcentaje = AplicaCuponTres::where('ordenes_id', $o->id)->pluck('porcentaje')->first();
                            $resta = $o->precio_total * ($porcentaje / 100);
                            $total = $o->precio_total - $resta;

                            if($total <= 0){
                                $total = 0;
                            }

                            $cupon = "Descuento de: " . $porcentaje . "%";

                            // sumar el precio de envio
                            $suma = $total + $o->precio_envio;

                            $o->precio_total = number_format((float)$suma, 2, '.', '');

                        }else if($tipo->tipo_cupon_id == 4){
                           
                            $producto = AplicaCuponCuatro::where('ordenes_id', $o->id)->pluck('producto')->first();

                            $sumado = $o->precio_total + $o->precio_envio;
                            $sumado = number_format((float)$sumado, 2, '.', '');

                            $cupon = "Producto Gratis: " . $producto;
    
                            $o->precio_total = $sumado;                          
                        }
                        else if($tipo->tipo_cupon_id == 5){
                            
                            $donacion = AplicaCuponCinco::where('ordenes_id', $o->id)->pluck('dinero')->first();

                            $cupon = "Donación de: $" . $donacion;

                            // sumar
                            $suma = $o->precio_total + $o->precio_envio + $donacion;

                            $o->precio_total = number_format((float)$suma, 2, '.', '');
                        }else{
                            $total = $o->precio_total + $o->precio_envio;                       
                            $o->precio_total = number_format((float)$total, 2, '.', '');
                        }
                    }else{
                        
                        $total = $o->precio_total + $o->precio_envio;                       
                        $o->precio_total = number_format((float)$total, 2, '.', '');
                    }

                    $o->cupon = $cupon;
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
                'o.estado_5', 'o.estado_6', 'o.precio_envio', 's.nombre', 
                's.id AS servicioid', 'o.estado_8', 'o.visible_m', 'o.pago_a_propi')
                ->where('o.estado_7', 0) // aun sin entregar al cliente
                ->where('o.visible_m', 1) // para ver si una orden fue cancelada a los 10 minutos, y el motorista la agarro, asi ver el estado
                ->where('o.estado_6', 1) // van a entregarse
                ->where('mo.motoristas_id', $request->id)
                ->get();

               
                // sumar mas envio
                foreach($orden as $o){

                    // Tiempo dado por propietario + tiempo de zona extra
                    $tiempo = OrdenesDirecciones::where('ordenes_id', $o->id)->first();
                   
                    $tiempoorden = $tiempo->copia_tiempo_orden + $o->hora_2;
                    $fechaOrden = Carbon::parse($o->fecha_4);
                    $horaEstimadaEntrega = $fechaOrden->addMinute($tiempoorden)->format('h:i A');                   
                    $o->fecharecoger = $horaEstimadaEntrega;

                    $cupon = "";
                    $tipo = "";

                    if($o->pago_a_propi == 1){
                        $tipo = "Pagar a Propietario: $" . number_format((float)$o->precio_total, 2, '.', '');
                    } 

                    $o->tipo = $tipo;
                    
                    // ver si fue cancelado desde panel de control
                    $o->canceladoextra = $tiempo->cancelado_extra;
                  
                    // buscar si aplico cupon
                    if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()){
                                        
                        // buscar tipo de cupon
                        $tipo = Cupones::where('id', $oc->cupones_id)->first();

                        // ver que tipo se aplico
                        // el precio envio ya esta modificado
                        if($tipo->tipo_cupon_id == 1){
                            $cupon = "Envío Gratis";
                            
                            // no sumara precio envio, ya que esta seteado a $0.00 por cupon envio gratis 
                            // cobrar a cliente                          
                            $o->precio_total = number_format((float)$o->precio_total, 2, '.', '');

                        }else if($tipo->tipo_cupon_id == 2){
                        
                            // modificar precio
                            $dd = AplicaCuponDos::where('ordenes_id', $o->id)->first();

                            $total = $o->precio_total - $dd->dinero;
                            if($total <= 0){
                                $total = 0;
                            }

                            // si aplico envio gratis
                            if($dd->aplico_envio_gratis == 1){
                                $cupon = "Descuento dinero: $" . $dd->dinero . " + Envío Gratis";
                                
                            }else{
                                $cupon = "Descuento dinero: $" . $dd->dinero;
                            }

                            //** NO importa sumar el envio, ya que si aplico envio gratis, el precio_envio sera $0.00 */
                            // sumar el precio de envio
                            $suma = $total + $o->precio_envio; 

                            // precio modificado con el descuento dinero
                            $o->precio_total = number_format((float)$suma, 2, '.', '');

                        }else if($tipo->tipo_cupon_id == 3){                          

                            $porcentaje = AplicaCuponTres::where('ordenes_id', $o->id)->pluck('porcentaje')->first();
                            $resta = $o->precio_total * ($porcentaje / 100);
                            $total = $o->precio_total - $resta;

                            if($total <= 0){
                                $total = 0;
                            }

                            $cupon = "Descuento de: " . $porcentaje . "%";

                            // sumar el precio de envio
                            $suma = $total + $o->precio_envio;

                            $o->precio_total = number_format((float)$suma, 2, '.', '');

                        }else if($tipo->tipo_cupon_id == 4){
                        
                            $producto = AplicaCuponCuatro::where('ordenes_id', $o->id)->pluck('producto')->first();

                            $sumado = $o->precio_total + $o->precio_envio;
                            $sumado = number_format((float)$sumado, 2, '.', '');

                            $cupon = "Producto Gratis: " . $producto;

                            $o->precio_total = $sumado;
                        }
                        else if($tipo->tipo_cupon_id == 5){ // donacion
                        
                            $donacion = AplicaCuponCinco::where('ordenes_id', $o->id)->pluck('dinero')->first();

                            $cupon = "Donación de: $" . $donacion;

                            // sumar
                            $suma = $o->precio_total + $o->precio_envio + $donacion;

                            $o->precio_total = number_format((float)$suma, 2, '.', '');
                        }else{
                            $total = $o->precio_total + $o->precio_envio;
                            $o->precio_total = number_format((float)$total, 2, '.', '');     
                        }

                    }else{                                            
                        $total = $o->precio_total + $o->precio_envio;
                        $o->precio_total = number_format((float)$total, 2, '.', '');                        
                    }

                    $o->cupon = $cupon;
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
                        'o.numero_casa', 'o.punto_referencia',
                        'o.latitud', 'o.longitud')
                ->where('o.ordenes_id', $request->ordenid)
                ->first();

                $servicioid = $or->servicios_id;

                $servicio = DB::table('servicios AS s')
                ->select('s.nombre', 's.telefono', 's.direccion', 's.latitud', 's.longitud', 's.producto_visible')
                ->where('s.id', $servicioid)
                ->first();

                $time1 = Carbon::parse($or->fecha_4);
               
                $horaEstimada = $time1->addMinute($or->hora_2)->format('h:i A');
                $horaEstimada = $horaEstimada;              


                // titulo que dira la notificacion, cuando se alerte al cliente que esta llegando su pedido.
                $mensaje = "Su orden #" . $request->ordenid . " esta llegando";
                
                return ['success' => 1, 'ordenes' => $orden,
                 'servicio' => $servicio, 'hora' => $horaEstimada, 
                 'estado' => $or->estado_6, 'cancelado' => $or->estado_8, 'mensaje' => $mensaje];
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
                     
                    if(!empty($device)){
                        if($device != "0000"){ // evitar id malos
                            try {
                                $this->envioNoticacionCliente($titulo, $mensaje, $device); 
                            } catch (Exception $e) {
                                
                            }
                        }                        
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
                  
                    if(!empty($device)){
                        if($device != "0000"){
                            try {
                                $this->envioNoticacionCliente($titulo, $mensaje, $device); 
                            } catch (Exception $e) {
                                
                            }                            
                        }                        
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

    // borrar orden, cuando fue cancelada extraordinariamente, y el motorista la acepto antes de los 5 minutos
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

                $orden;

                if($request->filtro == 1){ // solo ordenes donde se le pago a propietario
                    $orden = DB::table('motorista_ordenes AS m')
                    ->join('ordenes AS o', 'o.id', '=', 'm.ordenes_id')
                    ->select('o.id', 'o.precio_total', 'o.precio_envio', 'o.fecha_orden', 
                    'm.motoristas_id', 'o.ganancia_motorista', 'o.estado_7', 'o.servicios_id', 'o.pago_a_propi')
                    ->where('o.estado_7', 1) // solo completadas
                    ->where('m.motoristas_id', $request->id) // del motorista
                    ->where('o.pago_a_propi', 1)
                    ->whereBetween('o.fecha_orden', [$start, $end]) 
                    ->orderBy('o.id', 'DESC')
                    ->get();
                }else{
                    $orden = DB::table('motorista_ordenes AS m')
                    ->join('ordenes AS o', 'o.id', '=', 'm.ordenes_id')
                    ->select('o.id', 'o.precio_total', 'o.precio_envio', 'o.fecha_orden', 
                    'm.motoristas_id', 'o.ganancia_motorista', 'o.estado_7', 'o.servicios_id', 'o.pago_a_propi')
                    ->where('o.estado_7', 1) // solo completadas
                    ->where('m.motoristas_id', $request->id) // del motorista
                    ->whereBetween('o.fecha_orden', [$start, $end]) 
                    ->orderBy('o.id', 'DESC')
                    ->get();
                }

                foreach($orden as $o){
                   
                    $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));
                    
                    // nombre servicio
                    $o->servicio = Servicios::where('id', $o->servicios_id)->pluck('nombre')->first();

                    // sacar direccion guardada de la orden
                    $o->direccion = OrdenesDirecciones::where('ordenes_id', $o->id)->pluck('direccion')->first();
                    
                    $cupon = "";
                    $tipo = "";

                    if($o->pago_a_propi == 1){
                        $tipo = "Se pago a Propietario: $" . number_format((float)$o->precio_total, 2, '.', '');
                    }
                    $o->tipo = $tipo;

                    // buscar si aplico cupon
                    if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()){
                        
                        // buscar tipo de cupon
                        $tipo = Cupones::where('id', $oc->cupones_id)->first();

                        // ver que tipo se aplico
                        // el precio envio ya esta modificado
                        if($tipo->tipo_cupon_id == 1){
                             
                            $cupon = "Envío Gratis";
                            // no sumara precio envio, ya que esta seteado a $0.00 por cupon envio gratis                           
                            $o->precio_total = number_format((float)$o->precio_total, 2, '.', '');
                      

                        }else if($tipo->tipo_cupon_id == 2){                           

                            $dd = AplicaCuponDos::where('ordenes_id', $o->id)->first();

                            $total = $o->precio_total - $dd->dinero;
                            if($total <= 0){
                                $total = 0;
                            }

                            // si aplico envio gratis
                            if($dd->aplico_envio_gratis == 1){
                                $cupon = "Descuento dinero: $" . $dd->dinero . " + Envío Gratis";
                            }else{
                                $cupon = "Descuento dinero: $" . $dd->dinero;
                            }

                            //** NO importa sumar el envio, ya que si aplico envio gratis, el precio_envio sera $0.00 */

                            // sumar el precio de envio
                            $suma = $total + $o->precio_envio;

                            // precio modificado con el descuento dinero
                            $o->precio_total = number_format((float)$suma, 2, '.', '');

                        }else if($tipo->tipo_cupon_id == 3){                          

                            $porcentaje = AplicaCuponTres::where('ordenes_id', $o->id)->pluck('porcentaje')->first();
                            $resta = $o->precio_total * ($porcentaje / 100);
                            $total = $o->precio_total - $resta;

                            if($total <= 0){
                                $total = 0;
                            }

                            $cupon = "Descuento de: " . $porcentaje . "%";

                            // sumar el precio de envio
                            $suma = $total + $o->precio_envio;

                            $o->precio_total = number_format((float)$suma, 2, '.', '');

                        }else if($tipo->tipo_cupon_id == 4){
                          
                            $producto = AplicaCuponCuatro::where('ordenes_id', $o->id)->pluck('producto')->first();

                            $sumado = $o->precio_total + $o->precio_envio;
                            $sumado = number_format((float)$sumado, 2, '.', '');

                            $cupon = "Producto Gratis: " . $producto;
    
                            $o->precio_total = $sumado;
                        }
                        else if($tipo->tipo_cupon_id == 5){ // donacion
                           
                            $donacion = AplicaCuponCinco::where('ordenes_id', $o->id)->pluck('dinero')->first();

                            $cupon = "Donación de: $" . $donacion;

                            // sumar
                            $suma = $o->precio_total + $o->precio_envio + $donacion;

                            $o->precio_total = number_format((float)$suma, 2, '.', '');
                        }else{
                            $total = $o->precio_total + $o->precio_envio;
                            $o->precio_total = number_format((float)$total, 2, '.', '');     
                        }

                    }else{
                        
                        $total = $o->precio_total + $o->precio_envio;                       
                        $o->precio_total = number_format((float)$total, 2, '.', '');
                    }

                    $o->cupon = $cupon;
                }

                // sumar ganancia de motorista de esta fecha
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
                    $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_agarrada));
                    
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


    // notificar al cliente su orden esta cerca
    public function notificarClienteOrden(Request $request){
        if($request->isMethod('post')){ 
            $reglaDatos = array(
                'ordenid' => 'required'
            );

            $mensajeDatos = array(                                      
                'ordenid.required' => 'El orden id es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            // obtener usuario de la orden
            if($o = Ordenes::where('id', $request->ordenid)->first()){
                
                $datos = User::where('id', $o->users_id)->first();
                
                if($datos->device_id != "0000"){

                    $titulo = "El motorista se encuentra cerca de tu ubicación";
                    $message = "Su orden esta cerca";

                        // ver tipo de notiificacion, 
                        // completado   .mp3 .wav
                        // mensaje   .mp3  .wav

                        $odd = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                        if($odd->movil_orden == "3"){
                            // cliente no tiene el nuevo sonido
                            try {
                                $this->envioNoticacionCliente($titulo, $message, $datos->device_id); 
                            } catch (Exception $e) {
                                
                            }
                        }else{
                            // cliente ya tiene el nuevo sonido
                            try {
                                $this->envioNoticacionClienteAlerta($titulo, $message, $datos->device_id); 
                            } catch (Exception $e) {
                                
                            }

                            $mensaje = "Notificación enviada";
                    
                            return ['success' => 1, 'mensaje' => $mensaje];
                        }
                       

                    $mensaje = "Notificación enviada";
                    
                    return ['success' => 1, 'mensaje' => $mensaje];
                }else{

                    $mensaje = "Notificación no se pudo enviar";

                    return ['success' => 2, 'mensaje' => $mensaje];
                }    
            }
        }
    }


    public function verNuevosOrdenesEncargos(Request $request){
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
                                

                $noquiero = MotoristaOrdenEncargo::all();

                $pilaOrden = array();
                foreach($noquiero as $p){
                    array_push($pilaOrden, $p->ordenes_encargo_id);
                } 

                // una vez el ordenes_encargo tenga permiso para motorista,
                // lo podra agarrar, pero solo iniciara si ya fue completada,
                // por un propietario
               
                $orden = DB::table('ordenes_encargo AS oe')
                ->join('encargos AS e', 'e.id', '=', 'oe.encargos_id')
                ->join('motorista_encargo_asignado AS m', 'm.encargos_id', '=', 'e.id')
                ->select('oe.id', 'e.nombre', 'e.fecha_entrega', 'oe.encargos_id',
                 'oe.precio_subtotal', 'oe.precio_envio', 'oe.pago_a_propi')
                ->where('e.permiso_motorista', 1) // ya tiene permiso de ver todas las ordenes de ese encargo
                ->where('oe.visible_motorista', 1) // visible a motorista
                ->where('m.motoristas_id', $request->id) 
                ->where('oe.revisado', 2) // solo ordenes en proceso 
                ->whereNotIn('oe.id', $pilaOrden) // no ver las ordenes ya tomadas
                ->get();

                // # orden, nombre encargo, direccion, total, ver gps, fecha estimada de entrega al cliente
          
                foreach($orden as $o){
                  
                    $o->fecha_entrega = date("h:i A d-m-Y", strtotime($o->fecha_entrega));
                    $dd = OrdenesEncargoDireccion::where('ordenes_encargo_id', $o->id)->first();
               
                    $o->direccion = $dd->direccion;
                    $o->latitud = $dd->latitud;
                    $o->longitud = $dd->longitud;
                    
                    $tipo = "";
                    if($o->pago_a_propi == 1){
                        // pagar a propietario
                        $tipo = "Pagar a Propietario $". $o->precio_subtotal;
                    }

                    $o->tipo = $tipo;

                    $suma = $o->precio_subtotal + $o->precio_envio;
                    $o->total = number_format((float)$suma, 2, '.', '');

                    $servicio = "";
                    if($dd = EncargoAsignadoServicio::where('encargos_id', $o->encargos_id)->first()){
                        $servicio = Servicios::where('id', $dd->servicios_id)->pluck('nombre')->first();
                    }

                    $o->servicio = $servicio;
               
                }
                
                return ['success' => 1, 'ordenes' => $orden];
            }else{
                return ['success' => 2];
            }
        }
    }


    public function verListaDeProductosDeEncargo(Request $request){
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
                ->select('o.id', 'p.imagen', 'o.nombre', 'o.descripcion', 'o.cantidad', 'o.precio')
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


    public function verListaDeProductosDeEncargoIndividual(Request $request){

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

            if(OrdenesEncargoProducto::where('id', $request->id)->first()){

                $producto = DB::table('ordenes_encargo_producto AS o')
                ->join('producto_categoria_negocio AS p', 'p.id', '=', 'o.producto_cate_nego_id')
                ->select('o.id', 'p.imagen', 'o.nombre', 'o.nota', 'o.descripcion', 'o.cantidad', 'o.precio')
                ->where('o.id', $request->id)
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

    public function aceptarOrdenEncargo(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required',
                'idencargo' => 'required'             
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id  es requerido.',
                'idencargo.required' => 'el id del encargo es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            if(MotoristaOrdenEncargo::where('ordenes_encargo_id', $request->idencargo)->first()){
                return ['success' => 1];
            }
            
            if($dd = MotoristaOrdenEncargo::where('ordenes_encargo_id', $request->idencargo)->first()){
                
                if($dd->revisado == 5){ // orden cancelada
                    return ['success' => 2];
                }
            }

            $fecha = Carbon::now('America/El_Salvador');

            $nueva = new MotoristaOrdenEncargo;
            $nueva->ordenes_encargo_id = $request->idencargo;
            $nueva->motoristas_id = $request->id;
            $nueva->fecha_agarrada = $fecha;
            
            if($nueva->save()){
                return ['success' => 3];
            }else{
                return ['success' => 4];
            }
        }  
    }

    public function verNuevosOrdenesEncargosProceso(Request $request){

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

                $orden = DB::table('motorista_ordenes_encargo AS mo')
                ->join('ordenes_encargo AS o', 'o.id', '=', 'mo.ordenes_encargo_id')
                ->select('o.id', 'o.precio_subtotal', 'o.precio_envio', 
                'o.estado_1', 'o.encargos_id', 'o.revisado', 'o.pago_a_propi')
                ->where('o.visible_motorista', 1)
                ->where('o.estado_2', 0) // aun no han salido a entregarse 
                ->where('mo.motoristas_id', $request->id)
                ->get();

                foreach($orden as $o){

                    $servicio = "Encargo Privado";
                    if($dd = EncargoAsignadoServicio::where('encargos_id', $o->encargos_id)->first()){
                        $servicio = Servicios::where('id', $dd->servicios_id)->pluck('nombre')->first();
                    }

                    $tipo = "";
                    if($o->pago_a_propi == 1){
                        // pagar a propietario
                        $tipo = "Pagar a Propietario $". $o->precio_subtotal;
                    }

                    $o->tipo = $tipo;

                    $ee = Encargos::where('id', $o->encargos_id)->first();

                    $o->nombreencargo = $ee->nombre;
                    $o->fechaentrega = date("h:i A d-m-Y", strtotime($ee->fecha_entrega));

                    $suma = $o->precio_envio + $o->precio_subtotal;
                    $o->total = number_format((float)$suma, 2, '.', '');
                }

                return ['success' => 1, 'ordenes' => $orden];
            }else{
                return ['success' => 2];
            }
        }
    }

    public function verNuevosOrdenesEncargosProcesoEstado(Request $request){

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
                
                // mostrar si fue cancelada para despues setear visible_m

                $orden = DB::table('motorista_ordenes_encargo AS mo')
                ->join('ordenes_encargo AS o', 'o.id', '=', 'mo.ordenes_encargo_id')
                ->select('o.id', 'o.estado_1', 
                'o.encargos_id', 'o.revisado')
                ->where('o.estado_3', 0) // aun sin entregar al cliente
                //->where('o.visible_motorista', 1) // no es necesario aqui
                ->where('o.estado_2', 0) // aun no han salido a entregarse
                ->where('o.id', $request->id)
                ->get();

                foreach($orden as $o){

                    $servicio = "Encargo Privado";
                    $ubicacion = "";
                    $latiservicio = "";
                    $longiservicio = "";

                   
                    if($dd = EncargoAsignadoServicio::where('encargos_id', $o->encargos_id)->first()){
                        $info = Servicios::where('id', $dd->servicios_id)->first();
                        
                        $servicio = $info->nombre;
                        $ubicacion = $info->direccion;
                        $latiservicio = $info->latitud;
                        $longiservicio = $info->longitud;                      
                    }

                    $o->ubicacion = $ubicacion;
                    $o->latiservicio = $latiservicio;
                    $o->longiservicio = $longiservicio;

                    $ee = Encargos::where('id', $o->encargos_id)->first();

                    $o->nombreencargo = $ee->nombre;
                    $o->fechaentrega = date("h:i A d-m-Y", strtotime($ee->fecha_entrega));

                    $o->servicio = $servicio;
 
                    $dd = OrdenesEncargoDireccion::where('ordenes_encargo_id', $o->id)->first();

                    $o->nombrecliente = $dd->nombre;
                    $o->direcion = $dd->direccion;
                    $o->numerocasa = $dd->numero_casa;
                    $o->puntoreferencia = $dd->punto_referencia;
                    $o->latitud = $dd->latitud;
                    $o->longitud = $dd->longitud;
                }

                return ['success' => 1, 'ordenes' => $orden];
            }else{
                return ['success' => 2];
            }
        }
    }


    public function iniciarEntregaEncargo(Request $request){
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

            if($oo = OrdenesEncargo::where('id', $request->id)->first()){

                // cancelado
                if($oo->revisado == 5){
                    return ['success' => 1];
                }

                // orden aun no lista para ser entregada
                if($oo->estado_1 == 0){
                    return ['success' => 2];
                }

                $fecha = Carbon::now('America/El_Salvador');

                if($oo->estado_2 == 0){
                     // actualizar estado, motorista va en camino
                    OrdenesEncargo::where('id', $request->id)->update(['estado_2' => 1, 
                    'fecha_2' => $fecha, 'revisado' => 3]);

                    // envio de notificacion al cliente
                    $dd = User::where('id', $oo->users_id)->first();
                    if($dd->device_id != "0000"){

                        $titulo = "Encargo #" . $oo->id;
                        $mensaje = "Su encargo va en camino";

                        try {
                            $this->envioNoticacionCliente($titulo, $mensaje, $dd->device_id); 
                        } catch (Exception $e) {
                            
                        }
                    }

                }

                return ['success' => 3];

            }else{
                return ['success' => 0];
            }
        }
    }


    public function ocultarOrdenEncargoMotorista(Request $request){
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

            if($oo = OrdenesEncargo::where('id', $request->id)->first()){

                // sino esta cancelada, no se puede ocultar
                if($oo->revisado != 5){
                    return ['success' => 1];
                }

                OrdenesEncargo::where('id', $request->id)->update(['visible_motorista' => 0]);

                return ['success' => 2];

            }else{
                return ['success' => 0];
            }
        }
    }


    public function listaEncargosEnEntrega(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'idmoto' => 'required'                
            );
        
            $mensajeDatos = array(                                      
                'idmoto.required' => 'El id del motorista es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            if(Motoristas::where('id', $request->idmoto)->first()){
                
                // mostrar si fue cancelada para despues setear visible_m

                $orden = DB::table('motorista_ordenes_encargo AS mo')
                ->join('ordenes_encargo AS o', 'o.id', '=', 'mo.ordenes_encargo_id')
                ->select('o.id', 'o.precio_subtotal', 'o.precio_envio', 'o.estado_1',
                 'o.encargos_id', 'o.revisado', 'o.pago_a_propi')
                ->where('o.estado_3', 0) // aun sin entregar al cliente
                ->where('o.visible_motorista', 1) // aun visible al motorista
                ->where('o.estado_2', 1) // ya salio a entregarse
                ->where('o.revisado', 3) // en modo entrega
                ->where('mo.motoristas_id', $request->idmoto) // todas las que agarro este motorista
                ->get();

                foreach($orden as $o){

                    $tipo = "";
                    $dd = EncargoAsignadoServicio::where('encargos_id', $o->encargos_id)->first();
                    $o->servicio = Servicios::where('id', $dd->servicios_id)->pluck('nombre')->first();
                    
                    if($o->pago_a_propi == 1){
                        $tipo = "Se Paga a Propietario $" . number_format((float)$o->precio_subtotal, 2, '.', '');
                    }

                    $o->tipo = $tipo;

                    $ee = Encargos::where('id', $o->encargos_id)->first();

                    $o->nombreencargo = $ee->nombre;
                    $o->fechaentrega = date("h:i A d-m-Y", strtotime($ee->fecha_entrega));

                    $suma = $o->precio_envio + $o->precio_subtotal;
                    $o->total = number_format((float)$suma, 2, '.', ''); 
 
                }

                return ['success' => 1, 'ordenes' => $orden];
            }else{
                return ['success' => 2];
            }
        }
    }

    // ver estado del encargo a finalizar. 
    public function verEstadoOrdenEncargoAFinalizar(Request $request){

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
                
                $orden = DB::table('motorista_ordenes_encargo AS mo')
                ->join('ordenes_encargo AS o', 'o.id', '=', 'mo.ordenes_encargo_id')
                ->select('o.id', 'o.precio_subtotal', 'o.precio_envio', 'o.estado_1', 
                'o.encargos_id', 'o.revisado')
                ->where('o.id', $request->id)
                ->get();

                foreach($orden as $o){

                    $servicio = "";
                    $ubicacion = "";
                    $latiservicio = "";
                    $longiservicio = "";

                    $dd = EncargoAsignadoServicio::where('encargos_id', $o->encargos_id)->first();
                    $info = Servicios::where('id', $dd->servicios_id)->first();
                    
                    $servicio = $info->nombre;
                    $ubicacion = $info->direccion;
                    $latiservicio = $info->latitud;
                    $longiservicio = $info->longitud;                    

                    $o->ubicacion = $ubicacion;
                    $o->latiservicio = $latiservicio;
                    $o->longiservicio = $longiservicio;

                    $ee = Encargos::where('id', $o->encargos_id)->first();

                    $o->nombreencargo = $ee->nombre;
                    $o->fechaentrega = date("h:i A d-m-Y", strtotime($ee->fecha_entrega));

                    $o->servicio = $servicio;

                    $dd = OrdenesEncargoDireccion::where('ordenes_encargo_id', $o->id)->first();

                    $o->nombrecliente = $dd->nombre;
                    $o->direcion = $dd->direccion;
                    $o->numerocasa = $dd->numero_casa;
                    $o->puntoreferencia = $dd->punto_referencia;
                    $o->latitud = $dd->latitud;
                    $o->longitud = $dd->longitud;
                }

                return ['success' => 1, 'ordenes' => $orden];
            }else{
                return ['success' => 2];
            }
        }
    }


    // finalizar entrega del encargo
    public function finalizarEntregaEncargo(Request $request){
      
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'                
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id del encargo es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            if($oo = OrdenesEncargo::where('id', $request->id)->first()){     

                $fecha = Carbon::now('America/El_Salvador');

                if($oo->estado_3 == 0){
                    OrdenesEncargo::where('id', $request->id)->update(['estado_3' => 1, 
                    'fecha_3' => $fecha, 'visible_motorista' => 0, 'revisado' => 4]);
                    // notificar al cliente que su encargo a sido entregado


                    $dd = User::where('id', $oo->users_id)->first();
                    if($dd->device_id != "0000"){

                        $titulo = "Encargo Completado";
                        $mensaje = "Muchas Gracias por su Compra";

                        try {
                            $this->envioNoticacionCliente($titulo, $mensaje, $dd->device_id); 
                        } catch (Exception $e) {
                            
                        }
                    }
                }


                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }

    // mandar notificacion al cliente del encargo
    public function notificarClienteDelEncargo(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'                
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id del encargo es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            if($o = OrdenesEncargo::where('id', $request->id)->first()){     

                $datos = User::where('id', $o->users_id)->first();
                
                if($datos->device_id != "0000"){

                    $titulo = "El Motorista se encuentra cerca de tu ubicación";
                    $message = "Su orden esta cerca";

                        try {
                            $this->envioNoticacionCliente($titulo, $message, $datos->device_id); 
                        } catch (Exception $e) {
                            
                        }

                    $mensaje = "Notificación enviada";
            
                    return ['success' => 1, 'mensaje' => $mensaje];

                }else{

                    $mensaje = "Notificación no se pudo enviar";

                    return ['success' => 2, 'mensaje' => $mensaje];
                }  


                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }


    // historial de encargos completados
    public function verHistorialEncargosCompletados(Request $request){

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

                $orden;

                if($request->filtro == 1){ // ordenes encargo que motorista pago a propietario
                    $orden = DB::table('motorista_ordenes_encargo AS m')
                    ->join('ordenes_encargo AS o', 'o.id', '=', 'm.ordenes_encargo_id')
                    ->select('o.id', 'o.precio_subtotal', 'o.precio_envio', 'o.fecha_3', 
                    'm.motoristas_id', 'o.ganancia_motorista', 'o.encargos_id', 'o.pago_a_propi')
                    ->where('o.estado_3', 1) // solo completadas
                    ->where('m.motoristas_id', $request->id) // del motorista
                    ->where('o.pago_a_propi', 1)
                    ->whereBetween('o.fecha_3', [$start, $end]) 
                    ->orderBy('o.id', 'DESC')
                    ->get();
                }else{

                    // revueltos, pagado a propietario y no
                    $orden = DB::table('motorista_ordenes_encargo AS m')
                    ->join('ordenes_encargo AS o', 'o.id', '=', 'm.ordenes_encargo_id')
                    ->select('o.id', 'o.precio_subtotal', 'o.precio_envio', 'o.fecha_3', 
                    'm.motoristas_id', 'o.ganancia_motorista', 'o.encargos_id', 'o.pago_a_propi')
                    ->where('o.estado_3', 1) // solo completadas
                    ->where('m.motoristas_id', $request->id) // del motorista
                    ->whereBetween('o.fecha_3', [$start, $end]) 
                    ->orderBy('o.id', 'DESC')
                    ->get();
                }

                foreach($orden as $o){
                   
                    $o->fecha_3 = date("h:i A d-m-Y", strtotime($o->fecha_3));
                    
                    $servicio = "Encargo Privado";
                    $dd = EncargoAsignadoServicio::where('encargos_id', $o->encargos_id)->first();
                    $o->servicio = Servicios::where('id', $dd->servicios_id)->pluck('nombre')->first();
                    
                    $tipo = "";
                    if($o->pago_a_propi == 1){
                        $tipo = "Se Pago a Propietario $" . number_format((float)$o->precio_subtotal, 2, '.', '');
                    }                    
                    $o->tipo = $tipo;
                    
                    // se cobro a cliente
                    $suma = $o->precio_subtotal + $o->precio_envio;
                    $o->total = number_format((float)$suma, 2, '.', '');

                    $o->precio_envio = number_format((float)$o->precio_envio, 2, '.', '');
                }

                // sumar ganancia de motorista de esta fecha
                $suma = collect($orden)->sum('ganancia_motorista');
                $ganado = number_format((float)$suma, 2, '.', '');
                return ['success' => 1, 'histoorden' => $orden, 'ganado' => $ganado];
            }else{
                return ['success' => 2];
            }
        }
    }



    public function envioNoticacionCliente($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionCliente($titulo, $mensaje, $pilaUsuarios);
    }

    public function envioNoticacionClienteAlerta($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionClienteAlerta($titulo, $mensaje, $pilaUsuarios);
    }
} 
