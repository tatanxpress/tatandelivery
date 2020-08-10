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
use App\MotoristaOrdenes;
use App\OrdenesUrgentesDos;
use App\OrdenesUrgentesTres;
use App\OrdenesUrgentesCuatro;
use App\ClienteNoContesta;

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

        // ordenes completadas, aun no sale motorista, ya paso la hora estimada de entrega que dio el 
        // propietario + 5 min extra.
        // tabla: ordenes_urgentes

        $orden = DB::table('ordenes')
        ->where('estado_5', 1) // terminada de preparar
        ->where('estado_6', 0) // no ha iniciado entrega
        ->whereDate('fecha_orden', '=', Carbon::today('America/El_Salvador')->toDateString())
        ->orderBy('id', 'ASC')
        ->get();

        if(count($orden) > 0){ // verificar que hay al menos 1

            $total = 0; // contar ordenes
            $seguro = false;
            foreach($orden as $o){

                if(OrdenesUrgentes::where('ordenes_id', $o->id)->first()){
                    // ya tengo un registro igual, asi que no guardara
                }else{

                    $time1 = Carbon::parse($o->fecha_4);                         
                    $horaEstimada = $time1->addMinute($o->hora_2 + 5)->format('Y-m-d H:i:s');                     
                    $today = Carbon::now('America/El_Salvador')->format('Y-m-d H:i:s');
                                    
                    $d1 = new DateTime($horaEstimada);
                    $d2 = new DateTime($today);
    
                        if ($d1 > $d2){
                        // tiempo aun no superado

                        }else{
                        // supero tiempo estimada de entrega

                        $seguro = true;

                        $total = $total + 1;
    
                        $fecha = Carbon::now('America/El_Salvador');
    
                        $osp = new OrdenesUrgentes;
                        $osp->ordenes_id = $o->id; 
                        $osp->fecha = $fecha;
                        $osp->activo = 1;
                        //$osp->tipo = 1;
                        $osp->save();

                        }
                }
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

        // propietario termino de preparar la orden y ningun motorista agarro la orden
        // tabla: ordenes_urgentes_dos

        $orden2 = DB::table('ordenes')
        ->where('estado_5', 1) // terminada de preparar
        ->where('estado_6', 0) // aun no ha sido entregada
        ->whereDate('fecha_orden', '=', Carbon::today('America/El_Salvador')->toDateString())
        ->orderBy('id', 'ASC')
        ->get();

        if(count($orden2) > 0){ // verificar que hay al menos 1

            $total = 0; // contar ordenes
            $seguro = false;
            foreach($orden2 as $o){

                if(MotoristaOrdenes::where('ordenes_id', $o->id)->first()){
                    // esta orden ya la agarro motorista
                }else{
                    // registrar pues que no la agarrado y la orden ya esta preparada

                    if(OrdenesUrgentesDos::where('ordenes_id', $o->id)->first()){
                        // no guardar
                    }else{
                        $seguro = true;
                        $total = $total + 1;
    
                        $fecha = Carbon::now('America/El_Salvador');
                        $osp = new OrdenesUrgentesDos;
                        $osp->ordenes_id = $o->id; 
                        $osp->fecha = $fecha;
                        $osp->activo = 1;
                        //$osp->tipo = 1;
                        $osp->save();
                    }                   
                }
            }           

            if($seguro){

                // ENVIAR NOTIFICACIONES
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
                    $mensaje = $total . " Ordenes Sin Motorista";
                    try {
                        $this->envioNoticacionAdministrador($titulo, $mensaje, $pilaAdministradores);
                    } catch (Exception $e) {
                        
                    }                                                
                }
            }
        }


        //*********************************** */
        // pasaron 5+ de hora entrega al cliente (hora_2 + zona) y no se ha entregado su orden
        // tabla ordenes_urgentes_tres
        
        $orden3 = DB::table('ordenes')
        ->where('estado_5', 1) // terminada de preparar
        ->where('estado_7', 0) // aun no ha sido entregado
        ->whereDate('fecha_orden', '=', Carbon::today('America/El_Salvador')->toDateString())
        ->orderBy('id', 'ASC')
        ->get();

        if(count($orden3) > 0){ // verificar que hay al menos 1

            $total = 0; // contar ordenes
            $seguro = false;
            foreach($orden3 as $o){

                if(OrdenesUrgentesTres::where('ordenes_id', $o->id)->first()){
                    // ya tengo un registro igual, asi que no guardara
                }else{

                     // tiempo de la zona agregado
                    $tiempo = OrdenesDirecciones::where('ordenes_id', $o->id)->pluck('copia_tiempo_orden')->first();

                    // sumatoria
                    $tiempoTotal = $o->hora_2 + $tiempo + 5;

                    $time1 = Carbon::parse($o->fecha_4);                         
                    $horaEstimada = $time1->addMinute($tiempoTotal)->format('Y-m-d H:i:s');                     
                    $today = Carbon::now('America/El_Salvador')->format('Y-m-d H:i:s');
                                    
                    $d1 = new DateTime($horaEstimada);
                    $d2 = new DateTime($today);
    
                    if ($d1 > $d2){
                        // tiempo aun no superado

                    }else{
                        // supero tiempo estimada de entrega maxima

                        $seguro = true;

                        $total = $total + 1;
    
                        $fecha = Carbon::now('America/El_Salvador');
    
                        $osp = new OrdenesUrgentesTres;
                        $osp->ordenes_id = $o->id; 
                        $osp->fecha = $fecha;
                        $osp->activo = 1;
                        //$osp->tipo = 1;
                        $osp->save();
                    }
                }
            }           

            if($seguro){

                // ENVIAR NOTIFICACIONES
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
                    $titulo = "Orden Urgente";
                    $mensaje = $total . " Orden supero tiempo de entrega maxima";
                    try {
                        $this->envioNoticacionAdministrador($titulo, $mensaje, $pilaAdministradores);
                    } catch (Exception $e) {
                        
                    }                                                
                }
            }
        }


        // paso la mitad de tiempo que el propietario dijo que entregarian la orden
        // ningun motorista agarro la orden
        // tabla ordenes_urgentes_cuatro

        $orden4 = DB::table('ordenes')
        ->where('estado_4', 1) // ya inicio preparacion
        ->where('estado_8', 0) // aun no ha sido cancelada
        ->whereDate('fecha_orden', '=', Carbon::today('America/El_Salvador')->toDateString())
        ->orderBy('id', 'ASC')
        ->get();

        if(count($orden4) > 0){ // verificar que hay al menos 1

            $total = 0; // contar ordenes
            $seguro = false;
            foreach($orden4 as $o){

                if(MotoristaOrdenes::where('ordenes_id', $o->id)->first()){
                    // ya la agarraron
                }else{
                    if(OrdenesUrgentesCuatro::where('ordenes_id', $o->id)->first()){
                        // ya tengo un registro igual, asi que no guardara
                    }else{
    
                        // sumatoria
                        $tiempoTotal = $o->hora_2 / 2;
    
                        $time1 = Carbon::parse($o->fecha_4);                         
                        $horaEstimada = $time1->addMinute($tiempoTotal)->format('Y-m-d H:i:s');                     
                        $today = Carbon::now('America/El_Salvador')->format('Y-m-d H:i:s');
                                        
                        $d1 = new DateTime($horaEstimada);
                        $d2 = new DateTime($today);
        
                        if ($d1 > $d2){
                            // tiempo aun no superado
    
                        }else{
                            // supero tiempo estimada de entrega maxima
    
                            $seguro = true;
    
                            $total = $total + 1;
        
                            $fecha = Carbon::now('America/El_Salvador');
        
                            $osp = new OrdenesUrgentesCuatro;
                            $osp->ordenes_id = $o->id; 
                            $osp->fecha = $fecha;
                            $osp->activo = 1;
                            //$osp->tipo = 1;
                            $osp->save();
                        }
                    }
                }                
            }           

            if($seguro){

                // ENVIAR NOTIFICACIONES
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
                    $titulo = "Orden Sin Motorista";
                    $mensaje = $total . " Orden supero mitad de tiempo";
                    try {
                        $this->envioNoticacionAdministrador($titulo, $mensaje, $pilaAdministradores);
                    } catch (Exception $e) {
                        
                    }                                                
                }
            }
        }

        //*********************************** */

        // se activa cuando hay ordenes sin contestar y no han sido canceladas
        // se le agrega 1 minutos extra, sino se activara el registro y notificacion

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
                    $horaAlerta = $time1->addMinute(1)->format('Y-m-d H:i:s'); // 2 min de advertencia
                    
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
                        //$osp->tipo = 1;
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


            //*********************************** */

        // se activa cuando hay ordenes que el cliente no contesta
        // se le agrega 2 minutos extra, sino se activara el registro y notificacion

        $ordenhoy = DB::table('ordenes')
        ->where('estado_2', 1) // ya respondio cliente con el tiempo
        ->where('estado_3', 0) // aun no contesta cliente que espera la orden
        ->where('estado_8', 0) // no ha sido cancelada
        ->whereDate('fecha_orden', '=', Carbon::today('America/El_Salvador')->toDateString())
        ->orderBy('id', 'ASC')
        ->get();

        $pilaCliente = array();

        if(count($ordenhoy) > 0){

            $seguroC = false;
         
            foreach($ordenhoy as $o){

                // PARA TODOS LOS SERVICIOS
                if(ClienteNoContesta::where('ordenes_id', $o->id)->first()){
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

                        $seguroC = true;

                        $fecha = Carbon::now('America/El_Salvador');

                        $osp = new ClienteNoContesta;
                        $osp->ordenes_id = $o->id;
                        $osp->save();
                        
                    }
                }
            }

            if($seguroC){

                $administradores2 = DB::table('administradores')
                ->where('activo', 1)
                ->where('disponible', 1)
                ->get();

                $pilaAdministradores2 = array();
                foreach($administradores2 as $p){
                    if(!empty($p->device_id)){
                        
                        if($p->device_id != "0000"){
                            array_push($pilaAdministradores2, $p->device_id);
                        }
                    }
                } 

                //si no esta vacio
                if(!empty($pilaAdministradores2)){
                    $titulo = "Cliente No Contesta";
                    $mensaje = "Hay Ordenes Sin Contestacion de Cliente";
                    try {
                        $this->envioNoticacionAdministrador($titulo, $mensaje, $pilaAdministradores2);   
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
