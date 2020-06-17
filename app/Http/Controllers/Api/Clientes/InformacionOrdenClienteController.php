<?php

namespace App\Http\Controllers\Api\Clientes;

use App\AdminOrdenes;
use App\HorarioServicio;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Propietarios;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Ordenes;
use App\OrdenesDescripcion;
use App\OrdenesDirecciones;
use App\PagoPropietario;
use App\Producto;
use App\Servicios;
use App\User;
use App\Zonas;
use App\ZonasServicios;
use Carbon\Carbon;
use DateTime;
use Exception; 
use App\OrdenesPendiente;
use App\OrdenesCupones;
use App\Cupones;
use App\AplicaCuponCuatro;
use App\AplicaCuponCinco;
use App\MotoristaOrdenes;
use App\Motoristas;
use App\ServiciosTipo;
use Log;

class InformacionOrdenClienteController extends Controller
{
    
    // ver ordenes por usuario
    public function verOrdenesInfo(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'userid' => 'required'               
            );
        
            $mensajeDatos = array(                                      
                'userid.required' => 'El id del usuario es requerido.'            
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  
                        
            if(User::where('id', $request->userid)->first()){
                $orden = DB::table('ordenes AS o')
                    ->join('servicios AS s', 's.id', '=', 'o.servicios_id')              
                    ->select('o.id', 's.nombre', 'o.precio_total',
                    'o.nota_orden', 'o.fecha_orden', 'o.precio_envio',
                    'o.estado_2', 'o.fecha_2',
                    'o.hora_2', 'o.estado_3', 'o.fecha_3', 'o.estado_4', 'o.fecha_4',
                    'o.estado_5', 'o.fecha_5', 'o.estado_6', 'o.fecha_6', 'o.estado_7',
                    'o.fecha_7', 'o.estado_8', 'o.fecha_8', 'o.mensaje_8')
                    ->where('o.users_id', $request->userid)
                    ->where('o.visible', 1)
                    ->get();

                    // CLIENTE MIRA EL TIEMPO DEL PROPIETARIO MAS COPIA DEL TIEMPO DE ZONA
                                
                foreach($orden as $o){   
                    
                    $tiempo = OrdenesDirecciones::where('ordenes_id', $o->id)->first();

                    $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));

                    $sumado = $tiempo->copia_tiempo_orden + $o->hora_2;
                    $o->hora_2 = $sumado;

                    // ver si fue cancelado desde panel de control
                    $o->canceladoextra = $tiempo->cancelado_extra;
                     
                    if($o->estado_2 == 1){ // propietario da el tiempo de espera                        
                        $o->fecha_2 = date("h:i A d-m-Y", strtotime($o->fecha_2));                    
                    }

                    if($o->estado_3 == 1){ 
                        $o->fecha_3 =date("h:i A d-m-Y", strtotime($o->fecha_3));  
                    }
                
                    if($o->estado_4 == 1){ // orden en preparacion
                        $time1 = Carbon::parse($o->fecha_4);
                        
                        // ya va sumado el tiempo extra de la zona, aqui arriba
                        $horaEstimada = $time1->addMinute($o->hora_2)->format('h:i A d-m-Y');
                        $o->horaEstimada = $horaEstimada;
                    }
                    
                    if($o->estado_5 == 1){                             
                        $o->fecha_5 = date("h:i A d-m-Y", strtotime($o->fecha_5));
                    }

                    if($o->estado_6 == 1){                     
                        $o->fecha_6 = date("h:i A d-m-Y", strtotime($o->fecha_6));
                    }

                    if($o->estado_7 == 1){
                        $o->fecha_7 = date("h:i A d-m-Y", strtotime($o->fecha_7));
                    }

                    if($o->estado_8 == 1){
                        $o->fecha_8 = date("h:i A d-m-Y", strtotime($o->fecha_8));
                    }




                    // buscar si aplico cupon
                    if($oc = OrdenesCupones::where('ordenes_id', $o->id)->first()){
                        $o->aplicacupon = 1;
                        // buscar tipo de cupon
                        $tipo = Cupones::where('id', $oc->cupones_id)->first();

                        // ver que tipo se aplico
                        // el precio envio ya esta modificado
                        if($tipo->tipo_cupon_id == 1){
                            $o->tipocupon = 1;

                        }else if($tipo->tipo_cupon_id == 2){
                            $o->tipocupon = 2;
                            // modificar precio
                            $descuento = AplicaCuponDos::where('ordenes_id', $o->id)->pluck('dinero')->first();

                            $total = $o->precio_total - $descuento;
                            if($total <= 0){
                                $total = 0;
                            }

                            // precio modificado con el descuento dinero
                            $o->precio_total = number_format((float)$total, 2, '.', '');

                        }else if($tipo->tipo_cupon_id == 3){
                            $o->tipocupon = 3;

                            $porcentaje = AplicaCuponTres::where('ordenes_id', $o->id)->pluck('porcentaje')->first();
                            $resta = $o->precio_total * ($porcentaje / 100);
                            $total = $o->precio_total - $resta;

                            if($total <= 0){
                                $total = 0;
                            }

                            $o->precio_total = number_format((float)$total, 2, '.', '');

                        }else if($tipo->tipo_cupon_id == 4){
                            $o->tipocupon = 4;
                            $producto = AplicaCuponCuatro::where('ordenes_id', $o->id)->pluck('producto')->first();

                            $o->producto = $producto;

                            // solo sumara sub total + envio
                            $total = $o->precio_total + $o->precio_envio;
                            $o->precio_total = number_format((float)$total, 2, '.', '');
                        }
                        else if($tipo->tipo_cupon_id == 5){
                            $o->tipocupon = 5;

                            // sumar sub total + envio + donacion
                            $acc = AplicaCuponCinco::where('ordenes_id', $o->id)->pluck('dinero')->first();

                            $total = $o->precio_total + $o->precio_envio;
                            $total = $total + $acc;
                            $o->precio_total = number_format((float)$total, 2, '.', '');
                        }
                        else{
                            // dado error, extrano
                            $o->tipocupon = 0;
                            
                            $total = $o->precio_total;
                            $envio = $o->precio_envio;
                            $total = $total + $envio;
                            $total = number_format((float)$total, 2, '.', '');
        
                            $o->precio_total = $total;
                        }

                    }else{
                        $o->aplicacupon = 0;

                        $total = $o->precio_total;
                        $envio = $o->precio_envio;
                        $total = $total + $envio;
                        $total = number_format((float)$total, 2, '.', '');
    
                        $o->precio_total = $total;
                    }
                    
                }

