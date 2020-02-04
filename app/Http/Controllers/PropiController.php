<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Propietarios;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Servicios;

class PropiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
 
    public function index(){ 

        $servicios = Servicios::all();
        return view('backend.paginas.propietarios.listapropietario', compact('servicios'));
    } 

    // tabla  
    public function propitabla(){          
        $propi = DB::table('propietarios AS p')
        ->join('servicios AS s', 's.id', '=', 'p.servicios_id')
        ->select('p.id', 's.identificador', 'p.nombre AS nombrePropi', 'p.disponibilidad', 'p.fecha', 'p.activo', 'p.telefono')
        ->get();

        return view('backend.paginas.propietarios.tablas.tablapropietario', compact('propi'));
    }

    public function nuevo(Request $request){
        if($request->isMethod('post')){

            $regla = array( 
                'nombre' => 'required',
                'identificador' => 'required',
                'telefono' => 'required',
                'correo' => 'required',
                'dui' => 'required',
            );

            $mensaje = array(
                'nombre.required' => 'Nombre es requerido',
                'identificador.required' => 'Identificador es requerido',
                'telefono.required' => 'telefono es requerida',
                'correo.required' => 'correo inicio es requerido',
                'dui.required' => 'dui fin es requerida'
                );


            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            } 

            if(Propietarios::where('correo', $request->correo)->first()){
                return ['success' => 3];
            }

            if(Propietarios::where('telefono', $request->telefono)->first()){
                return ['success' => 4];
            } 

            $fecha = Carbon::now('America/El_Salvador');

            $p = new Propietarios();
            $p->nombre = $request->nombre;
            $p->telefono = $request->telefono;
            $p->password = bcrypt('12345678');
            $p->correo = $request->correo;
            $p->fecha = $fecha;
            $p->dui = $request->dui;
            $p->disponibilidad = 0;
            $p->device_id = "0000";
            $p->servicios_id = $request->identificador;
            $p->codigo_correo = "0000";
            $p->activo = 1; 
            if($p->save()){
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }

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

            
          if($p = Propietarios::where('id', $request->id)->first()){

            $servicios = Servicios::all();

            return ['success' => 1, 'servicios' => $servicios, 'propietario' => $p]; 
          }else{
              return ['success' => 2];
          }
        }
    }

    public function editar(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'identificador' => 'required',
                'telefono' => 'required',
                'correo' => 'required',
                'dui' => 'required',
                'activo' => 'required'            
            );

            $messages = array(   
                'id.required' => 'El id es requerido',
                'nombre.required' => 'Nombre es requerido',
                'identificador.required' => 'Identificador es requerido',
                'telefono.required' => 'telefono es requerida',
                'correo.required' => 'correo inicio es requerido',
                'dui.required' => 'dui fin es requerida',
                'activo.required' => 'activo es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if(Propietarios::where('id', $request->id)->first()){


                if(Propietarios::where('correo', $request->correo)->where('id', '!=', $request->id)->first()){
                    return [
                        'success' => 1 //correo ya registrado
                    ]; 
                }

                if(Propietarios::where('telefono', $request->telefono)->where('id', '!=', $request->id)->first()){
                    return ['success' => 3];
                } 
    
                Propietarios::where('id', $request->id)->update([
                    'nombre' => $request->nombre,
                    'telefono' => $request->telefono,
                    'correo' => $request->correo,
                    'dui' => $request->dui,
                    'servicios_id' => $request->identificador,
                    'activo' => $request->activo,
                    ]);
    
                return ['success' => 2];
            }else{
                return ['success' => 4];
            }
        }  
    }
}
