<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Ordenes;
use App\OrdenesDirecciones;
use App\OrdenesDescripcion;
use App\Motoristas;
use App\OrdenesPendiente;
use App\OrdenesCupones;
use App\Cupones;
use App\AplicaCuponUno;
use App\AplicaCuponDos;
use App\AplicaCuponTres;
use App\AplicaCuponCuatro;
use App\MotoristaExperiencia;
use App\MotoristaOrdenes;
use App\AplicaCuponCinco;
use App\Instituciones;
use App\Zonas;
use OneSignal;
use Illuminate\Support\Facades\DB;
use App\Propietarios;
use Illuminate\Support\Facades\Validator;

class ControlOrdenesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

     // lista de ordenes
     public function indexHoy(){

        $fecha = Carbon::now('America/El_Salvador');
        $fecha = date("d-m-Y h:i A", strtotime($fecha));        
        return view('backend.paginas.ordenes.listaordenhoy', compact('fecha'));
    }

    public function indexNotiCliente(){
        return view('backend.paginas.notificacion.listanotificacionzona');
    }
 
    // tabla de lista de ordenes, ultimas 100
    public function tablaHoy(){

        $fecha = Carbon::now('America/El_Salvador');

        $orden = DB::table('ordenes AS o')
        ->join('servicios AS s', 's.id', '=', 'o.servicios_id')       
        ->select('o.id', 's.identificador', 'o.fecha_orden', 's.nombre', 'o.precio_total',
            'o.estado_2', 'o.estado_3', 'o.estado_4', 'o.estado_5', 'o.estado_6',
            'o.estado_7', 'o.estado_8')
        ->whereDate('o.fecha_orden', $fecha)
        ->get();

        $estado = "";
        foreach($orden as $o){        
            $o->fecha_orden = date("h:i A", strtotime($o->fecha_orden));

            $od = OrdenesDirecciones::where('ordenes_id', $o->id)->first();
            $o->zonaidenti = Zonas::where('id', $od->zonas_id)->pluck('identificador')->first();
            
            if($o->estado_2 == 0){
                $estado = "Orden sin contestacion del propietario";
            }

            if($o->estado_2 == 1){
                $estado = "Orden contestada, esperando contestacion del cliente";
            }

            if($o->estado_3 == 1){
                $estado = "Orden contestada por cliente, esperando iniciar orden";
            }

            if($o->estado_4 == 0){
                $estado = "Orden inicio preparacion";
            }

            if($o->estado_5 == 0){
                $estado = "Orden termino prepararse";
            }

            if($o->estado_6 == 0){
                $estado = "Motorista va en camino";
            }

            if($o->estado_7 == 0){
                $estado = "Motorista completo la orden";
            }

            if($o->estado_8 == 0){
                $estado = "Orden cancelada";
            }

            $o->estado = $estado;
        }
 
        return view('backend.paginas.ordenes.tablas.tablaordenhoy', compact('orden'));
    } 

    // index notificaciones
    public function indexNotificacion(){
        return view('backend.paginas.notificacion.listanotificacion');
    }

    public function tablaPropiNoti($id){
        // viene el identificador del servicio

        $noti = DB::table('servicios AS s')
        ->join('propietarios AS p', 'p.servicios_id', '=', 's.id')       
        ->select('p.id', 'p.telefono', 'p.disponibilidad', 'p.activo', 'p.device_id')
        ->where('s.identificador', $id)
        ->get();

        foreach($noti as $n){

            $activo = "";
            $disponibilidad = "";

            if($n->disponibilidad == 1){
                $disponibilidad = "Si";
            }else{
                $disponibilidad = "No";
            }

            if($n->activo == 1){
                $activo = "Si";
            }else{
                $activo = "No";
            }

            $n->activo = $activo;
            $n->disponibilidad = $disponibilidad;
        }

        return view('backend.paginas.notificacion.tablas.tablapropinoti', compact('noti'));
    }

    public function enviarNotiPropi(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'device' => 'required',
                'titulo' => 'required',
                'mensaje' => 'required'       
            );
        
            $mensajeDatos = array(                                      
                'device.required' => 'Device id es requerido.',
                'titulo.required' => 'Titulo es requerido',
                'mensaje.required' => 'Mensaje es requerido'            
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }
            
            try {
                $this->envioNoticacionPropietario($request->titulo, $request->mensaje, $request->device);                               
                } catch (Exception $e) {} 

            return ['success' => 1];
        }
    }

    public function devicePropietario(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'               
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'Device id es requerido.'                        
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }


            $device = Propietarios::where('id', $request->id)->pluck('device_id')->first();

            return ['success' => 1, 'device' => $device]; 
        }
    }
 
    public function envioNoticacionPropietario($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionPropietario($titulo, $mensaje, $pilaUsuarios);
    }

}
 