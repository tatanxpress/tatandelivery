<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Revisador;
use App\BitacoraRevisador;
use Illuminate\Support\Facades\DB;
use DateTime;
use Carbon\Carbon;

class BitacoraRevisadorController extends Controller
{
    // ver fecha de recorte
    public function verFechaRecorte(Request $request){
        if($request->isMethod('post')){
            $reglaDatos = array( 
                'id' => 'required',
            );

            $mensajeDatos = array(                                      
                'id.required' => 'El id motorista es requerido.',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if(Revisador::where('id', $request->id)->first()){
                $dato = DB::table('bitacora_revisador')
                ->where('revisador_id', $request->id)
                ->latest('id')
                ->first();

                if (empty($dato)) {
                    /*$dinero = DB::table('ordenes_revisadas AS r')
                    ->join('ordenes AS o', 'o.id', '=', 'r.ordenes_id')
                    ->select('o.precio_total')
                    ->where('r.revisador_id', $request->id)     
                    ->get();

                    $suma = collect($dinero)->sum('precio_total');
                    $ganado = number_format((float)$suma, 2, '.', '');*/

                    // no ahy ningun registro
                    return ['success' => 1];
                }else{

                    // dia recorte
                    $fecha1Dato = new DateTime($dato->fecha1);
                    $fecha1 = date_format($fecha1Dato, 'd-m-Y');
                    $fecha2Dato = new DateTime($dato->fecha2);
                    $fecha2 = date_format($fecha2Dato, 'd-m-Y');
                    
                    /*$fechaLimite = $dato->fecha2;
                    
                    $dinero = DB::table('ordenes_revisadas AS r')
                    ->join('ordenes AS o', 'o.id', '=', 'r.ordenes_id')
                    ->select('o.precio_total', 'r.fecha')
                    ->where('r.revisador_id', $request->id)
                    ->whereDate('r.fecha', '>', $fechaLimite)
                    ->get();

                    // dinero que hubo entre fecha a fecha
                    $start = Carbon::parse($dato->fecha1)->startOfDay(); 
                    $end = Carbon::parse($dato->fecha2)->endOfDay(); 

                    $fechaDinero = DB::table('ordenes_revisadas AS r')
                    ->join('ordenes AS o', 'o.id', '=', 'r.ordenes_id')
                    ->select('o.precio_total', 'r.fecha', 'o.precio_envio')
                    ->where('r.revisador_id', $request->id)
                    ->whereBetween('r.fecha', [$start, $end])
                    ->get();*/
                  
                    //$sumaRecorte = collect($fechaDinero)->sum('precio_total');
                    //$sumaRecorteEnvio = collect($fechaDinero)->sum('precio_envio'); 

                   //$ganadoRecorte = number_format((float)$sumaRecorte + $sumaRecorteEnvio, 2, '.', '');

                   $total = number_format((float)$dato->total, 2, '.', '');
                    
                    return ['success' => 2, 'fecha1' => $fecha1, 'fecha2' => $fecha2, 'recorte' => $total];
                }
            }else{
                return ['success' => 3];
            }
        }
    }

}
