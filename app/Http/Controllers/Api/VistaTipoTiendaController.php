<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Servicios;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class VistaTipoTiendaController extends Controller
{
    // devolver productos por secciones
    public function getTodoProductoTienda(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(                
                'servicioid' => 'required',
            );    
        
            $mensajeDatos = array(                                      
                'servicioid.required' => 'El id del servicio es requerido.',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }   
            
            if(Servicios::where('id', $request->servicioid)->first()){

                // obtener secciones
                $tipo = DB::table('servicios_tipo AS st')
                ->join('servicios AS s', 's.id', '=', 'st.servicios_1_id')
                ->select('st.id AS tipoId', 'st.nombre AS nombreSeccion')
                ->where('st.servicios_1_id', $request->servicioid)
                ->where('st.activo', 1)
                ->orderBy('st.posicion', 'ASC')
                ->get();

                // obtener total de productos por seccion
                foreach ($tipo as $user){
    
                    // contar cada seccion
                    $producto = DB::table('servicios_tipo AS st')
                    ->join('producto AS p', 'p.servicios_tipo_id', '=', 'st.id')
                    ->select('st.id')
                    ->where('st.id', $user->tipoId)
                    ->get();
    
                    $contador = count($producto);
                    $user->total = $contador;    
                }
    
                $resultsBloque = array();        
                $index = 0;
    
                foreach($tipo  as $secciones){
                    array_push($resultsBloque,$secciones);                          
                
                    $subSecciones = DB::table('producto AS p')  
                    ->select('p.id AS idProducto','p.nombre AS nombreProducto', 'p.descripcion AS descripcionProducto',
                             'p.imagen AS imagenProducto', 'p.utiliza_imagen', 'p.precio AS precioProducto', 'p.disponibilidad', 'p.activo', 'p.es_promocion')
                    ->where('p.servicios_tipo_id', $secciones->tipoId)
                    ->where('p.activo', 1)
                    ->where('p.disponibilidad', 1)
                    ->where('p.es_promocion', 0)
                    ->take(5) //maximo 5 productos por seccion
                    ->orderBy('p.posicion', 'ASC') // ordenados
                    ->get(); 
                    
                    $resultsBloque[$index]->productos = $subSecciones;
                    $index++;
                }
        
                // buscar horarios del servicio
                $numSemana = [
                    0 => 1, // domingo
                    1 => 2, // lunes
                    2 => 3, // martes
                    3 => 4, // miercoles
                    4 => 5, // jueves
                    5 => 6, // viernes
                    6 => 7, // sabado
                ];
    
                $getValores = Carbon::now('America/El_Salvador');
                $getDiaHora = $getValores->dayOfWeek;
                $diaSemana = $numSemana[$getDiaHora];   
                            
                $servicio = DB::table('servicios AS s')
                ->join('tiempo_aprox AS t', 't.servicios_id', '=', 's.id')
                ->select('s.nombre', 's.descripcion', 's.imagen', 't.tiempo','s.minimo', 's.utiliza_minimo')
                ->where('s.id', $request->servicioid)
                ->where('t.dia', $diaSemana)
                ->get();
               
                //obtener horario
                $horario = DB::table('horario_servicio')            
                ->where('servicios_id', $request->servicioid)
                ->where('dia', $diaSemana)
                ->first(); 
                
                $hora1 = date("h:i A", strtotime($horario->hora1));
                $hora2 = date("h:i A", strtotime($horario->hora2));
                $hora3 = date("h:i A", strtotime($horario->hora3));
                $hora4 = date("h:i A", strtotime($horario->hora4));
                $segundaHora = $horario->segunda_hora; // si es 1, ocupa las 2 horas
                $cerrado = $horario->cerrado; // saver si hoy esta cerrado
             
                return [
                    'success' => 1,
                    'servicio' => $servicio,                    
                    'horario' => ['hora1' => $hora1, 'hora2'=> $hora2, 'hora3' => $hora3, 'hora4' => $hora4, 'segunda' => $segundaHora, 'cerrado' => $cerrado],
                    'productos' => $tipo
                ];

            }else{
                return ['success'=> 2];
            }             
        }
    }
}
