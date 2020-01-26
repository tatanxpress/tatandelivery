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
                'licensia' => 'required',
                'dui' => 'required',
                'cbzona' => 'required',
            );

            $mensaje = array(
                'identi.required' => 'identificador es requerido',
                'nombre.required' => 'Nombre es requerido',
                'telefono.required' => 'telefono es requerida',
                'correo.required' => 'correo inicio es requerido',
                'tipovehiculo.required' => 'tipo vehiculo es requerido',
                'numerovehiculo.required' => 'numero vehiculo es requerido',
                'licensia.required' => 'licensia es requerido',
                'dui.required' => 'dui es requerido',
                'cbzona.required' => 'cbzona es requerido',
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
                $m->licensia = $request->licensia;
                $m->dui = $request->dui;
                $m->imagen = $nombreFoto;
                $m->device_id = "0000";
                $m->codigo_correo = "0000";
                $m->limite_dinero = 25.00;
                $m->zona_pago = $request->cbzona;

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
                'licensia' => 'required',
                'dui' => 'required',
                'cbzona' => 'required',
               
                'cbactivo' => 'required',
                'dinero' => 'required'
            );

            $mensaje = array(
                'id.required' => 'id es requerido',
                'nombre.required' => 'Nombre es requerido',
                'telefono.required' => 'telefono es requerida',
                'correo.required' => 'correo inicio es requerido',
                'tipovehiculo.required' => 'tipo vehiculo es requerido',
                'numerovehiculo.required' => 'numero vehiculo es requerido',
                'licensia.required' => 'licensia es requerido',
                'dui.required' => 'dui es requerido',
                'cbzona.required' => 'cbzona es requerido',
               
                'cbactivo.required' => 'cbactivo es requerido',
                'dinero.required' => 'Dinero es requerido'
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
                            'licensia' => $request->licensia,
                            'imagen' => $nombreFoto,
                            'dui' => $request->dui,
                            'zona_pago' => $request->cbzona,
                         
                            'activo' => $request->cbactivo,
                            ]);
                            if(Storage::disk('usuario')->exists($imagenOld)){
                                Storage::disk('usuario')->delete($imagenOld);                                
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
                        'licensia' => $request->licensia,
                        'dui' => $request->dui,
                        'zona_pago' => $request->cbzona,
                     
                        'activo' => $request->cbactivo,
                        'limite_dinero' => $request->dinero
                        ]);
 
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
        ->select('ms.id','s.identificador', 's.nombre', 'm.nombre AS nombreMotorista', 'm.identificador')
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
