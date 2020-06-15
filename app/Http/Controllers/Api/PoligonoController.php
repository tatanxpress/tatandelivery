<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PoligonoController extends Controller
{
    // zonas para el mapa
    public function getListaZonas(Request $request){

        if($request->isMethod('post')){              
            
            $reglaDatos = array(
               'userid' => 'required',
           );
      
           $mensajeDatos = array(                                      
               'userid.required' => 'El id del usuario es requerido.',                
               );

           $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

           if ( $validarDatos->fails()) 
           {
               return [
                   'success' => 0, 
                   'message' => $validarDatos->errors()->all()
               ];
           }

           if(User::where('id', $request->userid)->first()){
 
                // obtener latitud y longitud de ciudad seleccionada
                $direccion = DB::table('users')            
                ->join('zonas', 'zonas.id', '=', 'users.zonas_id')                
                ->select('zonas.nombre AS nombreZona', 'zonas.latitud AS zonaLatitud', 
                'zonas.longitud AS zonaLongitud')
                ->where('users.id', $request->userid)
                ->get();

                $rr = DB::table('zonas AS z')
                ->join('poligono_array AS p', 'p.zonas_id', '=', 'z.id') 
                ->select('z.id')
                ->where('z.activo', 1)
                ->groupBy('id')
                ->get();

               

                // meter zonas que si tienen poligonos
                $pila = array();
                foreach($rr as $p){
                    array_push($pila, $p->id);
                }
        
                $tablas = DB::table('zonas')
                ->select('id AS idZona', 'nombre AS nombreZona')
                ->whereIn('id', $pila)
                ->get();
               
                $resultsBloque = array();
                $index = 0;

                foreach($tablas  as $secciones){
                    array_push($resultsBloque,$secciones);          
                
                    $subSecciones = DB::table('poligono_array AS pol')            
                    ->select('pol.latitud AS latitudPoligono', 'pol.longitud AS longitudPoligono')
                    ->where('pol.zonas_id', $secciones->idZona)
                    ->get(); 
                    
                    $resultsBloque[$index]->poligonos = $subSecciones;
                    $index++;
                }

                return [
                    'success' => 1,
                    'direccion' => $direccion,
                    'poligono' => $tablas
                ];
            }else{
                return ['success' => 2];
            }           
        }
    }

    

}
