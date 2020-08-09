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
use App\Motoristas;
use App\Ordenes;
use App\OrdenesDescripcion;
use App\OrdenesEncargoRevisadas;
use App\OrdenesEncargo;
use App\MotoristaOrdenes;
use App\OrdenRevisada;

class BitacoraRevisadorController extends Controller
{
    // ver fecha de recorte - NO UTILIZADO 08/08/2020
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

                    // no ahy ningun registro
                    return ['success' => 1];
                }else{

                    // dia recorte
                    $fecha1Dato = new DateTime($dato->fecha1);
                    $fecha1 = date_format($fecha1Dato, 'd-m-Y');
                    $fecha2Dato = new DateTime($dato->fecha2);
                    $fecha2 = date_format($fecha2Dato, 'd-m-Y');
                

                   $total = number_format((float)$dato->total, 2, '.', '');
                    
                    return ['success' => 2, 'fecha1' => $fecha1, 'fecha2' => $fecha2, 'recorte' => $total];
                }
            }else{
                return ['success' => 3];
            }
        }
    }


    // revisar todas las ordenes pendientes de un motorista
    public function revisarTodos(Request $request){
        if($request->isMethod('post')){ 
            $reglaDatos = array(
                'id' => 'required',
                'codigo' => 'required',
                'motorista' => 'required',
            );

            $mensajeDatos = array(                                      
                'id.required' => 'El id revisador es requerido.',
                'motorista.required' => 'El id motorista es requerido.',
                'codigo.required' => 'El codigo es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            } 

            if($r = Revisador::where('id', $request->id)->first()){

                if($r->codigo == $request->codigo){

                    $noquiero = DB::table('ordenes_revisadas')->get();

                    $pilaOrden = array();
                    foreach($noquiero as $p){
                        array_push($pilaOrden, $p->ordenes_id);
                    }

                    $orden = DB::table('motorista_ordenes AS mo')
                    ->join('ordenes AS o', 'o.id', '=', 'mo.ordenes_id')
                    ->select('o.id')
                    ->where('mo.motoristas_id', $request->motorista)               
                    ->where('o.estado_6', 1) // ordenes que motorista inicio la entrega 
                    ->where('o.estado_8', 0) // no canceladas
                    ->whereNotIn('o.id', $pilaOrden) // filtro para no ver ordenes revisadas
                    ->get();

                    $fecha = Carbon::now('America/El_Salvador');

                    DB::beginTransaction();
                    try {

                        foreach($orden as $o){

                            if(OrdenRevisada::where('ordenes_id', $o->id)->first()){
                                // ya hay un registro, asi que no guardar nada del bucle
                                return ['success' => 1];
                            }

                            if($oo = Ordenes::where('id', $o->id)->first()){
                                if($oo->estado_7 == 0){
                                    // orden aun no completada, no puede confirmar
                                    return ['success' => 2];
                                }
                            }

                            $nueva = new OrdenRevisada();
                            $nueva->ordenes_id = $o->id;
                            $nueva->fecha = $fecha;
                            $nueva->revisador_id = $request->id;
                            $nueva->save();                        
                        } 
                    
                        DB::commit();

                        return ['success' => 3]; // guardado

                    } catch(\Throwable $e){
                        DB::rollback();
                            return [
                                'success' => 4 // error en el bucle
                            ];
                    }

                }else{
                    // codigo incorrecto
                    return ['success' => 5];
                }
            }else{
                return ['success' => 6]; // revisador no encontrado
            }        
        }
    }



}
