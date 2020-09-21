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
use App\MotoristaOrdenEncargo;
use App\OrdenesEncargoDireccion;
use App\OrdenesEncargoProducto;
use App\OrdenesEncargo;
use App\EncargoAsignadoServicio;
use App\Encargos;
use OneSignal;
use App\ProductoCategoriaNegocio;
use App\CategoriasNegocio;


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


    // ver encargos asignados a este servicio
    public function verMisEncargos(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'                
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id del propietario es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            } 

            if($pp = Propietarios::where('id', $request->id)->first()){

                $idservicio = Servicios::where('id', $pp->servicios_id)->pluck('id')->first();

                // obtener todos los encargos, asignados a este negocio
                $lista = DB::table('encargo_asignado_servicio AS ea')
                ->join('encargos AS e', 'e.id', '=', 'ea.encargos_id')
                ->select('e.id', 'e.nombre', 'e.descripcion', 'e.fecha_finaliza', 'e.fecha_entrega')
                ->where('e.visible_propietario', 1) // mientras propietario no lo oculte, siempre se mostrara
                ->where('ea.servicios_id', $idservicio)
                ->get();
 
                foreach($lista as $o){
                    $o->fecha_finaliza = date("h:i A d-m-Y", strtotime($o->fecha_finaliza));
                    $o->fecha_entrega = date("h:i A d-m-Y", strtotime($o->fecha_entrega));
                }

                return ['success' => 1, 'ordenes' => $lista];
            }else{
                return ['success' => 2];
            }
        }
    }

    // oculta la tarjeta del encargo
    public function ocultarLaTarjetaEncargo(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'                
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id  es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            } 

            if(Encargos::where('id', $request->id)->first()){

                // verificar si hay encargos pendientes aun
                $lista = DB::table('ordenes_encargo')    
                ->where('encargos_id', $request->id)              
                ->get();

                $bloqueo = 0; 

                foreach($lista as $l){

                    if($l->revisado == 1){ // pendiente, bien
                        $bloqueo = 1; 
                    }

                    if($l->revisado == 2){ // en proceso, bien

                        // saver si finalizo primero                        
                        if($l->estado_1 == 0){
                            $bloqueo = 1;
                        }
                        
                    }
                }

                if($bloqueo > 0){
                    return ['success' => 1];
                }

                Encargos::where('id', $request->id)->update(['visible_propietario' => 0]);                

                return ['success' => 2];
            }else{
                return ['success' => 2];
            }
        }
    }

    // ver todos las ordenes encargos asignados al propietario
    // con esto podra ver los productos, iniciar, completar, cancelar orden
    public function verOrdenesEncargosLista(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'encargoid' => 'required'                
            ); 
        
            $mensajeDatos = array(                                      
                'encargoid.required' => 'El id del encargo es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            } 

            // buscar todos los ordenes encargos, con el id encargo
            // todos aquellos que no han sido completados aun.
            

            // obtener todos las ordenes encargo
            $lista = DB::table('ordenes_encargo')
            ->select('id', 'precio_subtotal', 'fecha_orden', 'estado_0', 'revisado')
            ->where('visible_propietario', 1) // cuando finaliza orden se setea a 0, para no volver a verlo y solo orden no cancelada
            ->whereIn('revisado', [1, 2]) // los pendientes de confirmacion y en proceso
            ->where('encargos_id', $request->encargoid) 
            ->get();

            foreach($lista as $o){
                $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));
                $o->precio_subtotal = number_format((float)$o->precio_subtotal, 2, '.', '');
            }

            return ['success' => 1, 'ordenes' => $lista];
        }
    }

    // el propietario inicia a preparar la orden
    public function iniciarOrdenEncargoPropietario(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'encargoid' => 'required'  // id de la orden encargo          
            );
        
            $mensajeDatos = array(                                      
                'encargoid.required' => 'El id del encargo es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            } 

            if($oo = OrdenesEncargo::where('id', $request->encargoid)->first()){

                // ya fue revisada por el admin, ya esta en proceso
                if($oo->revisado == 2){

                    if($oo->estado_0 == 0){
                        $fecha = Carbon::now('America/El_Salvador');
                        // orden iniciada por el propietarios
                        OrdenesEncargo::where('id', $request->encargoid)->update(['estado_0' => 1, 
                        'fecha_0' => $fecha]);
                    }

                    return ['success' => 1]; // encargo inicio preparacion
                }else{
                    return ['success' => 2]; // encargo aun no puede iniciar
                }
            }else{
                return ['success' => 3]; // encargo no puede ser encontrado 
            }
        }
    }


    // el propietario finaliza de preparar la orden
    public function finalizarOrdenEncargoPropietario(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'encargoid' => 'required'  // id de la orden encargo          
            );
        
            $mensajeDatos = array(                                      
                'encargoid.required' => 'El id del encargo es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            } 
 
            if($oo = OrdenesEncargo::where('id', $request->encargoid)->first()){

                if($oo->estado_2 == 0){
                    $fecha = Carbon::now('America/El_Salvador');
                    // orden finalizada por el propietarios
                    OrdenesEncargo::where('id', $request->encargoid)->update(['estado_1' => 1, 
                    'fecha_1' => $fecha, 'visible_propietario' => 0]);


                    if($moe = MotoristaOrdenEncargo::where('ordenes_encargo_id', $oo->id)->first()){

                        // enviar notificacion a motorista que agarro el encargo
                        $titulo = "Encargo #". $oo->id;
                        $mensaje = "Listo para iniciar entrega";

                        $dd = Motoristas::where('id', $moe->motoristas_id)->first();

                        if($dd->device_id != "0000"){
                            try {
                                $this->envioNoticacionMotorista($titulo, $mensaje, $dd->device_id);                               
                            } catch (Exception $e) {                              
                            }  
                        }                                            
                    }
                }

                return ['success' => 1]; // encargo finalizo preparacion
              
            }else{
                return ['success' => 2]; // encargo no puede ser encontrado 
            }
        }
    }

    // ver las ordenes encargos finalizadas hoy por el propietario
    public function verEncargosFinalizadosHoy(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'  // id de la orden encargo          
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id del propietario es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            } 

            if($oo = Propietarios::where('id', $request->id)->first()){
                
                //obtener todos los encargos asignados a este servicio
                $datos = EncargoAsignadoServicio::where('servicios_id', $oo->servicios_id)->get();

                // obtener todos los id de los encargos
                $pila = array();
                foreach($datos as $p){
                    array_push($pila, $p->encargos_id);
                }

                $orden = DB::table('encargo_asignado_servicio AS e')  // encargos_id  servicios_id
                ->join('ordenes_encargo AS o', 'o.encargos_id', '=', 'e.encargos_id')   // encargos_id
                ->select('o.id', 'o.precio_subtotal', 'o.fecha_1', 'o.encargos_id', 'o.revisado')
                ->where('o.estado_1', 1) // orden finalizada
                ->whereIn('e.encargos_id', $pila)
                ->whereDate('o.fecha_1', '=', Carbon::today('America/El_Salvador')->toDateString())
                ->get(); 

                foreach($orden as $o){
                    $o->fecha_1 = date("h:i A d-m-Y", strtotime($o->fecha_1));

                    $o->nombre = Encargos::where('id', $o->encargos_id)->pluck('nombre')->first();
                }   

                return ['success' => 1, 'ordenes' => $orden];
              
            }else{
                return ['success' => 2]; // encargo no puede ser encontrado 
            }
        }
    }


   

    public function verProductoOrdenEncargo(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'encargoid' => 'required'  // id de la orden encargo          
            );
        
            $mensajeDatos = array(                                      
                'encargoid.required' => 'El id del encargo es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            if($oo = OrdenesEncargo::where('id', $request->encargoid)->first()){

                $productos = DB::table('ordenes_encargo_producto AS o')
                ->join('producto_categoria_negocio AS p', 'p.id', '=', 'o.producto_cate_nego_id')
                ->select('o.id', 'p.imagen', 'o.cantidad', 'o.nota', 'o.precio', 'o.nombre', 'o.descripcion')
                ->where('o.ordenes_encargo_id', $request->encargoid)
                ->get();
        
                foreach($productos as $o){
                        
                    $cantidad = $o->cantidad;
                    $precio = $o->precio;
                    $multi = $cantidad * $precio;
                    $o->multiplicado = number_format((float)$multi, 2, '.', '');  
                    $o->precio = number_format((float)$o->precio, 2, '.', '');  
                }


                return ['success' => 1, 'productos' => $productos];              
            }else{
                return ['success' => 2]; 
            }
        }

    }


    public function verProductoIndividualOrdenEncargo(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'productoid' => 'required' // es el id de la fila de ese producto
            );
        
            $mensajeDatos = array(                                      
                'productoid.required' => 'El id del producto es requerido.'   
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            $producto = DB::table('ordenes_encargo_producto AS op')
            ->join('producto_categoria_negocio AS pc', 'pc.id', '=', 'op.producto_cate_nego_id')
            ->select('pc.id', 'pc.nombre', 'pc.imagen', 'pc.precio', 
            'pc.descripcion', 'pc.utiliza_nota', 'op.cantidad', 'op.nota', 'op.producto_cate_nego_id')
            ->where('op.id', $request->productoid)
            ->orderBy('op.id', 'ASC')
            ->get();

            $categoria = "";

            foreach($producto as $p){
                $cantidad = $p->cantidad;
                $precio = $p->precio;
                $multi = $cantidad * $precio;
                $p->multiplicado = number_format((float)$multi, 2, '.', '');

                $data = ProductoCategoriaNegocio::where('id', $p->producto_cate_nego_id)->first();
                $datacate = CategoriasNegocio::where('id', $data->categorias_negocio_id)->first();
                $p->categoria = $datacate->nombre;
            }

            return ['success' => 1, 'productos' => $producto];
        }
    }


     // ver historial de encargos realizados
     public function verHistorialDeEncargosFinalizados(Request $request){
        if($request->isMethod('post')){ 
            $reglaDatos = array(
                'id' => 'required', 
                'fecha1' => 'required',
                'fecha2' => 'required'
            );

            $mensajeDatos = array(                                      
                'id.required' => 'El id motorista es requerido.',
                'fecha1.required' => 'La fecha1 es requerido.',
                'fecha2.required' => 'La fecha2 es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

 
            if($p = Propietarios::where('id', $request->id)->first()){

                 //obtener todos los encargos asignados a este servicio
                $datos = EncargoAsignadoServicio::where('servicios_id', $p->servicios_id)->get();

                // obtener todos los id de los encargos
                $pila = array();
                foreach($datos as $p){
                    array_push($pila, $p->encargos_id);
                }

                $start = Carbon::parse($request->fecha1)->startOfDay(); 
                $end = Carbon::parse($request->fecha2)->endOfDay();

                $orden = DB::table('encargo_asignado_servicio AS e')  // encargos_id  servicios_id
                ->join('ordenes_encargo AS o', 'o.encargos_id', '=', 'e.encargos_id')   // encargos_id
                ->select('o.id', 'o.precio_subtotal', 'o.fecha_1', 'o.encargos_id', 'o.revisado')
                ->where('o.estado_1', 1) // orden finalizada
                ->whereIn('e.encargos_id', $pila)
                ->whereBetween('o.fecha_1', [$start, $end]) // fecha finalizado 
                ->get(); 

                foreach($orden as $o){
                   
                    $o->fecha_1 = date("h:i A d-m-Y", strtotime($o->fecha_1));
                                      
                    $o->venta = number_format((float)$o->precio_subtotal, 2, '.', '');

                    $o->nombre = Encargos::where('id', $o->encargos_id)->pluck('nombre')->first();
                }


                $suma = collect($orden)->sum('precio_subtotal');
                $ganado = number_format((float)$suma, 2, '.', '');
                return ['success' => 1, 'ordenes' => $orden, 'vendido' => $ganado];
               
            }else{
                return ['success' => 2];
            }
        }
    }
    

    public function envioNoticacionMotorista($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionMotorista($titulo, $mensaje, $pilaUsuarios);
    }


}