                return ['success' => 1, 'ordenes' => $orden];
            }else{
                return ['success' => 2];
            }            
        }
    }

    // ver ordenes por usuario
    public function verOrdenPorID(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'ordenid' => 'required'               
            );
        
            $mensajeDatos = array(                                      
                'ordenid.required' => 'El id del la orden es requerido.'            
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
            
                $orden = DB::table('ordenes')                   
                    ->select('id', 'fecha_orden', 'estado_2', 'fecha_2',
                    'hora_2', 'estado_3', 'fecha_3', 'estado_4', 'fecha_4',
                    'estado_5', 'fecha_5', 'estado_6', 'fecha_6', 'estado_7',
                    'fecha_7', 'estado_8', 'fecha_8', 'mensaje_8')
                    ->where('id', $request->ordenid)                
                    ->get();                
                      
                // CLIENTE MIRA EL TIEMPO DEL PROPIETARIO MAS COPIA DEL TIEMPO DE ZONA
                $tiempo = OrdenesDirecciones::where('ordenes_id', $request->ordenid)->first();

                // obtener fecha orden y sumarle tiempo si estado es igual a 2
                foreach($orden as $o){

                    $sumado = $tiempo->copia_tiempo_orden + $o->hora_2;
                    $o->hora_2 = $sumado;

                    // ver si fue cancelado desde panel de control
                    $o->canceladoextra = $tiempo->cancelado_extra;
                     
                    if($o->estado_2 == 1){ // propietario da el tiempo de espera                        
                        $o->fecha_2 = date("h:i A d-m-Y", strtotime($o->fecha_2));                    
                    }

                    if($o->estado_3 == 1){ 
                        $o->fecha_3 =date("h:i A d-m-Y", strtotime($o->fecha_3));  
                    }
                
                    if($o->estado_4 == 1){ // orden en preparacion
                        $time1 = Carbon::parse($o->fecha_4);
                        
                        // ya va sumado el tiempo extra de la zona, aqui arriba
                        $horaEstimada = $time1->addMinute($o->hora_2)->format('h:i A d-m-Y');
                        $o->horaEstimada = $horaEstimada;
                    }
                    
                    if($o->estado_5 == 1){                             
                        $o->fecha_5 = date("h:i A d-m-Y", strtotime($o->fecha_5));
                    }

                    if($o->estado_6 == 1){                     
                        $o->fecha_6 = date("h:i A d-m-Y", strtotime($o->fecha_6));
                    }

                    if($o->estado_7 == 1){
                        $o->fecha_7 = date("h:i A d-m-Y", strtotime($o->fecha_7));
                    }

                    if($o->estado_8 == 1){
                        $o->fecha_8 = date("h:i A d-m-Y", strtotime($o->fecha_8));
                    }

                    $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));
                }
            
                return ['success' => 1, 'ordenes' => $orden];
            }else{
                return ['success' => 2];
            }
        }
    }

}
