<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Log; 
use Carbon\Carbon;
use OneSignal;
use App\OrdenesUrgentes;
use App\Servicios;
use DateTime;
use App\OrdenesDirecciones;
use App\OrdenesPendienteContestar;

class VerificarOrdenes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ordenes:verificar';

    /** 
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Varios estados, con cronometro de 2 minutos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        // ORDENES DE HOY 

        $orden = DB::table('ordenes')
        ->where('estado_5', 1) // terminada de preparar
        ->where('estado_6', 0) // no ha iniciado entrega
        ->whereDate('fecha_orden', '=', Carbon::today('America/El_Salvador')->toDateString())
        ->orderBy('id', 'ASC')
        ->get();

        if(count($orden) > 0){ // verificar que hay al menos 1

            $total = 0; // contar ordenes
            $seguro = false;
            // obtener cada id, de la orden que necesita motorista y enviar notificacion
            // al administrador una alerta por todas las ordenes pendientes. 
            foreach($orden as $o){

                // SOLO PARA SERVICIOS NO PRIVADOS
                //$valor = Servicios::where('id', $o->servicios_id)->first();

               // if($valor->privado == 0){
                    if(OrdenesUrgentes::where('ordenes_id', $o->id)->first()){
                        // ya tengo un registro igual, asi que no guardarla
                    }else{

                        $tiempo = OrdenesDirecciones::where('ordenes_id', $request->ordenid)->first();

                        // preguntar si supera hora estimada, con la hora actual
                        $time1 = Carbon::parse($o->fecha_4);                         
                        $horaEstimada = $time1->addMinute($o->hora_2 + 2)->format('Y-m-d H:i:s'); // 2 min de advertencia
                       
                        $today = Carbon::now('America/El_Salvador')->format('Y-m-d H:i:s');
                                        
                        $d1 = new DateTime($horaEstimada);
                        $d2 = new DateTime($today);
        
                         if ($d1 > $d2){
                            // tiempo aun no superado

                         }else{
                            // tiempo superado. MANDAR ADVERTENCIA

                            $seguro = true;
    
                            $total = $total + 1;
        
                            $fecha = Carbon::now('America/El_Salvador');
        
                            $osp = new OrdenesUrgentes;
                            $osp->ordenes_id = $o->id; 
                            $osp->fecha = $fecha;
                            $osp->activo = 1;
                            $osp->tipo = 1;
                            $osp->save();

                         }
                    }
                //}  
            }           

            if($seguro){

                // ENVIAR NOTIFICACIONES SOBRE TOTAL DE ORDENES PENDIENTE
                $administradores = DB::table('administradores')
                ->where('activo', 1)
                ->where('disponible', 1)
                ->get();

                $pilaAdministradores = array();
                foreach($administradores as $p){
                    if(!empty($p->device_id)){
                        
                        if($p->device_id != "0000"){
                            array_push($pilaAdministradores, $p->device_id);
                        }
                    }
                } 

                //si no esta vacio
                if(!empty($pilaAdministradores)){
                    $titulo = "Orden Para Entrega Inmediata";
                    $mensaje = $total . " Ordenes pendiente de Entrega";
                    try {
                        $this->envioNoticacionAdministrador($titulo, $mensaje, $pilaAdministradores);
                    } catch (Exception $e) {
                        
                    } 
                                               
                }
            }
        }


        //*********************************** */

        // buscar ordenes con retraso de 2 minutos que no contestaron.
        // ordenes pendiente - revisar ordenes pendientes

        $ordenhoy = DB::table('ordenes')
        ->where('estado_2', 0) // aun no han contestado
        ->where('estado_8', 0) // no ha sido cancelada
        ->whereDate('fecha_orden', '=', Carbon::today('America/El_Salvador')->toDateString())
        ->orderBy('id', 'ASC')
        ->get();

        $pilaPropietarios = array();

        if(count($ordenhoy) > 0){

            $seguro1 = false;
         
            foreach($ordenhoy as $o){

                // PARA TODOS LOS SERVICIOS
                if(OrdenesPendienteContestar::where('ordenes_id', $o->id)->first()){
                    // no guardar registro, no obtener id propietarios. 
                }
                else{
                    // preguntar si supera hora estimada, con la hora actual
                    $time1 = Carbon::parse($o->fecha_orden);
                    // 2 minutos mas despues de recibir la orden                   
                    $horaAlerta = $time1->addMinute(2)->format('Y-m-d H:i:s'); // 2 min de advertencia
                    
                    $today = Carbon::now('America/El_Salvador')->format('Y-m-d H:i:s');
                                    
                    $d1 = new DateTime($horaAlerta);
                    $d2 = new DateTime($today);

                    if ($d1 > $d2){
                    // tiempo aun no superado
                            
                    }else{
                        // tiempo superado. MANDAR ADVERTENCIA

                        // guardar registro

                        $seguro1 = true;

                        $fecha = Carbon::now('America/El_Salvador');

                        $osp = new OrdenesPendienteContestar;
                        $osp->ordenes_id = $o->id; 
                        $osp->fecha = $fecha;
                        $osp->activo = 1;
                        $osp->tipo = 1;
                        $osp->save();

                        // guardar device id
                        $datos = DB::table('ordenes AS o')
                        ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
                        ->join('propietarios AS p', 'p.servicios_id', '=', 's.id')
                        ->select('p.disponibilidad', 'p.activo', 'o.id', 'p.device_id')
                        ->where('o.id', $o->id)
                        ->where('p.disponibilidad', 1)
                        ->where('p.activo', 1)
                        ->get();

                        foreach($datos as $d){
                            if(!empty($d->device_id)){
                                if($d->device_id != "0000"){                                   
                                    array_push($pilaPropietarios, $d->device_id); 
                                }
                            }
                        }
                        
                    }
                }
            }

            if($seguro1){
                if(!empty($pilaPropietarios)){
                    $titulo = "Ordenes Pendientes";
                    $mensaje = "Revisar Ordenes Nuevas";
                   
                    try {
                        $this->envioNoticacionPropietario($titulo, $mensaje, $pilaPropietarios);  
                    } catch (Exception $e) {
                        
                    }                                              
                }

                $administradores1 = DB::table('administradores')
                ->where('activo', 1)
                ->where('disponible', 1)
                ->get();

                $pilaAdministradores1 = array();
                foreach($administradores1 as $p){
                    if(!empty($p->device_id)){
                        
                        if($p->device_id != "0000"){
                            array_push($pilaAdministradores1, $p->device_id);
                        }
                    }
                } 

                //si no esta vacio
                if(!empty($pilaAdministradores1)){
                    $titulo = "Orden Sin Contestacion";
                    $mensaje = "Hay ordenes sin contestacion";
                    try {
                        $this->envioNoticacionAdministrador($titulo, $mensaje, $pilaAdministradores1);   
                    } catch (Exception $e) {
                        
                    }                                               
                }

                
            }            
        }       
    }

    public function envioNoticacionAdministrador($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionAdministrador($titulo, $mensaje, $pilaUsuarios);
    }

    public function envioNoticacionPropietario($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionPropietario($titulo, $mensaje, $pilaUsuarios);
    }
}
