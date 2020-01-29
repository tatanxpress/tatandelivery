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
                $noquiero = DB::table('ordenes_revisadas AS r')                
                ->get();

                $pilaOrden = array();
                foreach($noquiero as $p){
                    array_push($pilaOrden, $p->ordenes_id);
                }

                $orden = DB::table('motorista_ordenes AS mo')
                ->join('ordenes AS o', 'o.id', '=', 'mo.ordenes_id')
                ->select('o.id', 'mo.motoristas_id', 'o.precio_total', 'o.fecha_5', 
                'o.servicios_id', 'o.estado_8')
                ->where('mo.motoristas_id', $request->motoristaid)
                ->where('mo.motorista_prestado', 0)
                ->where('o.estado_8', 0)
                ->whereNotIn('o.id', $pilaOrden)
                ->get();

                foreach($orden as $o){
                    $fechaOrden = $o->fecha_5;
                    $hora = date("h:i A", strtotime($fechaOrden));
                    $fecha = date("d-m-Y", strtotime($fechaOrden));
                    $o->fecha_orden = $hora . " " . $fecha;                  
                }

                // sumar ganancia de esta fecha
                $suma = collect($orden)->sum('precio_total');
                $debe = number_format((float)$suma, 2, '.', '');
                return ['success' => 1, 'orden' => $orden, 'debe' => $debe];
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

            if(Revisador::where('id', $request->id)->first()){
                
                // buscar con la fecha dada. MINIMO PODRAN VER DE HOY MENOS X DIAS
                $encontro = DB::table('ordenes_revisadas AS r')
                ->join('ordenes AS o', 'o.id', '=', 'r.ordenes_id')
                ->select('o.id', 'o.precio_total', 'r.fecha')
                ->where('r.revisador_id', $request->id)
                ->whereDate('r.fecha', '>', Carbon::now('America/El_Salvador')->subDays(3))
                ->get();

                    // encontro un historial en el tiempo permitido. filtrar por la fecha dada
                    if(count($encontro) > 0){

                    $start = Carbon::parse($request->fecha1)->startOfDay(); 
                    $end = Carbon::parse($request->fecha2)->endOfDay();
                
                    $orden = DB::table('ordenes_revisadas AS r')
                    ->join('ordenes AS o', 'o.id', '=', 'r.ordenes_id')
                    ->select('o.id', 'o.precio_total', 'r.fecha', 'o.precio_envio')
                    ->where('r.revisador_id', $request->id)
                    ->whereBetween('r.fecha', [$start, $end])
                    ->orderBy('o.id', 'ASC')
                    ->get();

                    $total = 0.0;

                    foreach($orden as $o){
                        $fechaOrden = $o->fecha;
                        $hora = date("h:i A", strtotime($fechaOrden));
                        $fecha = date("d-m-Y", strtotime($fechaOrden));
                        $o->fecha = $hora . " " . $fecha;
                        
                        // nombre motorista
                        $idm = DB::table('motorista_ordenes AS m')                        
                        ->where('m.ordenes_id', $o->id)
                        ->pluck('motoristas_id')                 
                        ->first();

                        $sumado = $o->precio_total + $o->precio_envio;
                        $t = number_format((float)$sumado, 2, '.', '');

                        $o->precio_total = $t;

                        $nombre = Motoristas::where('id', $idm)->pluck('nombre')->first();
                        $o->motorista = $nombre;
                      
                        $total = $total + $sumado;
                    }

                    // sumar ganancia de esta fecha
                
                    $ganado = number_format((float)$total, 2, '.', '');
                    return ['success' => 1, 'histoorden' => $orden, 'ganado' => $ganado];
                }else{
                    return ['success' => 1, 'histoorden' => [], 'ganado' => "0.00"];
                }                
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


}
