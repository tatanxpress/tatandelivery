<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Servicios;
use App\ServiciosTipo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BuscadorController extends Controller
{
    // buscador de productos por nombre
    public function buscarProducto(Request $request){
        
        if($request->isMethod('post')){ 
            $reglaDatos = array(                
                'servicioid' => 'required',   
                'nombre' => 'required',             
            );    
                  
            $mensajeDatos = array(                                      
                'servicioid.required' => 'El id del servicio es requerido',
                'nombre.required' => 'El nombre es requerido'
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

                $productos = DB::table('servicios AS s')    
                ->join('servicios_tipo AS st', 'st.servicios_1_id', '=', 's.id')
                ->join('producto AS p', 'p.servicios_tipo_id', '=', 'st.id')
                ->select('p.id', 'p.nombre', 'p.imagen', 'p.activo', 'p.precio', 'p.disponibilidad', 'p.utiliza_imagen')
                ->where('s.id', $request->servicioid)
                ->where('p.disponibilidad', 1)
                ->where('p.activo', 1)
                
                ->where('p.nombre', 'like', '%' . $request->nombre . '%')
                ->get();
                
                return ['success' => 1, 'productos' => $productos];
            }else{
                return ['success' => 2];
            }
        }
    }

    // ver lista de productos al tocar seccion "ver todos"
    public function buscarProductoSeccion(Request $request){
        
        if($request->isMethod('post')){ 
            $reglaDatos = array(                
                'seccionid' => 'required',
            );    
                  
            $mensajeDatos = array(                                      
                'seccionid.required' => 'El id de la seccion es requerido',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );
            if($validarDatos->fails())
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            if(ServiciosTipo::where('id', $request->seccionid)->first()){

                $productos = DB::table('producto AS p')
                ->select('p.id', 'p.nombre', 'p.imagen', 'p.precio', 'p.utiliza_imagen', 'p.activo', 'p.disponibilidad')
                ->where('p.servicios_tipo_id', $request->seccionid)
                ->where('p.activo', 1)
                ->where('p.disponibilidad', 1)
                ->get();
                
                return ['success' => 1, 'productos' => $productos];
            }else{
                return ['success' => 2];
            }
        }
    }

}
