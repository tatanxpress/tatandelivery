<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TipoServiciosZona;
use App\Zonas;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\ZonasServicios;
use App\TipoServicios;

class TipoServicioZonaController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth:admin'); 
    }
 
    // lista de tipo servicios por zona
    public function index(){ 

        $identificador = DB::table('zonas')
        ->select('id', 'identificador')
        ->get();

        $tiposervicio = TipoServicios::all();

        return view('backend.paginas.tiposervicioszona.listatiposervicioszona', compact('identificador', 'tiposervicio'));
    }

    // tabla para ver servicios por zonas
    public function serviciotabla(){
        
        $tipo = DB::table('tipo_servicios_zonas AS tz')
        ->join('tipo_servicios AS ts', 'ts.id', '=', 'tz.tipo_servicios_id')          
        ->join('zonas AS z', 'z.id', '=', 'tz.zonas_id')
        ->select('tz.id', 'z.nombre', 'ts.descripcion', 'z.identificador', 'tz.activo', 'ts.nombre AS nombreServicio')
        ->get();

        return view('backend.paginas.tiposervicioszona.tablas.tablatiposerviciozona', compact('tipo'));
    } 


    // posiciones globales
    public function indexGlobal(){ 
        
        return view('backend.paginas.tiposervicioszona.listaglobal');
    }
 
    public function tablaGlobalTipos(){
           
       $tipos = TipoServicios::orderBy('nombre')->get();  

       foreach($tipos as $t){

        $contador = DB::table('tipo_servicios_zonas')
        ->where('tipo_servicios_id', $t->id)
        ->whereNotIn('zonas_id', [1,2]) // no quiero la zona de prueba, y la zona cero registro
        ->count();

        $t->cuantas = $contador;

        $activos = DB::table('tipo_servicios_zonas')
        ->where('tipo_servicios_id', $t->id)
        ->where('activo', 1)
        ->whereNotIn('zonas_id', [1,2]) // no quiero la zona de prueba, y la zona cero registro
        ->count();

        $t->activos = $activos;
       }

       return view('backend.paginas.tiposervicioszona.tablas.tablatiposervicioglobal', compact('tipos'));
    }

    // buscar servicio segun select
    public function buscarServicio(Request $request){
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

            $noquiero = DB::table('tipo_servicios_zonas')
                ->where('zonas_id', $request->id)  
                ->get();

            $pilaOrden = array();
                foreach($noquiero as $t){
                    if(!empty($t->tipo_servicios_id)){
                        array_push($pilaOrden, $t->tipo_servicios_id);
                    }
                }

            // obtener todos los servicios, menos los que ya tengo
            $tiposervicio = DB::table('tipo_servicios')
            ->whereNotIn('id', $pilaOrden)
            ->get();
            
            return ['success' => 1, 'tiposervicio'=>$tiposervicio];
        }
    }

    // nuevo tipo servicio zona
    public function nuevoTipoServicioZona(Request $request){

        if($request->isMethod('post')){  

            $regla = array( 
                'identificador' => 'required', // id zona
                'servicio' => 'required', //id tipo servicio
            ); 

            $mensaje = array(
                'identificador.required' => 'identificador es requerido',
                'servicio.required' => 'servicio es requerido'
                );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }
             
            if(TipoServiciosZona::where('tipo_servicios_id', $request->servicio)->where('zonas_id', $request->identificador)->first()){
                return ['success' => 1];
            }

            // aqui ira revuelto, todos los servicios de la misma zona, sin importar el tipo, se agregara hasta posicion ultima
            $conteo = TipoServiciosZona::where('zonas_id', $request->identificador)->count();
            $posicion = 1;

            if($conteo >= 1){
                // ya existe uno
                $registro = TipoServiciosZona::where('zonas_id', $request->identificador)->orderBy('id', 'DESC')->first();
                $posicion = $registro->posicion;
                $posicion++;
            } 

            $tipo = new TipoServiciosZona();
            $tipo->tipo_servicios_id = $request->servicio;
            $tipo->zonas_id = $request->identificador;
            $tipo->activo = 0;
            $tipo->posicion = $posicion;

            if($tipo->save()){
                return ['success' => 2];
            }else{
                return ['success' => 3];
            }
        }
    }

    // informacion tipo servicio zona
    public function informacionTipoZona(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'
            );    

            $messages = array(                                      
                'id.required' => 'El ID tipo servicio es requerido.'                        
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            if($tipo = TipoServiciosZona::where('id', $request->id)->first()){
                return['success' => 1, 'tipo' => $tipo];
            }else{
                return['success' => 2];
            }
        }
    }

    // editar tipo servicio zona
    public function editarTipo(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',               
                'toggle' => 'required',
            );    

            $messages = array(   
                'id.required' => 'El id es requerido.',                                   
                'toggle.required' => 'El toggle es requerido.',
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }
                        
            if(TipoServiciosZona::where('id', $request->id)->first()){                        
                TipoServiciosZona::where('id', $request->id)->update(['activo' => $request->toggle]);
                return ['success' => 1];
            }else{
                return ['success' => 2]; 
            }
        }
    }

    // filtro de posiciones
      // filtrado
    public function filtrado($idzona){

        return view('backend.paginas.tiposervicioszona.listafiltrado', compact('idzona'));  
    }  

    // tabla filtrado
    public function tablaFiltrado($idzona){

        $servicio = DB::table('tipo_servicios AS ts')
        ->join('tipo_servicios_zonas AS z', 'z.tipo_servicios_id', '=', 'ts.id') 
        ->select('z.id', 'ts.nombre', 'ts.descripcion')
        ->where('z.zonas_id', $idzona)
        ->orderBy('z.posicion', 'ASC')
        ->get();

        return view('backend.paginas.tiposervicioszona.tablas.tablalistafiltrado', compact('servicio', 'idzona'));
    }

    // ordenar producto
    public function ordenar(Request $request){

        $idzona = $request->idzona;

        // dame todos los servicios de ese tipo, un array
        $mismotipo = TipoServiciosZona::where('zonas_id', $idzona)->get();

        $pila = array();
        foreach($mismotipo as $p){
            array_push($pila, $p->id);
        }
       
        $tasks = DB::table('tipo_servicios_zonas')
        ->where('zonas_id', $idzona) 
        ->whereIn('id', $pila)
        ->get();
        
        
        foreach ($tasks as $task) { 
            $id = $task->id;
    
            foreach ($request->order as $order) {
                if ($order['id'] == $id) {
                   
                    DB::table('tipo_servicios_zonas')
                    ->where('id', $task->id)
                    ->update(['posicion' => $order['posicion']]);
                }
            }
        }

        return ['success' => 1];
    }


    // ordenar tipos de servicios para todas las zonas
    public function orderTipoServicioGlobalmente(Request $request){

        // recorrer cada tipo de servicio
        foreach ($request->order as $order) {

            $tipoid = $order['id'];

            DB::table('tipo_servicios_zonas')
            ->where('tipo_servicios_id', $tipoid) // restaurante por ejemplo
            ->update(['posicion' => $order['posicion']]); // actualizar posicion
        }           
        
        return ['success' => 1];
    }



    public function activarDesactivarTipoServicio(Request $request){
        
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'
            );    

            $messages = array(                                      
                'id.required' => 'El ID tipo servicio es requerido.'                        
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            } 
 
            TipoServiciosZona::where('tipo_servicios_id', $request->id)->update(['activo' => $request->estado]);

            return['success' => 1];
        }
    }


    // por zona servicio
    public function activarDesactivarZonaServicio(Request $request){
        
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'
            );    

            $messages = array(                                      
                'id.required' => 'El ID tipo servicio es requerido.'                        
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            } 
  
            ZonasServicios::where('servicios_id', $request->id)->update(['activo' => $request->estado]);

            return['success' => 1];
        }
    }


}
