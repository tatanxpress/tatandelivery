<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ZonaPublicidad;
use App\Zonas;
use App\Publicidad;
use App\Servicios;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\RegistroPromo;

class ZonaPublicidadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    } 

    // lista de zona de publicidad
    public function index(){     
        
        $zonas = Zonas::all();
        $publicidad = Publicidad::where('activo', 1)->get();

        return view('backend.paginas.zonaspublicidad.listazonapublicidad', compact('zonas', 'publicidad'));
    } 

    // tabla 
    public function tablazona(){

        $publicaciones = DB::table('zonas_publicidad AS zp')
        ->join('zonas AS z', 'z.id', '=', 'zp.zonas_id')  
        ->join('publicidad AS p', 'p.id', '=', 'zp.publicidad_id')          
        ->select('zp.id', 'z.identificador', 'p.identificador AS identiPubli', 'zp.fecha')
        ->orderBy('zp.posicion', 'ASC')
        ->get();
 
        return view('backend.paginas.zonaspublicidad.tablas.tablazonapublicidad', compact('publicaciones'));
    } 

     // nueva publicacion por zona
     public function nuevo(Request $request){

        if($request->isMethod('post')){

            $regla = array( 
                'idzona' => 'required',
                'idpubli' => 'required',
            );

            $mensaje = array(
                'idzona.required' => 'id zona es requerido',
                'idpubli.required' => 'id publicacion es requerido',               
                );


            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            } 

            if(ZonaPublicidad::where('zonas_id', $request->idzona)->where('publicidad_id', $request->idpubli)->first()){
                return ['success' => 1];
            }

            $conteo = ZonaPublicidad::where('zonas_id', $request->idzona)->where('publicidad_id', $request->idpubli)->count();
            $posicion = 1;

            if($conteo >= 1){
                $registro = ZonaPublicidad::where('zonas_id', $request->idzona)->where('publicidad_id', $request->idpubli)->orderBy('id', 'DESC')->first();
                $posicion = $registro->posicion;
                $posicion++;
            }

            $fecha = Carbon::now('America/El_Salvador');

            $p = new ZonaPublicidad();
            $p->zonas_id = $request->idzona;
            $p->publicidad_id = $request->idpubli;
            $p->posicion = $posicion;
            $p->fecha = $fecha;
            if($p->save()){
                return ['success' => 2];
            }else{
                return ['success' => 3];
            }
     
        }
    }

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

            ZonaPublicidad::where('id', $request->id)->delete();

            return ['success' => 1];
        }
    }

    // filtrado
    public function filtrado($idzona){

        $identificador = Zonas::where('id', $idzona)->pluck('identificador')->first();
        return view('backend.paginas.zonaspublicidad.listafiltrado', compact('idzona', 'identificador'));  
    }

    // tabla filtrado
    public function tablaFiltrado($idzona){

        $publicacion = DB::table('zonas_publicidad AS z')
        ->join('publicidad AS p', 'p.id', '=', 'z.publicidad_id') 
        ->select('z.id', 'p.nombre', 'p.identificador', 'z.fecha', 'z.posicion')
        ->where('z.zonas_id', $idzona)
        ->orderBy('z.posicion', 'ASC')
        ->get();

        return view('backend.paginas.zonaspublicidad.tablas.tablafiltrado', compact('publicacion', 'idzona'));
    } 

    public function ordenar(Request $request){

        $idzona = $request->idzona; 

        $tasks = ZonaPublicidad::where('zonas_id', $idzona)->get();
    
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


    // lista de registro de pago de publicidad o promocion
    public function index2(){
        
        $servicios = Servicios::all();
        return view('backend.paginas.publicidad.listaregistropromo', compact('servicios'));
    } 
 
    // tabla 
    public function tablaregistropromo(){
 
        $registro = DB::table('registro_promo AS r')
        ->join('servicios AS s', 's.id', '=', 'r.servicios_id')  
        ->select('r.id', 's.identificador', 'r.fecha1', 'r.fecha2', 'r.fecha', 'r.tipo', 'r.pago')
        ->orderBy('r.id', 'DESC')
        ->get(); 
 
        return view('backend.paginas.publicidad.tablas.tablaregistropromo', compact('registro'));
    }


     // nuevo registro de pago
     public function nuevoregistro(Request $request){       
        if($request->isMethod('post')){

            $regla = array( 
                'idservicio' => 'required',
                'tipo' => 'required',
                'fecha1' => 'required',
                'fecha2' => 'required',
                'pago' => 'required',
            );
  
            $mensaje = array(
                'idservicio.required' => 'id servicio es requerido',
                'tipo.required' => 'tipo promo es requerido',  
                'fecha1.required' => 'fecha 1 es requerido',  
                'fecha2.required' => 'fecha 2 es requerido',  
                'pago.required' => 'pago es requerido',               
                );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            } 

            $des = "";
            if($request->descripcion != null){
                $des = $request->descripcion;
            }
            $fecha = Carbon::now('America/El_Salvador');
 
            $p = new RegistroPromo();
            $p->servicios_id = $request->idservicio;
            $p->fecha1 = $request->fecha1;
            $p->fecha2 = $request->fecha2;
            $p->fecha = $fecha;
            $p->tipo = $request->tipo;
            $p->pago = $request->pago;
            $p->descripcion = $des;

            if($p->save()){
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }     
        }
    }

    // reporte para saver registros de promocion o publicidad
    public function reporte($idservicio, $idpromo){
      
        $registro = DB::table('registro_promo AS r')
        ->join('servicios AS s', 's.id', '=', 'r.servicios_id')      
        ->select('r.fecha1', 'r.fecha2', 'r.tipo', 'r.pago')
        ->where('r.tipo', $idpromo)
        ->where('s.id', $idservicio)
        ->orderBy('r.fecha2', 'ASC')
        ->get(); 

        $total = 0;
        $dinero = 0; 
        foreach($registro as $o){

            // cambiar formato fechas
            $fecha1 = date("d-m-Y", strtotime($o->fecha1));
            $fecha2 = date("d-m-Y", strtotime($o->fecha2));
            $o->fecha1 = $fecha1;  
            $o->fecha2 = $fecha2;
          
            //sumar
            $dinero = $dinero + $o->pago;

            // total registro
            $total = $total + 1;
        } 

        $promo = "Promociones";
        if($idpromo == 1){
            $promo = "Publicidad";
        }

        $nombre = Servicios::where('id', $idservicio)->pluck('nombre')->first();

        $totalDinero = number_format((float)$dinero, 2, '.', '');
 
        $view =  \View::make('backend.paginas.reportes.reportepromo', compact(['registro', 'nombre', 'promo', 'totalDinero', 'total']))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('carta', 'portrait');
 
        return $pdf->stream();
    } 

    // informacion de registro de pagos de promocion o publicidad
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
            
          if($r = RegistroPromo::where('id', $request->id)->first()){

            return ['success' => 1, 'registro' => $r];
          }else{
              return ['success' => 2];
          }
        }
    }

    // editar registro de pagos de promocion o publicidad
    public function editar(Request $request){

        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'fecha1' => 'required',
                'fecha2' => 'required',
                'pago' => 'required',
            );

            $messages = array(   
                'id.required' => 'El id es requerido',
                'fecha1.required' => 'El fecha 1 es requerido',
                'fecha2.required' => 'El fecha 2 es requerido',
                'pago.required' => 'El pago es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            $des = "";
            if($request->descripcion != null){
                $des = $request->descripcion;
            }

            if(RegistroPromo::where('id', $request->id)->first()){                        

                RegistroPromo::where('id', $request->id)->update([
                    'fecha1' => $request->fecha1,
                    'fecha2' => $request->fecha2,
                    'descripcion' => $des,
                    'pago' => $request->pago]);
              
                return ['success' => 1];
            }else{
                return ['success' => 2]; 
            }
        }         
    }
  
     
}
