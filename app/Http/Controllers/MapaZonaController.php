<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Zonas;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Poligono;

class MapaZonaController extends Controller
{
    // controlador protegido
    public function __construct()
    {
        $this->middleware('auth:admin'); 
    }

    // lista de zonas
    public function index(){
        return view('backend.paginas.zonas.listazonas');
    }

    // tabla para ver zonas
    public function zonatabla(){
        $zonas = Zonas::all();

        foreach($zonas as $z){
            $z->hora_abierto_delivery = date("h:i A", strtotime($z->hora_abierto_delivery));
            $z->hora_cerrado_delivery = date("h:i A", strtotime($z->hora_cerrado_delivery));
        }

        return view('backend.paginas.zonas.tablas.tablazona', compact('zonas'));
    }

    // crear zona
    public function nuevaZona(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'nombre' => 'required',
                'descripcion' => 'required',
                'horaabierto' => 'required',
                'horacerrado' => 'required',               
                'identificador' => 'required'
            );    

            $messages = array(                                      
                'nombre.required' => 'El nombre es requerido.',
                'descripcion.required' => 'la descripcion es requerido.',
                'horaabierto.required' => 'El horario abierto requerido.',
                'horacerrado.required' => 'El horario cerrado requerido.',               
                'identificador.required' => 'Identificador requerido.',
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            $identificador = str_replace(' ', '-', $request->identificador);

            if(Zonas::where('identificador', $identificador)->first()){
                return ['success'=> 3];
            }

            $fecha = Carbon::now('America/El_Salvador');

            $zona = new Zonas();
            $zona->nombre = $request->nombre;
            $zona->descripcion = $request->descripcion;
            $zona->identificador = $identificador;
            $zona->latitud = "1";
            $zona->longitud = "1";
            $zona->saturacion = 0;
            $zona->hora_abierto_delivery = $request->horaabierto;
            $zona->hora_cerrado_delivery = $request->horacerrado;
            $zona->fecha = $fecha;            
            $zona->activo = 1;

            if($zona->save()){
                return ['success'=>1];
            }else{
                return ['success'=>2];
            }
        }
    }

    // informacion de la zona
    public function informacionZona(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'
            );    

            $messages = array(                                      
                'id.required' => 'El ID zona es requerido.'                        
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            if($zona = Zonas::where('id', $request->id)->first()){
                return['success' => 1, 'zona' => $zona];
            }else{
                return['success' => 2];
            }
        }
    }

    // editar la zona
    public function editarZona(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',               
                'nombre' => 'required',
                'descripcion' => 'required',
                'horaabierto' => 'required',
                'horacerrado' => 'required',
                
                'togglep' => 'required',
                'togglea' => 'required',
                'identificador' => 'required'
            );    

            $messages = array(   
                'id.required' => 'El id es requerido.',                                   
                'nombre.required' => 'El nombre es requerido.',
                'descripcion.required' => 'la descripcion es requerido.',
                'horaabierto.required' => 'El horario abierto requerido.',
                'horacerrado.required' => 'El horario cerrado requerido.',
               
                'togglep.required' => 'El valor toggle saturacion requerido.',  
                'togglea.required' => 'El valor toggle activo requerido.',
                'identificador.required' => 'El identificador es requerido.',
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            $identificador = str_replace(' ', '-', $request->identificador);

            if(Zonas::where('identificador', $identificador)->where('id', '!=', $request->id) ->first()){
                return ['success'=> 3];
            }

            if(Zonas::where('id', $request->id)->first()){  
                
                Zonas::where('id', $request->id)->update(['nombre' => $request->nombre,
                'descripcion'=> $request->descripcion, 'hora_abierto_delivery' => $request->horaabierto, 
                'hora_cerrado_delivery' => $request->horacerrado, 'identificador' => $identificador, 'saturacion' => $request->togglep, 'activo' => $request->togglea]);
                
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }

    // agregar poligono a una zona
    public function crearPoligono(Request $request){
        if($request->isMethod('post')){    
            $rules = array(                
                'id' => 'required',
                'latitud' => 'required',
                'longitud' => 'required'
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.',
                'latitud.required' => 'Latitud es requerido.',
                'longitud.required' => 'Longitud requerido.'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if($validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            if(Zonas::where('id', $request->id)->first()){

                $pol = new Poligono();
                $pol->latitud = $request->latitud;
                $pol->longitud = $request->longitud;
                $pol->zonas_id = $request->id;

                if($pol->save()){
                    return ['success'=> 1];
                }else{
                    return ['success'=> 2];
                }
            }else{
                return ['success' => 3];
            }
        }
    }

    // borrar poligono de una zona
    public function borrarPoligono(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'                
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if($validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            if(Zonas::where('id', $request->id)->first()){

                Poligono::where('zonas_id', $request->id)->delete();

                return ['success'=> 1];
            }else{
                return ['success' => 2];
            }
        }
    }

    // ver mapa poligono
    function verMapa($id){

        $poligono = Poligono::where('zonas_id', $id)->get();

        return view('backend.paginas.zonas.mapapoligono', compact('poligono'));
    }
}
