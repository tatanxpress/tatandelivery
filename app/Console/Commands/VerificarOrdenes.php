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
    protected $description = 'Verificar ordenes que despues de 2 minutos de hora estimada, marque alarma';

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

        if(count($orden) > 0){

            $total = 0;
            $seguro = false;
            // obtener cada id, de la orden que necesita motorista y enviar notificacion
            // al administrador una alerta por todas las ordenes pendientes. 
            foreach($orden as $o){

                // SOLO PARA SERVICIOS NO PRIVADOS
                $valor = Servicios::where('id', $o->servicios_id)->first();

                if($valor->privado == 0){
                    if(OrdenesUrgentes::where('ordenes_id', $o->id)->first()){
                        // no guardar registro.
                    }else{

                        // preguntar si supera hora estimada, con la hora actual
                        $time1 = Carbon::parse($o->fecha_4);
                        $resta = $o->hora_2 - 5; // sin los 5 minutos al cliente                             
                        $horaEstimada = $time1->addMinute($resta + 2)->format('Y-m-d H:i:s'); // 2 min de advertencia
                       
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
                    $alarma = 1; //sonido alarma 
                    $color = 1; // color rojo
                    $icono = 1; // campana
                    $tipo = 4; // administradores

                    $this->envioNoticacion($titulo, $mensaje, $pilaAdministradores, $alarma, $color, $icono, $tipo);                            
                }
            }
        }
       
    }

    public function envioNoticacion($titulo, $mensaje, $pilaUsuarios, $alarma, $color, $icono, $tipo){
        OneSignal::sendNotificationToUser($titulo, $mensaje, $pilaUsuarios, $alarma, $color, $icono, $tipo);
    }
}
