<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\ServiciosTipo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; 

class CategoriasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    } 

    // lista de servicios tipo, que son categorias de cada servicio
    public function index($id){        
        return view('backend.paginas.servicios.listacategorias', compact('id'));
    } 

    // tabla lista categorias
    public function tablaCategorias($id){
        
        $servicio = DB::table('servicios_tipo AS st')
        ->join('servicios AS s', 's.id', '=', 'st.servicios_1_id')          
        ->select('st.id', 's.identificador', 'st.nombre', 'st.fecha', 'st.posicion', 'st.activo', 'st.activo_admin')
        ->where('st.servicios_1_id', $id)
        ->orderBy('st.posicion', 'ASC')
        ->get();

        return view('backend.paginas.servicios.tablas.tablacategorias', compact('servicio'));
    }

    // nueva categoria tipo servicio
    public function nuevo(Request $request){
        
        if($request->isMethod('post')){  
 
            $regla = array( 
                'id' => 'required', 
                'nombre' => 'required'            
            );
 
            $mensaje = array(
                'id.required' => 'id es requerido',
                'nombre.required' => 'nombre es requerido'          
            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }           

           // conteo de posicion
            $conteo = ServiciosTipo::count();
            $posicion = 1;

            if($conteo >= 1){
                // ya existe un slider, obtener ultima posicion
                $registro = ServiciosTipo::orderBy('id', 'DESC')->first();
                $posicion = $registro->posicion;
                $posicion++;
            }

            $fecha = Carbon::now('America/El_Salvador');

            $ca = new ServiciosTipo();
            $ca->nombre = $request->nombre;
            $ca->servicios_1_id = $request->id;
            $ca->posicion = $posicion;
            $ca->activo = 0;
            $ca->activo_admin = 0;
            $ca->fecha = $fecha;

            if($ca->save()){
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    } 

    // informacion de la categoria
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

            
          if(ServiciosTipo::where('id', $request->id)->first()){

            $categoria = DB::table('servicios_tipo AS st')
            ->join('servicios AS s', 's.id', '=', 'st.servicios_1_id')
            ->select('st.id', 'st.nombre', 'st.activo', 'st.activo_admin', 'st.fecha', 's.nombre AS nombreServicio')
            ->where('st.id', $request->id)
            ->first();

            return ['success' => 1, 'categoria' => $categoria];
          }else{
              return ['success' => 2];
          }
        }
    }
     
    // editar la categoria
    function editar(Request $request){
        
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'toggle' => 'required',
                'toggleadmin' => 'required',
                'nombre' => 'required',
            );

            $messages = array(   
                'id.required' => 'El id es requerido',
                'toggle.required' => 'El toggle es requerido',
                'toggleadmin.required' => 'El toggle admin es requerido',
                'nombre.required' => 'El nombre es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if(ServiciosTipo::where('id', $request->id)->first()){
             
                    ServiciosTipo::where('id', $request->id)->update([
                      
                        'nombre' => $request->nombre,
                        'activo' => $request->toggle,
                        'activo_admin' => $request->toggleadmin
                        ]);
              
                return ['success' => 1];
            }else{
                return ['success' => 2]; 
            }
        }         
    }

    // ordenar posiciones
    public function ordenar(Request $request){

        $tasks = ServiciosTipo::all();
    
        foreach ($tasks as $task) {
            $id = $task->id;
    
            foreach ($request->order as $order) {
                if ($order['id'] == $id) {
                    $task->update(['posicion' => $order['posicion']]);
                }
            }
        }
        return ['success' => 1];
    }
}
