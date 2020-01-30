<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Revisador;
use App\Motoristas;
use App\RevisadorMotorista;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\RevisadorBitacora;

class RevisadorController extends Controller
{    
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
 
    //**REVISADORES  */ 

    public function index(){

        return view('backend.paginas.revisador.listarevisador');
    } 

    // tabla 
    public function revisadortabla(){ 
         
        $revisador = DB::table('revisador')
        ->select('id', 'nombre', 'telefono', 'activo', 'disponible', 'identificador')
        ->get();

        return view('backend.paginas.revisador.tablas.tablarevisador', compact('revisador'));
    } 

    // nuevo revisador
    public function nuevo(Request $request){
        if($request->isMethod('post')){

            $regla = array(
                'identi' => 'required',
                'nombre' => 'required',
                'direccion' => 'required',
                'telefono' => 'required',
                'latitud' => 'required',
                'longitud' => 'required',
                'codigo' => 'required',
                'cbactivo' => 'required'
            );

            $mensaje = array(
                'identi.required' => 'identificador es requerido',
                'nombre.required' => 'Nombre es requerido',
                'direccion.required' => 'Direccion es requerido',
                'telefono.required' => 'telefono es requerida',
                'latitud.required' => 'latitud es requerido',
                'longitud.required' => 'longitud es requerido',
                'codigo.required' => 'Codigo es requerido',
                'cbactivo.required' => 'Activo es requerido',
                );
                
            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }  

            if(Revisador::where('identificador', $request->identi)->first()){
                return ['success' => 1];
            }

            $fecha = Carbon::now('America/El_Salvador');

            $m = new Revisador();
            $m->nombre = $request->nombre;
            $m->direccion = $request->direccion;
            $m->telefono = $request->telefono;
            $m->latitud = $request->latitud;
            $m->longitud = $request->longitud;
            $m->password = bcrypt('12345678');
            $m->disponible = 0;
            $m->activo = $request->cbactivo;
            $m->codigo = $request->codigo;
            $m->fecha = $fecha;
            $m->identificador = $request->identi;

            if($m->save()){
                return ['success' => 2];
            }else{
                return ['success' => 3];
            }   
        }
    }

    // informacion del revisador
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
            
          if($p = Revisador::where('id', $request->id)->first()){

            return ['success' => 1, 'revisador' => $p]; 
          }else{
            return ['success' => 2];
          }
        }
    }

    // informacion de bitacora
    public function infobitacora(Request $request){
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
            
          if($p = RevisadorBitacora::where('id', $request->id)->first()){

            return ['success' => 1, 'bitacora' => $p]; 
          }else{
            return ['success' => 2];
          }
        }
    }
    
    // editar revisador
    public function editar(Request $request){
    
        if($request->isMethod('post')){   
            
            $regla = array(
                'id' => 'required',
                'nombre' => 'required',
                'direccion' => 'required',
                'telefono' => 'required',
                'latitud' => 'required',
                'longitud' => 'required',
                'codigo' => 'required',
                'cbactivo' => 'required',
                'cbdisponible' => 'required'
            );

            $mensaje = array(
                'id.required' => 'id es requerido',
                'nombre.required' => 'Nombre es requerido',
                'direccion.required' => 'Direccion es requerido',
                'telefono.required' => 'telefono es requerida',
                'latitud.required' => 'latitud es requerido',
                'longitud.required' => 'longitud es requerido',
                'codigo.required' => 'Codigo es requerido',
                'cbactivo.required' => 'Activo es requerido',
                'cbdisponible.required' => 'Disponible cb es requerido',
                );
                
            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }  


            if($po = Revisador::where('id', $request->id)->first()){
               
                Revisador::where('id', $request->id)->update([
                        'nombre' => $request->nombre,
                        'direccion' => $request->direccion,
                        'telefono' => $request->telefono,
                        'latitud' => $request->latitud,
                        'longitud' => $request->longitud,
                        'disponible' => $request->cbdisponible,
                        'activo' => $request->cbactivo,
                        'codigo' => $request->codigo,
                        ]);

                        return ['success' => 1];
            
            }else{
                return ['success' => 2]; // no encontrado el id
            }
        }  
    }

    //**REVISADORES ASIGNACION A MOTORISTA */
    // lista de revisadores motos
    public function index2(){

        $revisador = Revisador::all();
        $motorista = Motoristas::all();

        return view('backend.paginas.revisador.listarevisadormoto', compact('revisador', 'motorista'));
    } 

    // tabla 
    public function revisadormototabla(){ 
         
        $revisador = DB::table('revisador_motoristas AS rm')
        ->join('revisador AS r', 'r.id', '=', 'rm.revisador_id')
        ->join('motoristas AS m', 'm.id', '=', 'rm.motoristas_id')        
        ->select('rm.id', 'r.identificador', 'r.nombre', 'm.identificador AS identificadorMotorista', 'm.nombre AS nombreMotorista')
        ->get();

        return view('backend.paginas.revisador.tablas.tablarevisadormoto', compact('revisador'));
    } 
 
    // nuevo revisador motorista
    public function nuevomoto(Request $request){
        if($request->isMethod('post')){

            $regla = array(
                'revisador' => 'required',
                'motorista' => 'required',
            );

            $mensaje = array(
                'revisador.required' => 'revisador es requerido',
                'motorista.required' => 'motorista es requerido',
                );
                
            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }  

            if(RevisadorMotorista::where('revisador_id', $request->revisador)->where('motoristas_id', $request->motorista)->first()){
                return ['success' => 1];
            }

            $m = new RevisadorMotorista();
            $m->revisador_id = $request->revisador;
            $m->motoristas_id = $request->motorista;
            
            if($m->save()){
                return ['success' => 2];
            }else{
                return ['success' => 3];
            }   
        }
    }

    // borrar revisador de motorista
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

            RevisadorMotorista::where('id', $request->id)->delete();

            return ['success' => 1];
        }
    }

    //**REVISADORES  BITACORA*/

    // lista de revisadores bitacora
    public function index3(){

        $revisador = Revisador::all();

        return view('backend.paginas.revisador.listarevisadorbitacora', compact('revisador'));
    } 

    // tabla 
    public function revisadorbitacoratabla(){ 
         
        $revisador = DB::table('bitacora_revisador AS b')
        ->join('revisador AS r', 'r.id', '=', 'b.revisador_id')
        ->select('b.id', 'r.identificador', 'r.nombre', 'b.fecha1', 'b.fecha2', 'b.total', 'b.confirmadas')
        ->get();

        return view('backend.paginas.revisador.tablas.tablarevisadorbitacora', compact('revisador'));
    } 

    public function reseteo(Request $request){
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

            if(Revisador::where('id', $request->id)->first()){
               
                Revisador::where('id', $request->id)->update([
                        'password' => bcrypt('12345678')                  
                        ]);

                        return ['success' => 1];
            
            }else{
                return ['success' => 2];  
            }
        }  
    }

    // nueva bitacora
    public function nuevabitacora(Request $request){
        if($request->isMethod('post')){

            $regla = array(
                'revisador' => 'required',
                'fechadesde' => 'required',
                'fechahasta' => 'required',
                'total' => 'required',
                'confirmada' => 'required',
            );

            $mensaje = array(
                'revisador.required' => 'revisador es requerido',
                'fechadesde.required' => 'fecha desde es requerido',
                'fechahasta.required' => 'fecha hasta es requerido',
                'total.required' => 'total es requerido',
                'confirmada.required' => 'confirmada es requerido',
                );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }  
            
            $m = new RevisadorBitacora();
            $m->revisador_id = $request->revisador;
            $m->fecha1 = $request->fechadesde;
            $m->fecha2 = $request->fechahasta;
            $m->total = $request->total;
            $m->confirmadas = $request->confirmada;
            
            if($m->save()){
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }   
        }
    }

    // editar bitacora
    public function editarbitacora(Request $request){
    
        if($request->isMethod('post')){   
            
            $regla = array(
                'id' => 'required',
                'fechadesde' => 'required',
                'fechahasta' => 'required',
                'total' => 'required',
                'confirmada' => 'required',
                
            );

            $mensaje = array(
                'id.required' => 'id es requerido',
                'fechadesde.required' => 'fechadesde es requerido',
                'fechahasta.required' => 'fechahasta es requerido',
                'total.required' => 'total es requerido',
                'confirmada.required' => 'confirmada es requerido',
                
                );
                
            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }  


            if($po = RevisadorBitacora::where('id', $request->id)->first()){
               
                RevisadorBitacora::where('id', $request->id)->update([
                        'fecha1' => $request->fechadesde,
                        'fecha2' => $request->fechahasta,
                        'total' => $request->total,
                        'confirmadas' => $request->confirmada,
                        ]);

                        return ['success' => 1];
            
            }else{
                return ['success' => 2]; // no encontrado el id
            }
        }  
    }

}
