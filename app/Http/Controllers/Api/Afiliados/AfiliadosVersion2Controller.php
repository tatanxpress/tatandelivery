<?php

namespace App\Http\Controllers\Api\Afiliados;

use App\AdminOrdenes;
use App\HorarioServicio;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Propietarios;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Ordenes;
use App\OrdenesDescripcion;
use App\OrdenesDirecciones;
use App\PagoPropietario;
use App\Producto;
use App\Servicios;
use App\User;
use App\Zonas;
use App\ZonasServicios;
use Carbon\Carbon;
use DateTime;
use Exception; 
use App\OrdenesPendiente;
use App\OrdenesCupones;
use App\Cupones;
use App\AplicaCuponCuatro;
use App\AplicaCuponCinco;
use App\MotoristaOrdenes;
use App\Motoristas;
use App\ServiciosTipo;
use Log;

class AfiliadosVersion2Controller extends Controller
{
    // CONTROLADOR PARA AFILIADOS 30/05/2020  11:23 AM

    // mostrara las categorias
    public function verCategoriasProductos(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
            );
 
            $messages = array(                                      
                'id.required' => 'El id propietario es requerido',
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Propietarios::where('id', $request->id)->first()){

                // buscar lista de productos
                $categorias = DB::table('servicios_tipo AS st')    
                ->join('servicios AS s', 's.id', '=', 'st.servicios_1_id')
                ->select('st.id', 'st.nombre', 'st.activo')
                ->where('st.servicios_1_id', $p->servicios_id) // unicamente dame el id del servicio
                ->orderBy('st.posicion', 'ASC')
                ->where('st.activo_admin', 1) // activo por administrador
                ->get();     
                                
                return ['success'=> 1, 'categorias'=> $categorias];
            }else{
                return ['success'=> 2];
            }
        }
    }

    // actualizar categoria
    public function actualizarCategoria(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
                'idcategoria' => 'required',
                'nombre' => 'required',
                'valor' => 'required'
            );
 
            $messages = array(                                      
                'id.required' => 'El id propietario es requerido',
                'idcategoria.required' => 'ID categoria es requerido',
                'nombre.required' => 'Nombre es requerido',
                'valor.required' => 'Actualizar categoria'          
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Propietarios::where('id', $request->id)->first()){


                if($request->valor == 1){
                     // obtener todos los productos de esa categoria
                     $pL = DB::table('producto') 
                     ->where('servicios_tipo_id', $request->idcategoria)
                     ->get();
 
                      $bloqueo = true;
 
                      foreach($pL as $lista){
                         if($lista->disponibilidad == 1){ // si hay al menos 1 producto activo, no se desactiva categoria
                             $bloqueo = false;
                         }
                     }
 
                     if($bloqueo){
                         $mensaje = "Para activar la categoría, se necesita un producto disponible";
                         return ['success' => 2, 'mensaje' => $mensaje];
                    } 
                }

                // actualizar
                ServiciosTipo::where('id', $request->idcategoria)->update(['activo' => $request->valor, 'nombre' => $request->nombre]);    
                                
                return ['success'=> 1];
            }else{
                return ['success'=> 0];
            }
        }
    }

    // activar categoria ios
    public function activarCategoria(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
                'idcategoria' => 'required'                
            );
 
            $messages = array(                                      
                'id.required' => 'El id propietario es requerido',
                'idcategoria.required' => 'ID categoria es requerido'                
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Propietarios::where('id', $request->id)->first()){

                // obtener todos los productos de esa categoria
                $pL = DB::table('producto') 
                ->where('servicios_tipo_id', $request->idcategoria)
                ->get();

                $bloqueo = true;

                foreach($pL as $lista){
                    if($lista->disponibilidad == 1){ // si hay al menos 1 producto activo, no se desactiva categoria
                        $bloqueo = false;
                    }
                }

                if($bloqueo){
                    $mensaje = "Para activar la categoría, se necesita un producto disponible";
                    return ['success' => 2, 'mensaje' => $mensaje];
                } 

                // actualizar
                ServiciosTipo::where('id', $request->idcategoria)->update(['activo' => 1]);    
                                
                return ['success'=> 1];
            }else{
                return ['success'=> 0];
            }
        }
    }

    // desactivar categoria ios
    public function desactivarCategoria(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
                'idcategoria' => 'required'                
            );
 
            $messages = array(                                      
                'id.required' => 'El id propietario es requerido',
                'idcategoria.required' => 'ID categoria es requerido'                
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Propietarios::where('id', $request->id)->first()){
               
                // actualizar
                ServiciosTipo::where('id', $request->idcategoria)->update(['activo' => 0]);    
                                
                return ['success'=> 1];
            }else{
                return ['success'=> 0];
            }
        }
    }

    // editar categoria para ios
    public function editarCategoria(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
                'idcategoria' => 'required',
                'nombre' => 'required'
            );
 
            $messages = array(                                      
                'id.required' => 'El id propietario es requerido',
                'idcategoria.required' => 'ID categoria es requerido',
                'nombre.required' => 'Nombre es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Propietarios::where('id', $request->id)->first()){
              
                // actualizar
                ServiciosTipo::where('id', $request->idcategoria)->update(['nombre' => $request->nombre]);    
                                
                return ['success'=> 1];
            }else{
                return ['success'=> 0];
            }
        }
    }

    // actualizar nombre de categoria para ios
    public function actualizarNombreCategoria(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
                'idcategoria' => 'required',
                'nombre' => 'required',
                'valor' => 'required'
            );
 
            $messages = array(                                      
                'id.required' => 'El id propietario es requerido',
                'idcategoria.required' => 'ID categoria es requerido',
                'nombre.required' => 'Nombre es requerido',
                'valor.required' => 'Actualizar categoria'          
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Propietarios::where('id', $request->id)->first()){


                if($request->valor == 1){
                     // obtener todos los productos de esa categoria
                     $pL = DB::table('producto') 
                     ->where('servicios_tipo_id', $request->idcategoria)
                     ->get();
 
                      $bloqueo = true;
 
                      foreach($pL as $lista){
                         if($lista->disponibilidad == 1){ // si hay al menos 1 producto activo, no se desactiva categoria
                             $bloqueo = false;
                         }
                     }
 
                     if($bloqueo){
                         $mensaje = "Para activar la categoría, se necesita un producto disponible";
                         return ['success' => 2, 'mensaje' => $mensaje];
                    } 
                }

                // actualizar
                ServiciosTipo::where('id', $request->idcategoria)->update(['activo' => $request->valor, 'nombre' => $request->nombre]);    
                                
                return ['success'=> 1];
            }else{
                return ['success'=> 0];
            }
        }
    }

    // actualizar producto
    public function actualizarProducto(Request $request){
         
        if($request->isMethod('post')){   
            $rules = array(   
                'id' => 'required',
                'productoid' => 'required',
                'estadonombre' => 'required', // cambiara nombre
                'estadodescripcion' => 'required', // cambiara descripcion
                'estadoprecio' => 'required', // cambiara precio
                'estadoproducto' => 'required', // cambiara estado
                'estadounidades' => 'required' // cambiara unidades                
            );
 
            $messages = array(  
                'id.required' => 'El id es requerido',  
                'productoid.required' => 'El id producto es requerido',
                'estadonombre.required' => 'El estado nombre es requerido',
                'estadodescripcion.required' => 'El estado descripcion es requerido',
                'estadoprecio.required' => 'El estado precio es requerido',
                'estadoproducto.required' => 'El estado producto es requerido',
                'estadounidades.required' => 'El estado unidades requerido',
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            } 

            if($pp = Propietarios::where('id', $request->id)->first()){

                // no puede editar los productos
                if($pp->bloqueado == 1){
                    return ['success'=> 6];
                }

                if($dataPro = Producto::where('id', $request->productoid)->first()){

                    // modificara nombre del producto
                    if($request->estadonombre == "1"){
                        if($request->nombre == ""){
                            return ['success'=> 1];
                        }
                        Producto::where('id', $request->productoid)->update(['nombre' => $request->nombre]);
                    }
    
                    // modificara descripcion del producto
                    if($request->estadodescripcion == "1"){
                        if($request->descripcion == ""){
                            return ['success'=> 2];
                        }
                        Producto::where('id', $request->productoid)->update(['descripcion' => $request->descripcion]);
                    }
    
                    // modificara precio
                    if($request->estadoprecio == "1"){
                        if($request->precio == ""){
                            return ['success'=> 3];
                        }
                        Producto::where('id', $request->productoid)->update(['precio' => $request->precio]);
                    }
    
                    // modificara unidades
                    if($request->estadounidades == "1"){
                        if($request->unidades != "" || $request->unidades != null){
                            Producto::where('id', $request->productoid)->update(['unidades' => $request->unidades]);
                        }
                    } 
    
                    // modificara nota producto
                    if($request->estadonota == "1"){
                        $nota = $request->nota;
                        if($request->nota == null){
                            $nota = "";
                        }
    
                        Producto::where('id', $request->productoid)->update(['utiliza_nota' => $request->estadonota, 'nota' => $nota]);
                    }else{
                        Producto::where('id', $request->productoid)->update(['utiliza_nota' => 0]);
                    }
    
                    // cambiar disponibilidad producto y utiliza unidades
                    Producto::where('id', $request->productoid)->update(['disponibilidad' => $request->estadoproducto, 'utiliza_cantidad' => $request->estadounidades]);
                      

                    // verificar si es el ultimo producto de esta categoria para desactivarla
                               
                    $productosLista = DB::table('producto') 
                    ->where('servicios_tipo_id', $dataPro->servicios_tipo_id) // activo por administrador
                    ->get();

                    $seguro = true;
                    
                    // comprobar
                    foreach($productosLista as $lista){
                        if($lista->disponibilidad == 1){ // si hay al menos 1 producto activo, no se desactiva categoria
                            $seguro = false;
                        }
                    }

                    if($seguro){
                        // desactivar categoria, ningun producto disponible
                        ServiciosTipo::where('id', $dataPro->servicios_tipo_id)->update(['activo' => 0]); 
                    }else{
                        // activar categoria ya que hay al menos 1 producto disponible
                        ServiciosTipo::where('id', $dataPro->servicios_tipo_id)->update(['activo' => 1]); 
                    }
                            
                    return ['success'=> 5];
    
                }else{
                    return ['success'=> 0];
                } 
            }else{
                return ['success'=> 0];
            }    
        }
    } 

    // actualizar posiciones de las categorias
    public function actualizarCategoriaPosiciones(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'
            );
 
            $messages = array(                                      
                'id.required' => 'El id propietario es requerido'
            );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Propietarios::where('id', $request->id)->first()){

               // Log::info($request->all());
               
                foreach($request->categoria as $key => $value){

                    $posicion = $value['posicion'];

                    ServiciosTipo::where('id', $key)->update(['posicion' => $posicion]); 
                }

                return ['success' => 1];
            }
        }
    }

     // actualizar posiciones de las categorias en ios
     public function actualizarCategoriaPosicionesIos(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'
            );
 
            $messages = array(                                      
                'id.required' => 'El id propietario es requerido'
            );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Propietarios::where('id', $request->id)->first()){

                foreach($request->categoria as $key => $value){

                    $id = $value["id"];
                    $posicion = $value['posicion'];

                    ServiciosTipo::where('id', $id)->update(['posicion' => $posicion]); 
                }

                return ['success' => 1];
            }
        }
    }

    // ver todos los productos de una categoria
    public function productosDeCategoria(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
                'idcategoria' => 'required'
            );
 
            $messages = array(                                      
                'id.required' => 'El id propietario es requerido',
                'idcategoria.required' => 'id categoria es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Propietarios::where('id', $request->id)->first()){

                // buscar lista de productos
                $categorias = DB::table('producto AS p')    
                ->join('servicios_tipo AS st', 'st.id', '=', 'p.servicios_tipo_id')
                ->select('p.id', 'p.nombre')
                ->where('st.id', $request->idcategoria)
                ->orderBy('p.posicion', 'ASC')
                ->where('p.activo', 1) // activo producto por admin
                ->get();
                                
                return ['success'=> 1, 'categorias'=> $categorias];
            }else{
                return ['success'=> 2];
            }
        }
    }

    // actualizar posiciones de los productos
    public function actualizarProductoPosiciones(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'
            );
 
            $messages = array(                                      
                'id.required' => 'El id propietario es requerido'
            );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Propietarios::where('id', $request->id)->first()){


                foreach($request->categoria as $key => $value){

                    $posicion = $value['posicion'];

                    Producto::where('id', $key)->update(['posicion' => $posicion]); 
                }


                return ['success' => 1];
            }
        }
    }

    // actualizar posiciones de los productos para ios
    public function actualizarProductoPosicionesIos(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'
            );
 
            $messages = array(                                      
                'id.required' => 'El id propietario es requerido'
            );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()){
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
            
            if($p = Propietarios::where('id', $request->id)->first()){


                foreach($request->categoria as $key => $value){

                    $id = $value["id"];
                    $posicion = $value['posicion'];

                    Producto::where('id', $id)->update(['posicion' => $posicion]); 
                }


                return ['success' => 1];
            }
        }
    }

}
