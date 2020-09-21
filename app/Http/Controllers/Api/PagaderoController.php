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
use App\BitacoraRevisador;
use App\Zonas;
use App\OrdenRevisada;
use App\Revisador;
use App\OrdenesDirecciones;
use App\OrdenesCupones;
use App\Cupones;
use App\AplicaCuponCuatro;
use App\AplicaCuponTres;
use App\AplicaCuponDos; 
use App\AplicaCuponCinco;
use App\OrdenesEncargoRevisadas;
use App\MotoristaOrdenEncargo;
use App\OrdenesEncargo;
use Log;

class PagaderoController extends Controller
{
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
         
            if($p = Revisador::where('telefono', $request->phone)->first()){

                if($p->activo == 0){
                    return ['success' => 1]; // revisador no activo
                }

                if (Hash::check($request->password, $p->password)) {

                    $id = $p->id;
                  
                    return ['success' => 2, 'usuario_id' => $id]; // login correcto
                }    else{
                    return ['success' => 3]; // contraseña incorrecta
                }
            }else{
                return ['success' => 4]; // datos incorrectos
            }
        }
    }

    // cambio de contraseña
    public function actualizarPassword(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
                'password' => 'required'
            );

            $messages = array(                                      
                'id.required' => 'El id revisador es requerido',
                'password.required' => 'El password es requerida'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Revisador::where('id', $request->id)->first()){
                
                Revisador::where('id', $request->id)->update(['password' => Hash::make($request->password)]);
                                
                return ['success'=> 1];
            }else{
                return ['success'=> 2];
            }
        }
    }

    // ordene pediente de pago
    public function pendientePago(Request $request){
        if($request->isMethod('post')){ 
            $reglaDatos = array(
                'id' => 'required',
                'motoristaid' => 'required'
            );

            $mensajeDatos = array(                                      
                'id.required' => 'El id revisador es requerido.',
                'motoristaid.required' => 'El motorista id es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            } 
            
            if(Revisador::where('id', $request->id)->first()){

                // estas ordenes ya fueron revisadas
                $noquiero = DB::table('ordenes_revisadas')->get();

                $pilaOrden = array();
                foreach($noquiero as $p){
                    array_push($pilaOrden, $p->ordenes_id);
                }

                $orden = DB::table('motorista_ordenes AS mo')
                ->join('ordenes AS o', 'o.id', '=', 'mo.ordenes_id')
                ->select('o.id', 'mo.motoristas_id', 'o.precio_total', 'o.precio_envio', 'o.fecha_5', 
                'o.servicios_id', 'o.estado_8', 'o.fecha_7', 'o.pago_a_propi', 'o.tipo_pago')
                ->where('mo.motoristas_id', $request->motoristaid)               
                ->where('o.estado_6', 1) // ordenes que motorista inicio la entrega 
                ->where('o.estado_8', 0) // no canceladas
                ->whereNotIn('o.id', $pilaOrden) // filtro para no ver ordenes revisadas
                ->get();

                $totalcobro = 0;

                foreach($orden as $o){


                    if($o->fecha_7 == null){
                        $o->fecha_orden = "Sin completar aun";
                    }else{
                        $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_7));
                    }
                     
                    $aplicacupon = 0;
                    $textocupon = "";
                    $mensaje = "Pago realizado a Propietario: $" . number_format((float)$o->precio_total, 2, '.', '');

                    // buscar si aplico cupon
                    if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()){
                     
                        $aplicacupon = 1;

                        // buscar tipo de cupon
                        $tipo = Cupones::where('id', $oc->cupones_id)->first();

                        // ver que tipo se aplico
                        // el precio envio ya esta modificado
                        if($tipo->tipo_cupon_id == 1){
                            $o->tipocupon = 1;

                            if($o->pago_a_propi == 1){
                                // se paga a propietario
                               
                                // NO SUMAR SI PAGO CON CREDI PUNTOS
                                if($o->tipo_pago == 0){
                                   // $totalcobro = $totalcobro + $o->precio_total; 
                                   // MENOS LO QUE SE PAGARA A PROPIETARIO
                                   // MAS LO QUE CANCELARA CLIENTE
                                   // envio es siempre 0
                                   $info = -$o->precio_total + $o->precio_total;
                                   $totalcobro = $totalcobro + $info;
                                }else{
                                    // credi puntos
                                    $totalcobro = $totalcobro - $o->precio_total;
                                }
                               // $o->precio_total = number_format((float), 2, '.', '');  
                               $o->precio_total = number_format((float)$o->precio_total, 2, '.', '');
                            }else{
                                // no se paga a propietario
                                // no sumara precio envio, ya que esta seteado a $0.00 por cupon envio gratis
                                // NO SUMAR SI PAGO CON CREDI PUNTOS
                                if($o->tipo_pago == 0){
                                    $totalcobro = $totalcobro + $o->precio_total;   
                                }
                                
                                $o->precio_total = number_format((float)$o->precio_total, 2, '.', '');
                            }
                            //
                            $textocupon = "Envío Gratis";
                           
                        }else if($tipo->tipo_cupon_id == 2){
                            $o->tipocupon = 2;
                            // modificar precio
                            $dd = AplicaCuponDos::where('ordenes_id', $o->id)->first();
                            $descuento = $dd->dinero;

                            $total = $o->precio_total - $descuento;
                            if($total <= 0){
                                $total = 0;
                            }

                            $aplicoenvio = 0; // para saver en la app si aplico tambien envio gratis

                            if($dd->aplico_envio_gratis == 0){                              
                                $textocupon = "Descuento de $ " . $descuento;
                            }else{
                                // si aplico el envio gratis este cupon
                                $aplicoenvio = 1;
                                $textocupon = "Descuento de $" . $descuento . " + Envío Gratis";
                            }

                            $o->aplicoenvio = $aplicoenvio;

                            if($o->pago_a_propi == 1){
                               
                                if($o->tipo_pago == 0){ // efectivo
                                    $info = -$o->precio_total + ($total + $o->precio_envio);
                                    $totalcobro = $totalcobro + $info;
                                                               
                                }else{
                                    // credi puntos
                                
                                    $totalcobro = $totalcobro - $o->precio_total;
                                }

                                $afectado = $o->precio_envio + $total;
                                $o->precio_total = number_format((float)$afectado, 2, '.', '');

                            }else{ 
                                // no se le paga a propietario

                                // sumar el precio de envio
                                $suma = $total + $o->precio_envio;

                                // precio modificado con el descuento dinero
                                $o->precio_total = number_format((float)$suma, 2, '.', '');
                                // NO SUMAR SI PAGO CON CREDI PUNTOS
                                if($o->tipo_pago == 0){
                                    $totalcobro = $totalcobro + $suma; 
                                }
                            }

                        }else if($tipo->tipo_cupon_id == 3){
                            $o->tipocupon = 3;

                            $porcentaje = AplicaCuponTres::where('ordenes_id', $o->id)->pluck('porcentaje')->first();
                            $resta = $o->precio_total * ($porcentaje / 100);
                            $total = $o->precio_total - $resta;

                            $textocupon = "Descuento del " . $porcentaje . "%";

                            if($total <= 0){
                                $total = 0;
                            }

                            if($o->pago_a_propi == 1){
                               
                                if($o->tipo_pago == 0){ // efectivo
                                    $info = -$o->precio_total + ($total + $o->precio_envio);
                                    $totalcobro = $totalcobro + $info;
                                                               
                                }else{
                                    // credi puntos
                                
                                    $totalcobro = $totalcobro - $o->precio_total;
                                }

                                $afectado = $o->precio_envio + $total;
                                $o->precio_total = number_format((float)$afectado, 2, '.', '');

                            }else{ 
                                // no se le paga a propietario

                                // sumar el precio de envio
                                $suma = $total + $o->precio_envio;

                                // precio modificado con el descuento dinero
                                $o->precio_total = number_format((float)$suma, 2, '.', '');
                                // NO SUMAR SI PAGO CON CREDI PUNTOS
                                if($o->tipo_pago == 0){
                                    $totalcobro = $totalcobro + $suma; 
                                }
                            }

                        }else if($tipo->tipo_cupon_id == 4){
                            $o->tipocupon = 4;
                            $producto = AplicaCuponCuatro::where('ordenes_id', $o->id)->pluck('producto')->first();
                            $o->producto = $producto;
                            $textocupon = "Producto Gratis: " . $producto;
                           
                           
                            if($o->pago_a_propi == 1){
                           
                                if($o->tipo_pago == 0){
                                    $info = -$o->precio_total + ($o->precio_total + $o->precio_envio);
                                    $totalcobro = $totalcobro + $info;                                
                                    $o->precio_total = number_format((float)$o->precio_total + $o->precio_envio, 2, '.', '');
                                }else{
                                    // CREDI PUNTOS
                                    $totalcobro = $totalcobro - $o->precio_total;
    
                                    $o->precio_total = number_format((float)0, 2, '.', '');
                                }
                               
                            }else{
                                // no se le paga a servicio 
    
                                $cobro = $o->precio_total + $o->precio_envio;
                                $o->precio_total = number_format((float)$cobro, 2, '.', '');
                                // NO SUMAR SI PAGO CON CREDI PUNTOS
                                if($o->tipo_pago == 0){
                                    $totalcobro = $totalcobro + $cobro; 
                                }
                            }


                        }
                        else if($tipo->tipo_cupon_id == 5){
                            $o->tipocupon = 5;
                            $donacion = AplicaCuponCinco::where('ordenes_id', $o->id)->pluck('dinero')->first();
                            $textocupon = "Donación de: " . $donacion;
                            
                            $total = $donacion + $o->precio_total;
                            
                            if($o->pago_a_propi == 1){
                           
                                if($o->tipo_pago == 0){
                                    $info = -$o->precio_total + ($total + $o->precio_envio);
                                    $totalcobro = $totalcobro + $info;                                
                                    $o->precio_total = number_format((float)$total + $o->precio_envio, 2, '.', '');
                                }else{
                                    // CREDI PUNTOS
                                    $totalcobro = $totalcobro - $o->precio_total;
    
                                    $o->precio_total = number_format((float)0, 2, '.', '');
                                }
                               
                            }else{
                                // no se le paga a servicio 
    
                                $cobro = $total + $o->precio_envio;
                                $o->precio_total = number_format((float)$cobro, 2, '.', '');
                                
                                if($o->tipo_pago == 0){
                                    $totalcobro = $totalcobro + $cobro; 
                                }
                            }
                          
                        }
                        else{
                            $o->tipocupon = 0;
                        }

                    }else{              

                        if($o->pago_a_propi == 1){
                           
                            if($o->tipo_pago == 0){
                                $info = -$o->precio_total + ($o->precio_total + $o->precio_envio);
                                $totalcobro = $totalcobro + $info;                                
                                $o->precio_total = number_format((float)$o->precio_total + $o->precio_envio, 2, '.', '');
                            }else{
                                // CREDI PUNTOS
                                $totalcobro = $totalcobro - $o->precio_total;

                                $o->precio_total = number_format((float)0, 2, '.', '');
                            }
                           
                        }else{
                            // no se le paga a servicio 

                            $cobro = $o->precio_total + $o->precio_envio;
                            $o->precio_total = number_format((float)$cobro, 2, '.', '');
                            // NO SUMAR SI PAGO CON CREDI PUNTOS
                            if($o->tipo_pago == 0){
                                $totalcobro = $totalcobro + $cobro; 
                            }
                        }

                        
                    }

                  
                    $o->mensaje = $mensaje; // lo que se pago a propietario
                    $o->aplicacupon = $aplicacupon;
                    $o->textocupon = $textocupon;
                  
                }
                
                // sumar ganancia de esta fecha
                $totalcobro = number_format((float)$totalcobro, 2, '.', '');

              
                
                return ['success' => 1, 'orden' => $orden, 'debe' => $totalcobro];
            }
        }
    }

    // confirmar pago
    public function confirmarPago(Request $request){
        if($request->isMethod('post')){ 
            $reglaDatos = array(
                'id' => 'required',
                'ordenid' => 'required',
                'codigo' => 'required'
            );

            $mensajeDatos = array(                                      
                'id.required' => 'El id revisador es requerido.',
                'ordenid.required' => 'El id orden es requerido.',
                'codigo.required' => 'El codigo es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($r = Revisador::where('id', $request->id)->first()){
                
                // verificar si ya existe el registro                 
                if(OrdenRevisada::where('ordenes_id', $request->ordenid)->first()){
                    return ['success' => 1];
                }
                
                if($r->codigo == $request->codigo){

                    if($oo = Ordenes::where('id', $request->ordenid)->first()){
                        if($oo->estado_7 == 0){
                            // orden aun no completada, no puede confirmar
                            return ['success' => 5];
                        }
                    }
                   
                    $fecha = Carbon::now('America/El_Salvador');

                    $nueva = new OrdenRevisada();
                    $nueva->ordenes_id = $request->ordenid;
                    $nueva->fecha = $fecha;
                    $nueva->revisador_id = $request->id;
                    
                    if($nueva->save()){
                        return ['success' => 2];
                    }else{
                        return ['success' => 3];
                    }
                }else{
                    return ['success' => 4]; // codigo incorrecto
                }
            }
        }
    }

    // lista de motoristas asignador a mi id
    public function verMotoristas(Request $request){
        if($request->isMethod('post')){ 
            $reglaDatos = array(
                'id' => 'required',    
            );
                  
            $mensajeDatos = array(                 
                'id.required' => 'El id es requerido',
                );
            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );
            if($validarDatos->fails())
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($r = Revisador::where('id', $request->id)->first()){

                if($r->activo == 0){
                    return ['success' => 1];
                }

                $motoristas = DB::table('revisador_motoristas AS r')
                ->join('motoristas AS m', 'm.id', '=', 'r.motoristas_id')
                ->select('m.id', 'm.nombre', 'm.imagen', 'm.disponible', 'm.activo')
                ->where('r.revisador_id', $request->id)
                ->where('m.activo', 1)
                ->get();

                return ['success' => 2, 'motoristas' => $motoristas];
            }else{
                return ['success' => 3];
            }
        }
    }

 
  
    // ver historial - version cuando motorista paga a propietario o no
    public function verHistorialNuevo(Request $request){
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
             
 
            if(Revisador::where('id', $request->id)->first()){
                    
                $start = Carbon::parse($request->fecha1)->startOfDay(); 
                $end = Carbon::parse($request->fecha2)->endOfDay();
            
                $orden = DB::table('ordenes_revisadas AS r')
                ->join('ordenes AS o', 'o.id', '=', 'r.ordenes_id')
                ->select('o.id', 'o.precio_total', 'r.fecha', 
                'o.precio_envio', 'o.pago_a_propi', 'o.tipo_pago')
                ->where('r.revisador_id', $request->id)
                ->whereBetween('r.fecha', [$start, $end]) 
                ->orderBy('o.id', 'ASC')
                ->get();

                $totalcobro = 0;

                foreach($orden as $o){

                    $motorista = "";

                    if($mm = MotoristaOrdenes::where('ordenes_id', $o->id)->first()){
                        $motorista = Motoristas::where('id', $mm->motoristas_id)->pluck('nombre')->first();
                    }

                    $o->fecha = date("h:i A d-m-Y", strtotime($o->fecha));

                    $o->motorista = $motorista;
                     

                    // buscar si aplico cupon
                    if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()){
                     

                        // buscar tipo de cupon
                        $tipo = Cupones::where('id', $oc->cupones_id)->first();

                        // ver que tipo se aplico
                        // el precio envio ya esta modificado
                        if($tipo->tipo_cupon_id == 1){

                            if($o->pago_a_propi == 1){
                                // se paga a propietario
                               
                                // NO SUMAR SI PAGO CON CREDI PUNTOS
                                if($o->tipo_pago == 0){
                                   // $totalcobro = $totalcobro + $o->precio_total; 
                                   // MENOS LO QUE SE PAGARA A PROPIETARIO
                                   // MAS LO QUE CANCELARA CLIENTE
                                   // envio es siempre 0
                                   $info = -$o->precio_total + $o->precio_total;
                                   $totalcobro = $totalcobro + $info;
                                }else{
                                    // credi puntos
                                    $totalcobro = $totalcobro - $o->precio_total;
                                }
                               // $o->precio_total = number_format((float), 2, '.', '');  
                               $o->precio_total = number_format((float)$o->precio_total, 2, '.', '');
                            }else{
                                // no se paga a propietario
                                // no sumara precio envio, ya que esta seteado a $0.00 por cupon envio gratis
                                // NO SUMAR SI PAGO CON CREDI PUNTOS
                                if($o->tipo_pago == 0){
                                    $totalcobro = $totalcobro + $o->precio_total;   
                                }
                                
                                $o->precio_total = number_format((float)$o->precio_total, 2, '.', '');
                            }
                            //
                           
                        }else if($tipo->tipo_cupon_id == 2){
                            $o->tipocupon = 2;
                            // modificar precio
                            $dd = AplicaCuponDos::where('ordenes_id', $o->id)->first();
                            $descuento = $dd->dinero;

                            $total = $o->precio_total - $descuento;
                            if($total <= 0){
                                $total = 0;
                            }

                            if($o->pago_a_propi == 1){
                               
                                if($o->tipo_pago == 0){ // efectivo
                                    $info = -$o->precio_total + ($total + $o->precio_envio);
                                    $totalcobro = $totalcobro + $info;
                                                               
                                }else{
                                    // credi puntos
                                
                                    $totalcobro = $totalcobro - $o->precio_total;
                                }

                                $afectado = $o->precio_envio + $total;
                                $o->precio_total = number_format((float)$afectado, 2, '.', '');

                            }else{ 
                                // no se le paga a propietario

                                // sumar el precio de envio
                                $suma = $total + $o->precio_envio;

                                // precio modificado con el descuento dinero
                                $o->precio_total = number_format((float)$suma, 2, '.', '');
                                // NO SUMAR SI PAGO CON CREDI PUNTOS
                                if($o->tipo_pago == 0){
                                    $totalcobro = $totalcobro + $suma; 
                                }
                            }

                        }else if($tipo->tipo_cupon_id == 3){

                            $porcentaje = AplicaCuponTres::where('ordenes_id', $o->id)->pluck('porcentaje')->first();
                            $resta = $o->precio_total * ($porcentaje / 100);
                            $total = $o->precio_total - $resta;


                            if($total <= 0){
                                $total = 0;
                            }

                            if($o->pago_a_propi == 1){
                               
                                if($o->tipo_pago == 0){ // efectivo
                                    $info = -$o->precio_total + ($total + $o->precio_envio);
                                    $totalcobro = $totalcobro + $info;
                                                               
                                }else{
                                    // credi puntos
                                
                                    $totalcobro = $totalcobro - $o->precio_total;
                                }

                                $afectado = $o->precio_envio + $total;
                                $o->precio_total = number_format((float)$afectado, 2, '.', '');

                            }else{ 
                                // no se le paga a propietario

                                // sumar el precio de envio
                                $suma = $total + $o->precio_envio;

                                // precio modificado con el descuento dinero
                                $o->precio_total = number_format((float)$suma, 2, '.', '');
                                // NO SUMAR SI PAGO CON CREDI PUNTOS
                                if($o->tipo_pago == 0){
                                    $totalcobro = $totalcobro + $suma; 
                                }
                            }

                        }else if($tipo->tipo_cupon_id == 4){
                            $o->tipocupon = 4;
                            $producto = AplicaCuponCuatro::where('ordenes_id', $o->id)->pluck('producto')->first();
                           
                           
                            if($o->pago_a_propi == 1){
                           
                                if($o->tipo_pago == 0){
                                    $info = -$o->precio_total + ($o->precio_total + $o->precio_envio);
                                    $totalcobro = $totalcobro + $info;                                
                                    $o->precio_total = number_format((float)$o->precio_total + $o->precio_envio, 2, '.', '');
                                }else{
                                    // CREDI PUNTOS
                                    $totalcobro = $totalcobro - $o->precio_total;
    
                                    $o->precio_total = number_format((float)0, 2, '.', '');
                                }
                               
                            }else{
                                // no se le paga a servicio 
    
                                $cobro = $o->precio_total + $o->precio_envio;
                                $o->precio_total = number_format((float)$cobro, 2, '.', '');
                                // NO SUMAR SI PAGO CON CREDI PUNTOS
                                if($o->tipo_pago == 0){
                                    $totalcobro = $totalcobro + $cobro; 
                                }
                            }


                        }
                        else if($tipo->tipo_cupon_id == 5){
                            $donacion = AplicaCuponCinco::where('ordenes_id', $o->id)->pluck('dinero')->first();
                            
                            $total = $donacion + $o->precio_total;
                            
                            if($o->pago_a_propi == 1){
                           
                                if($o->tipo_pago == 0){
                                    $info = -$o->precio_total + ($total + $o->precio_envio);
                                    $totalcobro = $totalcobro + $info;                                
                                    $o->precio_total = number_format((float)$total + $o->precio_envio, 2, '.', '');
                                }else{
                                    // CREDI PUNTOS
                                    $totalcobro = $totalcobro - $o->precio_total;
    
                                    $o->precio_total = number_format((float)0, 2, '.', '');
                                }
                               
                            }else{
                                // no se le paga a servicio 
    
                                $cobro = $total + $o->precio_envio;
                                $o->precio_total = number_format((float)$cobro, 2, '.', '');
                                
                                if($o->tipo_pago == 0){
                                    $totalcobro = $totalcobro + $cobro; 
                                }
                            }
                          
                        }
                       

                    }else{              

                        if($o->pago_a_propi == 1){
                           
                            if($o->tipo_pago == 0){
                                $info = -$o->precio_total + ($o->precio_total + $o->precio_envio);
                                $totalcobro = $totalcobro + $info;                                
                                $o->precio_total = number_format((float)$o->precio_total + $o->precio_envio, 2, '.', '');
                            }else{
                                // CREDI PUNTOS
                                $totalcobro = $totalcobro - $o->precio_total;

                                $o->precio_total = number_format((float)0, 2, '.', '');
                            }
                           
                        }else{
                            // no se le paga a servicio 

                            $cobro = $o->precio_total + $o->precio_envio;
                            $o->precio_total = number_format((float)$cobro, 2, '.', '');
                            // NO SUMAR SI PAGO CON CREDI PUNTOS
                            if($o->tipo_pago == 0){
                                $totalcobro = $totalcobro + $cobro; 
                            }
                        }
                    }
                }
                
                // sumar ganancia de esta fecha
                $totalcobro = number_format((float)$totalcobro, 2, '.', '');

                
                return ['success' => 1, 'histoorden' => $orden, 'ganado' => $totalcobro];                             
            }else{
                return ['success' => 2];
            }
        }
    }

     // ver historial encargos
     public function verHistorialEncargos(Request $request){
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
 
            if(Revisador::where('id', $request->id)->first()){
                    
                $start = Carbon::parse($request->fecha1)->startOfDay(); 
                $end = Carbon::parse($request->fecha2)->endOfDay();

            
                $orden = DB::table('ordenes_encargo_revisadas AS r')
                ->join('ordenes_encargo AS o', 'o.id', '=', 'r.ordenes_encargo_id')
                ->select('o.id', 'o.precio_subtotal', 'o.estado_1', 'o.fecha_1',  
                'o.precio_envio', 'o.pago_a_propi', 'o.tipo_pago')
                ->where('r.revisador_id', $request->id)
                ->where('o.estado_1', 1) // propietario finalizo la preparacion
                ->whereBetween('r.fecha', [$start, $end]) // fecha cuando reviso
                ->orderBy('o.id', 'ASC')
                ->get();
 
                $totalcobro = 0;

                foreach($orden as $o){   

                    $motorista = "";

                    if($mm = MotoristaOrdenEncargo::where('ordenes_encargo_id', $o->id)->first()){
                        $motorista = Motoristas::where('id', $mm->motoristas_id)->pluck('nombre')->first();
                    }

                    $o->motorista = $motorista;

                    if($o->pago_a_propi == 1){

                           
                        if($o->tipo_pago == 0){
                            $info = -$o->precio_subtotal + ($o->precio_subtotal + $o->precio_envio);
                            $totalcobro = $totalcobro + $info;                                
                            $o->precio_total = number_format((float)$info, 2, '.', '');
                        }else{
                            // CREDI PUNTOS
                            $totalcobro = $totalcobro - $o->precio_subtotal;

                            $o->precio_total = number_format((float)0, 2, '.', '');
                        }
                       
                    }else{
                        // no se le paga a servicio 

                        $cobro = $o->precio_subtotal + $o->precio_envio;
                        $o->precio_total = number_format((float)$cobro, 2, '.', '');
                       
                        if($o->tipo_pago == 0){
                            $totalcobro = $totalcobro + $cobro; 
                        } else{
                            $totalcobro = $totalcobro + 0; 
                        }
                    }
                }
                 
                // sumar ganancia de esta fecha
                $totalcobro = number_format((float)$totalcobro, 2, '.', '');
               
                return ['success' => 1, 'histoorden' => $orden, 'ganado' => $totalcobro];                             
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

            if(Revisador::where('id', $request->id)->first()){
               
                Revisador::where('id', $request->id)->update([
                        'password' => bcrypt($request->password)
                    ]);

                        return ['success' => 1];
            
            }else{
                return ['success' => 2];  
            }
        }  
    }



     // ordene encargo pediente de pago
     public function pendienteEncargoPago(Request $request){
        if($request->isMethod('post')){ 
            $reglaDatos = array(
                'id' => 'required',
                'motoristaid' => 'required'
            );

            $mensajeDatos = array(                                      
                'id.required' => 'El id revisador es requerido.',
                'motoristaid.required' => 'El motorista id es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [ 
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            
            if(Revisador::where('id', $request->id)->first()){

                // estas ordenes ya fueron revisadas
                $noquiero = DB::table('ordenes_encargo_revisadas')->get();

                $pilaOrden = array();
                foreach($noquiero as $p){
                    array_push($pilaOrden, $p->ordenes_encargo_id);
                }

                $orden = DB::table('motorista_ordenes_encargo AS mo')
                ->join('ordenes_encargo AS o', 'o.id', '=', 'mo.ordenes_encargo_id')
                ->select('o.id', 'o.fecha_3', 'mo.motoristas_id', 'o.precio_subtotal', 
                'o.precio_envio', 'o.pago_a_propi', 'o.estado_3', 'o.tipo_pago')
                ->where('mo.motoristas_id', $request->motoristaid)               
                ->where('o.estado_2', 1) // ordenes que motorista inicio la entrega
                ->whereNotIn('o.id', $pilaOrden) // filtro para no ver ordenes encargo revisadas
                ->get(); 

                $totalcobro = 0;

                foreach($orden as $o){    
                    
                    $fecha3 = "";
                    if($o->estado_3 == 0){
                        $fecha3 = "Encargo no completado";
                    }else{
                       $fecha3 = date("h:i A d-m-Y", strtotime($o->fecha_3)); // fecha completo la orden 
                    }
                    $o->fecha_3 = $fecha3;
                        
                    $pagoa = "";

                    if($o->pago_a_propi == 1){

                        $pagoa = "Pago a propietario: $" . $o->precio_subtotal;
                           
                        if($o->tipo_pago == 0){
                            $info = -$o->precio_subtotal + ($o->precio_subtotal + $o->precio_envio);
                            $totalcobro = $totalcobro + $info;                                
                            $o->precio_total = number_format((float)$info, 2, '.', '');
                        }else{
                            // CREDI PUNTOS
                            $totalcobro = $totalcobro - $o->precio_subtotal;

                            $o->precio_total = number_format((float)0, 2, '.', '');
                        }
                       
                    }else{
                        // no se le paga a servicio 

                        $cobro = $o->precio_subtotal + $o->precio_envio;
                        $o->precio_total = number_format((float)$cobro, 2, '.', '');
                       
                        if($o->tipo_pago == 0){
                            $totalcobro = $totalcobro + $cobro; 
                        } else{
                            $totalcobro = $totalcobro + 0; 
                        }
                    }

                    $o->pagoa = $pagoa;

                }
                
                // sumar ganancia de esta fecha
                $totalcobro = number_format((float)$totalcobro, 2, '.', '');
                
                return ['success' => 1, 'orden' => $orden, 'debe' => $totalcobro];
            }
        }
    }

     // confirmar pago encargo
     public function confirmarPagoEncargo(Request $request){
        if($request->isMethod('post')){ 
            $reglaDatos = array(
                'id' => 'required',
                'ordenid' => 'required',
                'codigo' => 'required'
            );

            $mensajeDatos = array(                                      
                'id.required' => 'El id revisador es requerido.',
                'ordenid.required' => 'El id orden es requerido.',
                'codigo.required' => 'El codigo es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            } 

            if($r = Revisador::where('id', $request->id)->first()){
                
                // verificar si ya existe el registro                 
                if(OrdenesEncargoRevisadas::where('ordenes_encargo_id', $request->ordenid)->first()){
                    return ['success' => 1];
                }
                
                if($r->codigo == $request->codigo){

                    if($oo = OrdenesEncargo::where('id', $request->ordenid)->first()){
                        if($oo->estado_3 == 0){
                            // orden encargo aun no completada, no puede confirmar
                            return ['success' => 5];
                        }
                    }
                   
                    $fecha = Carbon::now('America/El_Salvador');

                    $nueva = new OrdenesEncargoRevisadas();
                    $nueva->ordenes_encargo_id = $request->ordenid;
                    $nueva->fecha = $fecha;
                    $nueva->revisador_id = $request->id;
                    
                    if($nueva->save()){
                        return ['success' => 2];
                    }else{
                        return ['success' => 3];
                    }
                }else{
                    return ['success' => 4]; // codigo incorrecto
                }
            }
        }
    }


}
