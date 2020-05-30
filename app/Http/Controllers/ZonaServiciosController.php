<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Servicios;
use App\Zonas;
use App\ZonasServicios;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\TipoServicios;

class ZonaServiciosController extends Controller
{
    public function __construct()
    { 
        $this->middleware('auth:admin');
    }

    // lista de zonas servicios
    public function index(){ 

        $zonas = Zonas::all();
        $servicios = DB::table('servicios AS s')
        ->select('s.id', 's.identificador')
        ->get();

        $serviciostipo = TipoServicios::all();

        return view('backend.paginas.zonaservicios.listazonaservicios', compact('zonas', 'servicios', 'serviciostipo'));
    }

    // tabla
    public function serviciotabla(){
        
        $servicio = DB::table('zonas_servicios AS zs')
        ->join('zonas AS z', 'z.id', '=', 'zs.zonas_id')
        ->join('servicios AS s', 's.id', '=', 'zs.servicios_id')
        ->select('zs.id', 'z.identificador', 's.identificador AS idenServicio', 
        's.nombre', 'zs.activo', 's.privado', 'zs.precio_envio', 
        'zs.ganancia_motorista', 'zs.zona_envio_gratis', 'zs.mitad_precio')
        ->orderBy('s.id', 'ASC')
        ->get();  
 
        return view('backend.paginas.zonaservicios.tablas.tablazonaservicios', compact('servicio'));
    } 
 
    // agregar zona servicio
    public function nuevo(Request $request){
        
        if($request->isMethod('post')){  

            $regla = array( 
                'selectzona' => 'required',
                'selectservicio' => 'required',
                'cbactivo' => 'required',
                'precioenvio' => 'required',
                'ganancia' => 'required',              
            ); 
 
            $mensaje = array(
                'selectzona.required' => 'Select zona es requerido',
                'selectservicio.required' => 'Select servicio es requerido',
                'cbactivo.required' => 'check activo es requerido',
                'precioenvio.required' => 'precio envio es requerido',
                'ganancia.required' => 'ganancia motorista es requerido',          
            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            } 

            // ya existe
            if(ZonasServicios::where('zonas_id', $request->selectzona)->where('servicios_id', $request->selectservicio)->first()){
                return ['success' => 1];
            }

            $fecha = Carbon::now('America/El_Salvador');

            $zona = new ZonasServicios();
            $zona->zonas_id = $request->selectzona;
            $zona->servicios_id = $request->selectservicio;
            $zona->fecha = $fecha;
            $zona->precio_envio = $request->precioenvio;
            $zona->activo = $request->cbactivo;
            $zona->ganancia_motorista = $request->ganancia;
            $zona->posicion = 1;
            $zona->zona_envio_gratis = 0;
            $zona->mitad_precio = 0;
            $zona->tiempo_limite = 0;
            $zona->horario_inicio = "07:00";
            $zona->horario_final = "21:00";
            $zona->costo_envio_gratis = 0;
            $zona->min_envio_gratis = 0;

            if($zona->save()){
                return ['success' => 2];
            }else{
                return ['success' => 3];
            }
        }
    } 

    // informacion
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
            
