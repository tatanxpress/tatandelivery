<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Motoristas;
use App\Servicios;
use App\MotoristaExperiencia;
use App\MotoristasAsignados;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MotoristaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    } 
 
    // lista de motoristas
    public function index(){
        return view('backend.paginas.motoristas.listamotorista');
    }  
 
    // tabla 
    public function mototabla(){
         
        $moto = DB::table('motoristas')
        ->select('id', 'nombre', 'telefono', 'correo', 'activo', 'disponible', 'identificador')
        ->get();

        return view('backend.paginas.motoristas.tablas.tablamotorista', compact('moto'));
    } 
 
    // nuevo motorista
    public function nuevo(Request $request){
        if($request->isMethod('post')){

            $regla = array(
                'identi' => 'required',
                'nombre' => 'required',
                'telefono' => 'required',
                'correo' => 'required',
                'tipovehiculo' => 'required',
                'numerovehiculo' => 'required',
                'limite' => 'required',
                'privado' => 'required'
            );

            $mensaje = array(
                'identi.required' => 'identificador es requerido',
                'nombre.required' => 'Nombre es requerido',
                'telefono.required' => 'telefono es requerida',
                'correo.required' => 'correo inicio es requerido',
                'tipovehiculo.required' => 'tipo vehiculo es requerido',
                'numerovehiculo.required' => 'numero vehiculo es requerido',
                'limite.required' => 'limite dinero es requerido',
                'privado.required' => 'privado es requerido'
            );

                
            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }  

            if(Motoristas::where('identificador', $request->identi)->first()){
                return ['success' => 4];
            }

            if(Motoristas::where('correo', $request->correo)->first()){
                return ['success' => 1];
            }

            if(Motoristas::where('telefono', $request->telefono)->first()){
                return ['success' => 5];  
            }

            $cadena = Str::random(15);
            $tiempo = microtime();
            $union = $cadena.$tiempo;
            $nombre = str_replace(' ', '_', $union);
            
            $extension = '.'.$request->imagen->getClientOriginalExtension();
            $nombreFoto = $nombre.strtolower($extension);
            $avatar = $request->file('imagen'); 
            $upload = Storage::disk('usuario')->put($nombreFoto, \File::get($avatar));
            
            if($upload){

                $fecha = Carbon::now('America/El_Salvador');

                $m = new Motoristas();
                $m->nombre = $request->nombre;
                $m->telefono = $request->telefono;
                $m->correo = $request->correo;
                $m->password = bcrypt('12345678');
                $m->tipo_vehiculo = $request->tipovehiculo;
                $m->numero_vehiculo = $request->numerovehiculo;
                $m->activo = 1;
                $m->identificador = $request->identi;
                $m->disponible = 0;
                $m->fecha = $fecha;
                $m->imagen = $nombreFoto;
                $m->device_id = "0000";
                $m->codigo_correo = "0000";
                $m->limite_dinero = $request->limite;
                $m->privado = $request->privado;

                if($m->save()){
                    return ['success' => 2];
                }else{
                    return ['success' => 3];
                }                    
            }
        }
    } 

    // informacion del motorista
    public function informacion(Request $request){
        if($request->isMethod('post')){  

            $regla = array( 
                'id' => 'required', 
            );
 
            $mensaje = array(
                'id.required' => 'id es requerido',                      
            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }
            
          if($p = Motoristas::where('id', $request->id)->first()){

            return ['success' => 1, 'motorista' => $p]; 
          }else{
            return ['success' => 2];
          }
        }
    }

    // editar motorista
    public function editar(Request $request){
        if($request->isMethod('post')){   
            
            $regla = array(  
                'id' => 'required',
                'nombre' => 'required',
                'telefono' => 'required',
                'correo' => 'required',
                'tipovehiculo' => 'required',
                'numerovehiculo' => 'required',               
                'cbactivo' => 'required',
                'dinero' => 'required',
                'privado' => 'required'
            );
 
            $mensaje = array(
                'id.required' => 'id es requerido',
                'nombre.required' => 'Nombre es requerido',
                'telefono.required' => 'telefono es requerida',
                'correo.required' => 'correo inicio es requerido',
                'tipovehiculo.required' => 'tipo vehiculo es requerido',
                'numerovehiculo.required' => 'numero vehiculo es requerido',  
                'cbactivo.required' => 'cbactivo es requerido',
                'dinero.required' => 'Dinero es requerido',
                'privado.required' => 'Privado es requerido'
                );

                $validar = Validator::make($request->all(), $regla, $mensaje );

                if ($validar->fails()) 
                {
                    return [
                        'success' => 0, 
                        'message' => $validar->errors()->all()
                    ];
                } 

            if($po = Motoristas::where('id', $request->id)->first()){
                
                if(Motoristas::where('correo', $request->correo)->where('id', '!=', $request->id)->first()){
                    return ['success' => 1];  
                }

                if(Motoristas::where('telefono', $request->telefono)->where('id', '!=', $request->id)->first()){
                    return ['success' => 5];  
                }

                if(Motoristas::where('identificador', $request->identificador)->where('id', '!=', $request->id)->first()){
                    return ['success' => 6];  
                }
                

                if($request->hasFile('imagen')){

                    $cadena = Str::random(15);
                    $tiempo = microtime(); 
                    $union = $cadena.$tiempo;
                    $nombre = str_replace(' ', '_', $union);
                    
                    $extension = '.'.$request->imagen->getClientOriginalExtension();
                    $nombreFoto = $nombre.strtolower($extension);
                    $avatar = $request->file('imagen'); 
                    $upload = Storage::disk('usuario')->put($nombreFoto, \File::get($avatar));
     
                    if($upload){
                        $imagenOld = $po->imagen;
                        Motoristas::where('id', $request->id)->update([
                            'nombre' => $request->nombre,
                            'telefono' => $request->telefono,
                            'correo' => $request->correo,
                            'tipo_vehiculo' => $request->tipovehiculo,
                            'numero_vehiculo' => $request->numerovehiculo,
                            'imagen' => $nombreFoto,                         
                            'activo' => $request->cbactivo,
                            'privado' =>$request->privado,
                            'identificador' => $request->identificador
                            ]);
                            if(Storage::disk('usuario')->exists($imagenOld)){
                                Storage::disk('usuario')->delete($imagenOld);                                
                            } 

                            // actualizar su password
                            if($request->checkpassword == 1){
                                Motoristas::where('id', $request->id)->update([
                                    'password' => bcrypt('12345678')                  
                                    ]);
                            }



                            return ['success' => 2];
                    }else{
                        return ['success' => 3];
                    }
    
                }else{

                    Motoristas::where('id', $request->id)->update([
                        'nombre' => $request->nombre,
                        'telefono' => $request->telefono,
                        'correo' => $request->correo,
                        'tipo_vehiculo' => $request->tipovehiculo,
                        'numero_vehiculo' => $request->numerovehiculo, 
                        'activo' => $request->cbactivo,
                        'limite_dinero' => $request->dinero,
                        'privado' =>$request->privado,
                        'identificador' => $request->identificador
                        ]);

                        // actualizar password
                        if($request->checkpassword == 1){
                            Motoristas::where('id', $request->id)->update([
                                'password' => bcrypt('12345678')                  
                                ]);
                        }
 
                        return ['success' => 2];
                }    
                 
            }else{
                return ['success' => 4]; // no encontrado el id
            }
        }  
    }
 

    //** motoristas asignados */

    public function index2(){

        $servicios = Servicios::all();
        $motoristas = Motoristas::all();

        return view('backend.paginas.motoristas.listamotoristaservicio', compact('servicios', 'motoristas'));
    } 

    // tabla motorista asignados
    public function motoserviciotabla(){ 

        $moto = DB::table('motoristas_asignados AS ms')
        ->join('servicios AS s', 's.id', '=', 'ms.servicios_id')
        ->join('motoristas AS m', 'm.id', '=', 'ms.motoristas_id')
        ->select('ms.id','s.identificador AS identi', 's.nombre', 'm.nombre AS nombreMotorista', 'm.identificador')
        ->get();
       
        return view('backend.paginas.motoristas.tablas.tablamotoristaservicio', compact('moto'));
    } 

    // borrar motorista asignado
    public function borrar(Request $request){
        if($request->isMethod('post')){

            $regla = array( 
                'id' => 'required',
            );

            $mensaje = array(
                'id.required' => 'id es requerido',
                );
 

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0,
                    'message' => $validar->errors()->all()
                ];
            }

            MotoristasAsignados::where('id', $request->id)->delete();

            return ['success' => 1];
        }
    }

    // borrar TODAS LAS ASIGNACIONES
    public function borrarTodo(Request $request){
        if($request->isMethod('post')){           
            MotoristasAsignados::truncate();
            return ['success' => 1];
        }
    }

    // agregar motorista a servicio
    public function nuevomotoservicio(Request $request){
        if($request->isMethod('post')){

            $regla = array(
                'motorista' => 'required',
                'servicio' => 'required',
            );

            $mensaje = array(
                'motorista.required' => 'identificador es requerido',
                'servicio.required' => 'Nombre es requerido',
                );

                
            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }  

            if(MotoristasAsignados::where('servicios_id', $request->servicio)->where('motoristas_id', $request->motorista)->first()){
                return ['success' => 1];
            }

            $m = new MotoristasAsignados();
            $m->servicios_id = $request->servicio;
            $m->motoristas_id = $request->motorista;
            if($m->save()){
                return ['success' => 2];
            }else{
                return ['success' => 3];
            }
        }
    }

     // agregar motorista global
     public function nuevoGlobal(Request $request){
        if($request->isMethod('post')){

            $regla = array(
                'motorista' => 'required'
            );

            $mensaje = array(
                'motorista.required' => 'identificador es requerido'
                );

                
            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }  
           
            // obener todos los servicios
            $servicios = Servicios::all();
            foreach($servicios as $s){

                if(MotoristasAsignados::where('motoristas_id', $request->motorista)
                ->where('servicios_id', $s->id)->first()){
                    // ya existe el registro
                }else{
                    // guardar registro
                    $m = new MotoristasAsignados();
                    $m->servicios_id = $s->id;
                    $m->motoristas_id = $request->motorista;
                    $m->save();
                }
            }
            
            // Completado
            return ['success' => 1];           
        }
    }
    
    // sacar promedio completo
    public function promedio(Request $request){
        if($request->isMethod('post')){  

            $regla = array( 
                'id' => 'required', 
            );
 
            $mensaje = array(
                'id.required' => 'id es requerido',                      
            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }
            
          if($p = Motoristas::where('id', $request->id)->first()){

            $datos = MotoristaExperiencia::where('motoristas_id', $request->id)->get();

            $conteo = MotoristaExperiencia::where('motoristas_id', $request->id)->count();

            if($conteo > 0){

                $sumado=0;
                foreach ($datos as $valor){
    
                    $num = $valor->experiencia;
                    $sumado=$sumado+$num;
                }

                $resultado = $sumado / $conteo; 
    
                return ['success' => 1, 'promedio' => $resultado]; 
            }else{
                return ['success' => 2];
            }

            return ['success' => 1, 'motorista' => $p]; 
          }else{
            return ['success' => 2];
          }
        }
    }

}
