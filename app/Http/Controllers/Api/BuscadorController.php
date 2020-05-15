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
                ->where('p.disponibilidad', 1) // producto disponible
                ->where('p.activo', 1) // producto activo
                ->where('st.activo', 1) // categoria activa
                ->where('p.es_promocion', 0) // ningun producto marcado como promocion
                ->where('p.nombre', 'like', '%' . $request->nombre . '%')
                ->get();
                
                return ['success' => 1, 'productos' => $productos];
            }else{
                return ['success' => 2];
            }
        }
    }

    // buscador de productos globalmente en los servicios donde esta la direccion del usuario
    public function buscarProductoGlobal(Request $request){
        
        if($request->isMethod('post')){ 
            $reglaDatos = array(                
                'userid' => 'required',   
                'nombre' => 'required',             
            );    
                  
            $mensajeDatos = array(                                      
                'userid.required' => 'El id del usuario es requerido',
                'nombre.required' => 'El nombre del producto es requerido'
                );
            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );
            if($validarDatos->fails())
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }
            
            // obtener todos los servicios de la direccion del usuario
            $datos = DB::table('direccion_usuario AS du')    
                ->join('zonas AS z', 'z.id', '=', 'du.zonas_id')
                ->join('zonas_servicios AS zs', 'zs.zonas_id', '=', 'z.id')
                ->select('du.user_id', 'du.seleccionado', 'du.direccion', 'zs.servicios_id', 'zs.activo')
                ->where('du.user_id', $request->userid)
                ->where('du.seleccionado', 1) // primera direccion seleccionada
                ->where('zs.activo', 1) // zona servicio activo                       
                ->get();

                if($datos == null){
                    return ['success' => 1]; // ninguna direccion encontrada
                }

                $pilaIDServicio = array();

                foreach($datos as $p){
                    $id = $p->servicios_id;                    
                    array_push($pilaIDServicio, $id); 
                }

                $productos = DB::table('servicios AS s')    
                ->join('servicios_tipo AS st', 'st.servicios_1_id', '=', 's.id')
                ->join('producto AS p', 'p.servicios_tipo_id', '=', 'st.id')
                ->select('p.id', 'p.nombre', 'p.precio', 'p.imagen', 'p.utiliza_imagen', 
                's.nombre AS nombreservicio')
                ->whereIn('s.id', $pilaIDServicio)
                ->where('s.activo', 1) // servicio activo
                ->where('st.activo', 1) // la categoria esta activa
                ->where('p.activo', 1) // producto activo
                ->where('p.disponibilidad', 1) // producto disponible
                ->where('p.es_promocion', 0) // ningun producto marcado como promocion
                ->where('p.nombre', 'like', '%' . $request->nombre . '%')
                ->orderBy('s.id', 'ASC')            
                ->get();

            return ['success' => 2, 'productos' => $productos];
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
