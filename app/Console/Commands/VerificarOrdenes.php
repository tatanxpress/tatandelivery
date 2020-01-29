<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Log; 
use Carbon\Carbon;
use OneSignal;
use App\OrdenesPendiente;

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
    protected $description = 'Verificar ordenes de estad5 == 1 y estado6 == 0';

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
        ->where('estado_5', 1)
        ->where('estado_6', 0) 
        ->whereDate('fecha_orden', '=', Carbon::today('America/El_Salvador')->toDateString())
        ->orderBy('id', 'ASC')
        ->get();

        if(count($orden) > 0){

            $total = 0;
            // obtener cada id, de la orden que necesita motorista y enviar notificacion
            // al administrador una alerta por todas las ordenes pendientes. 
            foreach($orden as $o){
                if(OrdenesPendiente::where('ordenes_id', $o->id)->first()){
                    // no guardar registro.
                }else{

                    $total = $total + 1;

                    $fecha = Carbon::now('America/El_Salvador');

                    $osp = new OrdenesPendiente;
                    $osp->ordenes_id = $o->id; 
                    $osp->fecha = $fecha;
                    $osp->activo = 1;
                    $osp->tipo = 5;
                    $osp->save();
                }
            }

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

                $this->envioNoticacion($titulo, $mensaje, $pilaAdministradores, $alarma, $color, $icono);                            
            }
        }
        echo "tarea ejecutada";
    }

    public function envioNoticacion($titulo, $mensaje, $pilaUsuarios, $alarma, $color, $icono){
        OneSignal::sendNotificationToUser($titulo, $mensaje, $pilaUsuarios, $alarma, $color, $icono);
    }
}
