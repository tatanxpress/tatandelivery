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

                $tablas = DB::table('zonas AS z')
                ->select('z.id AS idZona', 'z.nombre AS nombreZona')
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