          if(ZonasServicios::where('id', $request->id)->first()){

            $zonaservicio = DB::table('zonas_servicios AS zs')
            ->join('zonas AS z', 'z.id', '=', 'zs.zonas_id')
            ->join('servicios AS s', 's.id', '=', 'zs.servicios_id')
            ->select('zs.id', 'z.identificador AS idenZona', 's.identificador AS idenServicio', 
             'zs.fecha',  'zs.activo', 'zs.precio_envio', 'zs.ganancia_motorista', 
             'zs.min_envio_gratis', 'zs.costo_envio_gratis', 'zs.zona_envio_gratis',
              's.privado', 'zs.mitad_precio', 'zs.nuevo_cargo')
            ->where('zs.id', $request->id)
            ->first();

            return ['success' => 1, 'zonaservicio' => $zonaservicio];
          }else{
              return ['success' => 2];
          }
        }
    } 

    // editar servicios
    public function editarServicio(Request $request){

        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'toggle' => 'required',
                'precioenvio' => 'required',
                'ganancia' => 'required',
                'cbmingratis' => 'required',
                'minenvio' => 'required',
                'nuevocargo' => 'required'
            );

            $messages = array(   
                'id.required' => 'El id es requerido',
                'toggle.required' => 'El toggle es requerido',
                'precioenvio.required' => 'El precio envio es requerido',
                'ganancia.required' => 'La ganancia es requerido',
                'cbmingratis.required' => 'check min para envio gratis es requerido',
                'minenvio.required' => 'costo minimo para envio gratis es requerido',
                'nuevocargo.required' => 'nuevo cargo es requerido'                               
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            } 

            if(ZonasServicios::where('id', $request->id)->first()){                        

                ZonasServicios::where('id', $request->id)->update([
                    'precio_envio' => $request->precioenvio,
                    'activo' => $request->toggle,
                    'ganancia_motorista' => $request->ganancia,
                    'min_envio_gratis' => $request->cbmingratis,
                    'costo_envio_gratis' => $request->minenvio,
                    'zona_envio_gratis' => $request->cbzonagratis,
                    'mitad_precio' => $request->cbmitadprecio,
                    'nuevo_cargo' => $request->nuevocargo
                    ]);
              
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }
  
    // filtrado
    public function filtrado($idzona, $idtipo){
        return view('backend.paginas.servicios.listafiltrado', compact('idzona', 'idtipo'));  
    }

    // seteara el campo de envio gratis, solo a servicios PUBLICOS 
    public function setearEnvioGratis(Request $request){
       // $myString = $request->idzonas;
       // $myArray = explode(',', $myString);
        if(is_array( $request->idzonas)){          

            // obtener todas las zonas servicios, con los servicios publicos
            $publicos = DB::table('servicios AS s')
            ->join('zonas_servicios AS z', 'z.servicios_id', '=', 's.id')       
            ->select('z.id')
            ->where('s.privado', 0) // unicamente servicios publicos
            ->whereIn('z.zonas_id', $request->idzonas)
            ->get();                       

           $pilaArray = array();
           foreach($publicos as $p){
               array_push($pilaArray, $p->id);
           }
   
           // recorrer cada uno para actualizar
           foreach($pilaArray as $p){
               ZonasServicios::where('id', $p)->update([               
                   'zona_envio_gratis' => $request->cbzonapublico              
               ]);
           }       
           
           return ['success' => 1];
        } 
       
        return ['success' => 2];
    }

      // seteara el campo de mitad de precio, solo a servicios PUBLICOS 
      public function setearMitadPrecio(Request $request){
         if(is_array( $request->idzonas)){            
            
            $publicos = DB::table('servicios AS s')
             ->join('zonas_servicios AS z', 'z.servicios_id', '=', 's.id')       
             ->select('z.id')
             ->where('s.privado', 0) // unicamente servicios publicos
             ->whereIn('z.zonas_id', $request->idzonas)
             ->get();   
 
            $pilaArray = array();
            foreach($publicos as $p){
                array_push($pilaArray, $p->id);
            }
    
            // recorrer cada uno para actualizar
            foreach($pilaArray as $p){
                ZonasServicios::where('id', $p)->update([               
                    'mitad_precio' => $request->cbzonapublico              
                ]);
            }       
            
            return ['success' => 1];
         } 
        
         return ['success' => 2];
     }    
 
    // tabla filtrado
    public function tablaFiltrado($idzona, $idtipo){

        $servicio = DB::table('servicios AS s')
        ->join('tipo_servicios AS ts', 'ts.id', '=', 's.tipo_servicios_id') 
        ->join('zonas_servicios AS z', 'z.servicios_id', '=', 's.id')         
        ->select('z.id','s.nombre', 's.descripcion', 's.imagen', 
        's.cerrado_emergencia', 'z.zonas_id', 'z.posicion', 's.tipo_servicios_id', 's.activo', 's.identificador', 'ts.nombre AS nombreServicio')
        ->where('z.zonas_id', $idzona)
        ->where('s.tipo_servicios_id', $idtipo)
        ->orderBy('z.posicion', 'ASC')
        ->get();  

        return view('backend.paginas.servicios.tablas.tablafiltrado', compact('servicio', 'idzona', 'idtipo'));
    }

    // ordenar producto
    public function ordenar(Request $request){

        // actualizar posicion por id de zona servicio y set posicion
        foreach ($request->order as $order) {    
            
            DB::table('zonas_servicios')
                ->where('id', $order['id'])
                ->update(['posicion' => $order['posicion']]);
        }

        return ['success' => 1];

        
        foreach ($tasks as $task) { 
            $id = $task->id;
    
            foreach ($request->order as $order) {              
                if ($order['id'] == $id) {


                    DB::table('zonas_servicios')
                    ->where('id', $id)
                    ->update(['posicion' => $order['posicion']]);
                }
            }
        }

        return ['success' => 1];
    }
}
