<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TipoCupon;
use App\Cupones;
use App\CuponDescuentoDinero;
use App\CuponDescuentoPorcentaje;
use App\CuponEnvioServicios;
use App\CuponEnvioZonas;
use App\CuponProductoGratis;
use App\OrdenesCupones;
use App\CuponDescuentoDineroServicios;
use App\CuponDescuentoPorcentajeServicios;
use App\CuponEnvioDinero;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Instituciones;
use App\CuponDonacion;

class CuponesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    // lista de tipo de cupones
    public function indextipo(){
        return view('backend.paginas.cupones.envio.listacuponestipo');
    } 
 
    // tabla lista de tipos de cupones
    public function tablatipocupones(){
         
        $tipo = DB::table('tipo_cupon')
        ->select('id', 'nombre', 'descripcion')
        ->get();
 
        return view('backend.paginas.cupones.tablas.envio.tablatipocupones', compact('tipo'));
    } 
 
    // ver cupones
    public function indexcuponesenviogratis(){
        
        // lista de zonas
        $zonas = DB::table('zonas')->select('id', 'identificador')->get();
        
        // lista de servicios
        $servicios = DB::table('servicios')->select('id', 'identificador')->get();

        return view('backend.paginas.cupones.envio.listacuponesenviogratis', compact('zonas', 'servicios'));
    } 
 
    // tabla lista de cupones
    public function tablacuponesenviogratis(){         
        $cupones = DB::table('cupones')
        ->where('tipo_cupon_id', 1) // solo de envio gratis
        ->get();

        return view('backend.paginas.cupones.tablas.envio.tablacuponesenviogratis', compact('cupones'));
    } 

    // lista de cupones de descuento de dinero
    public function indexDescuentoD(){
        $servicios = DB::table('servicios')->select('id', 'identificador')->get();

        return view('backend.paginas.cupones.descuentod.listacuponesdescuentod', compact('servicios'));

    }

    // tabla lista de cupones para descuento de dinero
    public function tablaDescuentoD(){
        $cupones = DB::table('cupones AS c')
        ->join('c_descuento_dinero AS d', 'd.cupones_id', '=', 'c.id')
        ->select('c.id', 'd.aplica_envio_gratis', 'c.fecha', 'c.activo', 'c.texto_cupon', 'c.uso_limite', 'c.contador')
        ->where('c.tipo_cupon_id', 2) // solo de descuento de dinero
        ->get();

        return view('backend.paginas.cupones.tablas.descuentod.tablacupondescuentod', compact('cupones'));
    }

    // lista de cupones de descuento de porcentaje
    public function indexDescuentoP(){
        $servicios = DB::table('servicios')->select('id', 'identificador')->get();

        return view('backend.paginas.cupones.descuentop.listacuponesdescuentop', compact('servicios'));

    }

    // tabla lista de cupones para descuento de porcentaje
    public function tablaDescuentoP(){
        $cupones = DB::table('cupones AS c')
        ->join('c_descuento_porcentaje AS d', 'd.cupones_id', '=', 'c.id')
        ->select('c.id', 'c.fecha', 'c.activo', 'd.dinero', 'c.texto_cupon', 'c.uso_limite', 'c.contador', 'd.porcentaje')
        ->where('c.tipo_cupon_id', 3) // solo de descuento de porcentaje
        ->get();

        return view('backend.paginas.cupones.tablas.descuentop.tablacupondescuentop', compact('cupones'));
    }

    // lista de cupones de productos gratis
    public function indexProducto(){
        $servicios = DB::table('servicios')->select('id', 'identificador')->get();

        return view('backend.paginas.cupones.progratis.listacuponesprogratis', compact('servicios'));

    }

    // tabla lista de cupones para producto gratis
    public function tablaProducto(){
        $cupones = DB::table('cupones AS c')
        ->join('c_producto_gratis AS p', 'p.cupones_id', '=', 'c.id')
        ->select('c.id', 'c.fecha', 'c.activo', 'p.dinero_carrito', 'p.nombre', 'c.texto_cupon', 'c.uso_limite', 'c.contador')
        ->where('c.tipo_cupon_id', 4) // solo de producto gratis
        ->get();

        return view('backend.paginas.cupones.tablas.progratis.tablacuponprogratis', compact('cupones'));
    }

    // lista instituciones
    public function indexInstituciones(){        
        return view('backend.paginas.cupones.instituciones.listainstituciones');
    }

    // tabla lista instituciones
    public function tablaInstituciones(){
        $instituciones = DB::table('instituciones')->get();

        return view('backend.paginas.cupones.tablas.instituciones.tablainstituciones', compact('instituciones'));
    }

    // lista donaciones
    public function indexDonacion(){     
        $instituciones = DB::table('instituciones')->get();        

        return view('backend.paginas.cupones.donacion.listadonacion', compact('instituciones'));
    }

    // tabla lista de donaciones
    public function tablaDonacion(){
        $donacion = DB::table('cupones AS c')
        ->join('c_donacion AS d', 'd.cupones_id', '=', 'c.id')
        ->join('instituciones AS i', 'i.id', '=', 'd.instituciones_id')
        ->select('c.id', 'c.fecha', 'c.activo', 'c.ilimitado', 'c.texto_cupon',
         'c.uso_limite', 'c.contador', 'i.nombre AS institucion', 'd.dinero')
        ->where('c.tipo_cupon_id', 5) // cupon donacion
        ->get();

        return view('backend.paginas.cupones.tablas.donacion.tabladonacion', compact('donacion'));
    }

    //******** */
    // nuevo cupon para envio gratis
    public function nuevoCuponEnvioGratis(Request $request){

        if($request->isMethod('post')){   
            $rules = array( 
                'textocupon' => 'required',
                'usolimite' => 'required',
                'dinero' => 'required'         
            );

            $messages = array(   
                'textocupon.required' => 'El texto cupon es requerido',
                'usolimite.required' => 'Uso limite es requerido',
                'dinero.required' => 'Dinero es requerido'                     
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            } 

            // buscar si ya existe el cupon
            if(Cupones::where('texto_cupon', $request->textocupon)->first()){
                return ['success' => 2];
            }

            $fecha = Carbon::now('America/El_Salvador');

            DB::beginTransaction();
           
            try {
                
                // guardar nuevo cupon tipo envio gratis
                $c = new Cupones();
                $c->tipo_cupon_id = 1;
                $c->texto_cupon = $request->textocupon;
                $c->uso_limite = $request->usolimite;
                $c->contador = 0;
                $c->fecha = $fecha;
                $c->activo = 1;
                $c->ilimitado = 0;
                if($c->save()){    
                    
                    $ced = new CuponEnvioDinero;
                    $ced->cupones_id = $c->id;
                    $ced->dinero = $request->dinero;
                    $ced->save();
                                      
                    // recorrer cada uno para actualizar
                    foreach($request->idzonas as $z){
                        $cez = new CuponEnvioZonas();
                        $cez->cupones_id = $c->id;
                        $cez->zonas_id = $z;
                        $cez->save();
                    }  
                    
                    // recorrer cada uno para actualizar
                    foreach($request->idservicios as $s){
                        $ces = new CuponEnvioServicios();
                        $ces->cupones_id = $c->id;
                        $ces->servicios_id = $s;
                        $ces->save();
                    }

                    DB::commit();
                    return ['success' => 3];

                }else{
                    return ['success' => 1];
                }
               
            } catch(\Throwable $e){
                DB::rollback();
                return ['success' => 1];
            }
        } 
    }

    public function cuponInformacion(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required'                                
            );

            $messages = array(   
                'id.required' => 'ID es requerido'               
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if($c = Cupones::where('id', $request->id)->first()){                
                return ['success' => 1, 'info' => $c];
            }else{
                return ['success' => 2];
            }            
        } 
    }
   
    public function editarInformacion(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'texto' => 'required',
                'limite' => 'required'                        
            );

            $messages = array(   
                'id.required' => 'ID es requerido',
                'texto.required' => 'El texto es requerido',
                'limite.required' => 'limite de cupon es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if(Cupones::where('id', $request->id)->first()){

                if(Cupones::where('texto_cupon', $request->texto)->where('id', '!=', $request->id)->first()){
                    return ['success' => 2];
                }

                // actualizar informacion del cupon
                Cupones::where('id', $request->id)->update([
                    'texto_cupon' => $request->texto,
                    'uso_limite' => $request->limite
                    ]);

                return ['success' => 3];
            }else{
                return ['success' => 1];
            }            
        } 
    }

    // desactivar un cupon
    public function desactivarCupon(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required'                                  
            );

            $messages = array(   
                'id.required' => 'ID es requerido'               
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if(Cupones::where('id', $request->id)->first()){

                Cupones::where('id', $request->id)->update([
                    'activo' => 0]);


                return ['success' => 1];
            }else{
                return ['success' => 2];
            }            
        } 
    }

    // activar un cupon
    public function activarCupon(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required'                                  
            );

            $messages = array(   
                'id.required' => 'ID es requerido'               
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if($c = Cupones::where('id', $request->id)->first()){

                $limite = $c->uso_limite;
                $contador = $c->contador;

                if($contador >= $limite){
                    return ['success' => 1]; // no se puede activar
                }

                Cupones::where('id', $request->id)->update([
                    'activo' => 1]);


                return ['success' => 2];
            }else{
                return ['success' => 3];
            }            
        } 
    }

    public function vistaEnvioGratis($id){        
        $dinero = DB::table('c_envio_dinero')
        ->where('cupones_id', $id)
        ->pluck('dinero')
        ->first();

        $zonas = DB::table('zonas')->get();
        $servicios = DB::table('servicios')->get();

        return view('backend.paginas.cupones.envio.listavistaenviogratis', compact('id', 'dinero', 'zonas', 'servicios'));
    }

    // carga la tabla para ver las zonas que da envio gratis el cupon
    public function tablaZonasEnvioGratis($id){
        $zonas = DB::table('zonas AS z')
        ->join('c_envio_zonas AS c', 'c.zonas_id', '=', 'z.id')
        ->select('c.id', 'z.nombre', 'z.identificador')
        ->where('cupones_id', $id)
        ->get();

        return view('backend.paginas.cupones.tablas.envio.tablazonasenviogratis', compact('zonas'));
    }

    // carga la tabla para ver los servicios que da envio gratis el cupon
    public function tablaServiciosEnvioGratis($id){
        $servicios = DB::table('servicios AS s')
        ->join('c_envio_servicios AS cs', 'cs.servicios_id', '=', 's.id')
        ->select('cs.id', 's.nombre', 's.identificador')
        ->where('cupones_id', $id)
        ->get();

        return view('backend.paginas.cupones.tablas.envio.tablaserviciosenviogratis', compact('servicios'));
    }

    // actualizar minimo de dinero para aplicar el cupon de envio gratis
    public function actualizarMinimoEnvioGratis(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'dinero' => 'required'                                
            );

            $messages = array(   
                'id.required' => 'ID es requerido',
                'dinero.required' => 'Dinero es requerido'              
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if(CuponEnvioDinero::where('cupones_id', $request->id)->first()){

                // actualizar informacion del cupon
                CuponEnvioDinero::where('cupones_id', $request->id)->update([
                    'dinero' => $request->dinero                    
                    ]);

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }            
        } 
    }

    // borrar servicio del cupon de envio gratis
    public function borrarServicioDeEnvio(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required'
            );

            $messages = array(   
                'id.required' => 'ID es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }
           
            if(CuponEnvioServicios::where('id', $request->id)->first()){

                // actualizar informacion del cupon
                CuponEnvioServicios::where('id', $request->id)->delete();

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }              
        } 
    }

    // borrar zona del cupon de envio gratis
    public function borrarZonaDeEnvio(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required'
            );

            $messages = array(   
                'id.required' => 'ID es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if(CuponEnvioZonas::where('id', $request->id)->first()){

                // actualizar informacion del cupon
                CuponEnvioZonas::where('id', $request->id)->delete();

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }            
        } 
    }

    public function nuevaZonaEnvio(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'idcupon' => 'required'
            );

            $messages = array(   
                'id.required' => 'ID es requerido',
                'idcupon.required' => 'ID cupon es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }


            if(CuponEnvioZonas::where('cupones_id', $request->idcupon)->where('zonas_id', $request->id)->first()){
                return ['success' => 1];
            }

            $c = new CuponEnvioZonas();
            $c->cupones_id = $request->idcupon;
            $c->zonas_id = $request->id;           
            if($c->save()){
                return ['success' => 2];
            }else{
                return ['success' => 3];
            }
                   
        } 
    }

    public function nuevoServicioEnvio(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'idcupon' => 'required'
            );

            $messages = array(   
                'id.required' => 'ID es requerido',
                'idcupon.required' => 'ID cupon es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if(CuponEnvioServicios::where('cupones_id', $request->idcupon)->where('servicios_id', $request->id)->first()){
                return ['success' => 1];
            }

            $c = new CuponEnvioServicios();
            $c->cupones_id = $request->idcupon;
            $c->servicios_id = $request->id;           
            if($c->save()){
                return ['success' => 2];
            }else{
                return ['success' => 3];
            }                   
        } 
    }

    public function vistaUsosGeneral($id){   
        return view('backend.paginas.cupones.envio.listausosgeneral', compact('id'));
    }

    public function tablaVistaUsosGeneral($id){

        //$tipo = Cupones::where('id', $id)->pluck('tipo_cupon_id')->first();
            
        $orden = DB::table('ordenes AS o')
        ->join('servicios AS s', 's.id', '=', 'o.servicios_id')
        ->join('ordenes_cupones AS oc', 'oc.ordenes_id', '=', 'o.id')
        ->join('cupones AS c', 'c.id', '=', 'oc.cupones_id')
        ->select('o.id', 's.nombre', 's.identificador', 'o.fecha_orden')
        ->where('c.id', $id)
        ->get();

        foreach($orden as $o){           
            $o->fecha_orden = date("d-m-Y h:i A", strtotime($o->fecha_orden));
        }

        return view('backend.paginas.cupones.tablas.envio.tablausosgeneral', compact('orden'));
    }
    
    // nuevo cupon para descuento de dinero
    public function nuevoCuponDescuentoD(Request $request){

        if($request->isMethod('post')){   
            $rules = array( 
                'textocupon' => 'required',
                'usolimite' => 'required',
                'dinero' => 'required',
                'aplica' => 'required'      
            );

            $messages = array(   
                'textocupon.required' => 'El texto cupon es requerido',
                'usolimite.required' => 'Uso limite es requerido',
                'dinero.required' => 'Dinero es requerido',
                'aplica.required' => 'Aplica es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            } 

            // buscar si ya existe el cupon
            if(Cupones::where('texto_cupon', $request->textocupon)->first()){
                return ['success' => 2];
            }

            $fecha = Carbon::now('America/El_Salvador');

            DB::beginTransaction();
           
            try { 
                
                // guardar nuevo cupon descuento dinero
                $c = new Cupones();
                $c->tipo_cupon_id = 2;
                $c->texto_cupon = $request->textocupon;
                $c->uso_limite = $request->usolimite;
                $c->contador = 0;
                $c->fecha = $fecha;
                $c->activo = 1;
                $c->ilimitado = 0;
                if($c->save()){

                    $d = new CuponDescuentoDinero();
                    $d->cupones_id = $c->id;
                    $d->dinero = $request->dinero;
                    $d->aplica_envio_gratis = $request->aplica;
                    $d->save();                                      
                                       
                    // recorrer cada uno para agregar
                    foreach($request->idservicios as $s){
                        $ces = new CuponDescuentoDineroServicios();
                        $ces->cupones_id = $c->id;
                        $ces->servicios_id = $s;
                        $ces->save();
                    }

                    DB::commit();
                    return ['success' => 3];

                }else{
                    return ['success' => 1];
                }
               
            } catch(\Throwable $e){
                DB::rollback();
                return ['success' => 1];
            }
        } 
    }
 
    public function cuponInfoDescuentoD(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required'                                
            );

            $messages = array(   
                'id.required' => 'ID es requerido'               
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if(Cupones::where('id', $request->id)->first()){  
               
                $cupon = DB::table('cupones AS c')
                ->join('c_descuento_dinero AS d', 'd.cupones_id', '=', 'c.id')
                ->select('c.id', 'c.texto_cupon', 'c.uso_limite', 'd.dinero')
                ->where('c.id', $request->id)
                ->first();
                
                return ['success' => 1, 'info' => $cupon];
            }else{
                return ['success' => 2];
            }            
        }
    }

    // editar cupon de descuento de dinero
    public function editarDescuentoD(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'texto' => 'required',
                'limite' => 'required',
                'dinero' => 'required'                        
            );

            $messages = array(   
                'id.required' => 'ID es requerido',
                'texto.required' => 'El texto es requerido',
                'limite.required' => 'limite de cupon es requerido',
                'dinero.required' => 'dinero de cupon es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if(Cupones::where('id', $request->id)->first()){

                if(Cupones::where('texto_cupon', $request->texto)->where('id', '!=', $request->id)->first()){
                    return ['success' => 2];
                }

                // actualizar informacion del cupon
                Cupones::where('id', $request->id)->update([
                    'texto_cupon' => $request->texto,
                    'uso_limite' => $request->limite
                    ]);

                CuponDescuentoDinero::where('cupones_id', $request->id)->update([
                    'dinero' => $request->dinero
                    ]);

                return ['success' => 3];
            }else{
                return ['success' => 1];
            }            
        } 
    }

    public function vistaDescuentoD($id){
           
        $dinero = DB::table('c_descuento_dinero')
        ->where('cupones_id', $id)
        ->pluck('dinero') 
        ->first();

        $servicios = DB::table('servicios')->get();

        return view('backend.paginas.cupones.descuentod.listavistadescuentod', compact('id', 'dinero', 'servicios'));
    }

    public function tablaServicioDescuentoD($id){
        $servicios = DB::table('servicios AS s')
        ->join('c_descuento_dinero_servicios AS cs', 'cs.servicios_id', '=', 's.id')
        ->select('cs.id', 's.nombre', 's.identificador')
        ->where('cupones_id', $id)
        ->get();

        return view('backend.paginas.cupones.tablas.descuentod.tablaserviciosdescuentod', compact('servicios'));
    }
        
    public function nuevaServicioDescuentoD(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'idcupon' => 'required'
            );

            $messages = array(   
                'id.required' => 'ID es requerido',
                'idcupon.required' => 'ID cupon es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if(CuponDescuentoDineroServicios::where('cupones_id', $request->idcupon)->where('servicios_id', $request->id)->first()){
                return ['success' => 1];
            }

            $c = new CuponDescuentoDineroServicios();
            $c->cupones_id = $request->idcupon;
            $c->servicios_id = $request->id;
            if($c->save()){
                return ['success' => 2];
            }else{
                return ['success' => 3];
            }
                   
        } 
    }
    
    // borrar servicio del cupon de descuento dinero
    public function borrarServicioDescuentoD(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required'
            );

            $messages = array(   
                'id.required' => 'ID es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }
           
            if(CuponDescuentoDineroServicios::where('id', $request->id)->first()){

                // actualizar informacion del cupon
                CuponDescuentoDineroServicios::where('id', $request->id)->delete();

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }              
        } 
    }

    // actualia dinero de descuento de dinero
    public function actualizarDescuentoD(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'dinero' => 'required'                                
            );

            $messages = array(   
                'id.required' => 'ID es requerido',
                'dinero.required' => 'Dinero es requerido'              
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if(CuponDescuentoDinero::where('cupones_id', $request->id)->first()){

                // actualizar informacion del cupon
                CuponDescuentoDinero::where('cupones_id', $request->id)->update([
                    'dinero' => $request->dinero                    
                    ]);

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }            
        } 
    }

    // nuevo cupon para descuento de porcentaje
    public function nuevoCuponDescuentoP(Request $request){

        if($request->isMethod('post')){   
            $rules = array( 
                'textocupon' => 'required',
                'usolimite' => 'required',
                'dinero' => 'required',
                'porcentaje' => 'required'                  
            );

            $messages = array(   
                'textocupon.required' => 'El texto cupon es requerido',
                'usolimite.required' => 'Uso limite es requerido',
                'dinero.required' => 'Dinero es requerido',
                'porcentaje.required' => 'Porcentaje es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            } 

            // buscar si ya existe el cupon
            if(Cupones::where('texto_cupon', $request->textocupon)->first()){
                return ['success' => 2];
            }

            $fecha = Carbon::now('America/El_Salvador');

            DB::beginTransaction();
           
            try { 
                
                // guardar nuevo cupon descuento porcentaje
                $c = new Cupones();
                $c->tipo_cupon_id = 3;
                $c->texto_cupon = $request->textocupon;
                $c->uso_limite = $request->usolimite;
                $c->contador = 0;
                $c->fecha = $fecha;
                $c->activo = 1;
                $c->ilimitado = 0;
                if($c->save()){

                    $d = new CuponDescuentoPorcentaje();
                    $d->cupones_id = $c->id;
                    $d->dinero = $request->dinero;
                    $d->porcentaje = $request->porcentaje;
                    $d->save();                                      
                                        
                    // recorrer cada uno para agregar
                    foreach($request->idservicios as $s){
                        $ces = new CuponDescuentoPorcentajeServicios();
                        $ces->cupones_id = $c->id;
                        $ces->servicios_id = $s;
                        $ces->save();
                    }

                    DB::commit();
                    return ['success' => 3];

                }else{
                    return ['success' => 1];
                }
               
            } catch(\Throwable $e){
                DB::rollback();
                return ['success' => 1];
            }
        } 
    }

    // editar cupon de descuento de porcentaje
    public function editarDescuentoP(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'texto' => 'required',
                'limite' => 'required',
                'dinero' => 'required',
                'porcentaje' => 'required'                        
            );

            $messages = array(   
                'id.required' => 'ID es requerido',
                'texto.required' => 'El texto es requerido',
                'limite.required' => 'limite de cupon es requerido',
                'dinero.required' => 'dinero de cupon es requerido',
                'porcentaje.required' => 'porcentaje de cupon es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if(Cupones::where('id', $request->id)->first()){

                if(Cupones::where('texto_cupon', $request->texto)->where('id', '!=', $request->id)->first()){
                    return ['success' => 2];
                }

                // actualizar informacion del cupon
                Cupones::where('id', $request->id)->update([
                    'texto_cupon' => $request->texto,
                    'uso_limite' => $request->limite
                    ]);
 
                    CuponDescuentoPorcentaje::where('cupones_id', $request->id)->update([
                    'dinero' => $request->dinero,
                    'porcentaje' => $request->porcentaje
                    ]);

                return ['success' => 3];
            }else{
                return ['success' => 1];
            }            
        } 
    }

    public function cuponInfoDescuentoP(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required'                                
            );

            $messages = array(   
                'id.required' => 'ID es requerido'               
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if(Cupones::where('id', $request->id)->first()){  
               
                $cupon = DB::table('cupones AS c')
                ->join('c_descuento_porcentaje AS p', 'p.cupones_id', '=', 'c.id')
                ->select('c.id', 'c.texto_cupon', 'c.uso_limite', 'p.dinero', 'p.porcentaje')
                ->where('c.id', $request->id)
                ->first();
                
                return ['success' => 1, 'info' => $cupon];
            }else{
                return ['success' => 2];
            }            
        }
    }

    public function vistaDescuentoP($id){
           
        $datos = DB::table('c_descuento_porcentaje')
        ->where('cupones_id', $id)
        ->first();

        $dinero = $datos->dinero;
        $porcentaje = $datos->porcentaje;

        $servicios = DB::table('servicios')->get();

        return view('backend.paginas.cupones.descuentop.listavistadescuentop', compact('id', 'dinero', 'servicios', 'porcentaje'));
    }

    public function tablaServicioDescuentoP($id){
        $servicios = DB::table('servicios AS s')
        ->join('c_descuento_porcentaje_servicios AS cs', 'cs.servicios_id', '=', 's.id')
        ->select('cs.id', 's.nombre', 's.identificador')
        ->where('cupones_id', $id)
        ->get();

        return view('backend.paginas.cupones.tablas.descuentop.tablaserviciosdescuentop', compact('servicios'));
    }

    // actualiza porcentaje y dinero
    public function actualizarDescuentoP(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'dinero' => 'required',
                'porcentaje' => 'required'                                
            );

            $messages = array(   
                'id.required' => 'ID es requerido',
                'dinero.required' => 'Dinero es requerido',
                'porcentaje.required' => 'Porcentaje es requerido'         
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if(CuponDescuentoPorcentaje::where('cupones_id', $request->id)->first()){

                // actualizar informacion del cupon
                CuponDescuentoPorcentaje::where('cupones_id', $request->id)->update([
                    'dinero' => $request->dinero,
                    'porcentaje' => $request->porcentaje                  
                    ]);

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }            
        } 
    }

    public function nuevaServicioDescuentoP(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'idcupon' => 'required'
            );

            $messages = array(   
                'id.required' => 'ID es requerido',
                'idcupon.required' => 'ID cupon es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if(CuponDescuentoPorcentajeServicios::where('cupones_id', $request->idcupon)
            ->where('servicios_id', $request->id)->first()){
                return ['success' => 1];
            }

            $c = new CuponDescuentoPorcentajeServicios();
            $c->cupones_id = $request->idcupon;
            $c->servicios_id = $request->id;
            if($c->save()){
                return ['success' => 2];
            }else{
                return ['success' => 3];
            }
                   
        } 
    }
    
    // borrar servicio del cupon de descuento porcentaje
    public function borrarServicioDescuentoP(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required'
            );

            $messages = array(   
                'id.required' => 'ID es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }
           
            if(CuponDescuentoPorcentajeServicios::where('id', $request->id)->first()){

                // actualizar informacion del cupon
                CuponDescuentoPorcentajeServicios::where('id', $request->id)->delete();

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }              
        } 
    }

    // nuevo cupon para producto gratis
    public function nuevoCuponProGratis(Request $request){

        if($request->isMethod('post')){   
            $rules = array( 
                'textocupon' => 'required',
                'usolimite' => 'required',
                'dinero' => 'required',
                'idservicios' => 'required',
                'producto' => 'required'                  
            );

            $messages = array(   
                'textocupon.required' => 'El texto cupon es requerido',
                'usolimite.required' => 'Uso limite es requerido',
                'dinero.required' => 'Dinero es requerido',
                'idservicios.required' => 'id servicio es requerido',
                'producto.required' => 'nombre producto es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            } 

            // buscar si ya existe el cupon
            if(Cupones::where('texto_cupon', $request->textocupon)->first()){
                return ['success' => 2];
            }

            $fecha = Carbon::now('America/El_Salvador');

            DB::beginTransaction();
           
            try { 

                // guardar nuevo cupon tipo envio gratis
                $c = new Cupones();
                $c->tipo_cupon_id = 4;
                $c->texto_cupon = $request->textocupon;
                $c->uso_limite = $request->usolimite;
                $c->contador = 0;
                $c->fecha = $fecha;
                $c->activo = 1;
                $c->ilimitado = 0;
                if($c->save()){

                    $d = new CuponProductoGratis();
                    $d->cupones_id = $c->id;
                    $d->servicios_id = $request->idservicios;
                    $d->dinero_carrito = $request->dinero;
                    $d->nombre = $request->producto;
                    $d->save();                                      
                   
                    DB::commit();
                    return ['success' => 3];

                }else{
                    return ['success' => 1];
                }
               
            } catch(\Throwable $e){
                DB::rollback();
                return ['success' => 1];
            }
        } 
    }

    public function cuponInfoProGratis(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required'                                
            );

            $messages = array(   
                'id.required' => 'ID es requerido'               
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            } 

            if(Cupones::where('id', $request->id)->first()){  
               
                $cupon = DB::table('cupones AS c')
                ->join('c_producto_gratis AS p', 'p.cupones_id', '=', 'c.id')
                ->join('servicios AS s', 's.id', '=', 'p.servicios_id')
                ->select('c.id', 'c.texto_cupon', 'c.uso_limite', 
                'p.dinero_carrito', 'p.nombre', 's.nombre AS nombreservicio', 's.identificador')
                ->where('c.id', $request->id)
                ->first();
                
                return ['success' => 1, 'info' => $cupon];
            }else{
                return ['success' => 2];
            }            
        }
    }

    // editar cupon de producto gratis
    public function editarProGratis(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'texto' => 'required',
                'limite' => 'required',
                'dinero' => 'required',
                'producto' => 'required'                        
            );

            $messages = array(   
                'id.required' => 'ID es requerido',
                'texto.required' => 'El texto es requerido',
                'limite.required' => 'limite de cupon es requerido',
                'dinero.required' => 'dinero de cupon es requerido',
                'producto.required' => 'producto gratis de cupon es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if(Cupones::where('id', $request->id)->first()){

                if(Cupones::where('texto_cupon', $request->texto)->where('id', '!=', $request->id)->first()){
                    return ['success' => 2];
                }

                // actualizar informacion del cupon
                Cupones::where('id', $request->id)->update([
                    'texto_cupon' => $request->texto,
                    'uso_limite' => $request->limite
                    ]);
 
                    CuponProductoGratis::where('cupones_id', $request->id)->update([
                    'dinero_carrito' => $request->dinero,
                    'nombre' => $request->producto
                    ]);

                return ['success' => 3];
            }else{
                return ['success' => 1];
            }            
        } 
    }


    public function nuevaInstitucion(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'nombre' => 'required',
            );

            $messages = array(   
                'nombre.required' => 'El nombre es requerido',
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            } 

            // nueva institucion
            $c = new Instituciones();
            $c->nombre = $request->nombre;
            if($c->save()){
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }

           
        } 
    }

    public function infoInstitucion(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required'                             
            );

            $messages = array(   
                'id.required' => 'El id es requerido'                
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            } 

            if($info = Instituciones::where('id', $request->id)->first()){  
                              
                return ['success' => 1, 'info' => $info];
            }else{
                return ['success' => 2];
            }             
        } 
    }

    public function editarInstitucion(Request $request){
        if($request->isMethod('post')){   
            $rules = array(
                'id' => 'required',
                'nombre' => 'required',
                                
            );

            $messages = array(   
                'id.required' => 'El id es requerido',
                'nombre.required' => 'El nombre es requerido',
                                
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            } 

            if(Instituciones::where('id', $request->id)->first()){

                // actualizar informacion del cupon
                Instituciones::where('id', $request->id)->update([
                    'nombre' => $request->nombre
                   
                    ]);

                return ['success' => 1];
            }else{
                return ['success' => 2];
            } 
           
        } 
    }

    // nuevo cupon para donacion
    public function nuevaDonacion(Request $request){

        if($request->isMethod('post')){   
            $rules = array( 
                'textocupon' => 'required',
                'usolimite' => 'required',
                'donacion' => 'required',
                'institucionid' => 'required',
                'descripcion' => 'required'                 
            );

            $messages = array(   
                'textocupon.required' => 'El texto cupon es requerido',
                'usolimite.required' => 'Uso limite es requerido',
                'donacion.required' => 'Donacion es requerido',
                'institucionid.required' => 'id institucion es requerido',
                'descripcion.required' => 'descripcion es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            } 

            // buscar si ya existe el cupon
            if(Cupones::where('texto_cupon', $request->textocupon)->first()){
                return ['success' => 2];
            }

            $fecha = Carbon::now('America/El_Salvador');

            DB::beginTransaction();
           
            try { 

                // guardar nuevo cupon tipo envio gratis
                $c = new Cupones();
                $c->tipo_cupon_id = 5;
                $c->texto_cupon = $request->textocupon;
                $c->uso_limite = $request->usolimite;
                $c->contador = 0;
                $c->fecha = $fecha;
                $c->activo = 1;
                $c->ilimitado = 0;
                if($c->save()){

                    $d = new CuponDonacion();
                    $d->cupones_id = $c->id;
                    $d->instituciones_id = $request->institucionid;
                    $d->dinero = $request->donacion;   
                    $d->descripcion = $request->descripcion;              
                    $d->save();                                      
                   
                    DB::commit();
                    return ['success' => 3];

                }else{
                    return ['success' => 1];
                }
               
            } catch(\Throwable $e){
                DB::rollback();
                return ['success' => 1];
            }
        } 
    }

    public function infoDonacion(Request $request){

        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required'                             
            );

            $messages = array(   
                'id.required' => 'El id es requerido'                
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            } 

            if(Cupones::where('id', $request->id)->first()){  

                $info = DB::table('cupones AS c')
                ->join('c_donacion AS d', 'd.cupones_id', '=', 'c.id')
                ->select('c.id', 'c.texto_cupon', 'd.descripcion', 'c.activo', 'c.uso_limite', 'd.dinero', 'c.ilimitado')
                ->where('c.id', $request->id)
                ->first();
                              
                return ['success' => 1, 'info' => $info];
            }else{
                return ['success' => 2];
            }             
        } 
    }

    public function editarDonacion(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'texto' => 'required',
                'limite' => 'required',
                'dinero' => 'required',
                'ilimitado' => 'required',
                'activo' => 'required',
                'descripcion' => 'required'                  
            );

            $messages = array(   
                'id.required' => 'ID es requerido',
                'texto.required' => 'El texto es requerido',
                'limite.required' => 'limite de cupon es requerido',
                'dinero.required' => 'dinero de cupon es requerido',
                'ilimitado.required' => 'ilimitado es requerido',
                'activo.required' => 'activo es requerido',
                'descripcion.required' => 'descripcion es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages);

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if(Cupones::where('id', $request->id)->first()){

                if(Cupones::where('texto_cupon', $request->texto)->where('id', '!=', $request->id)->first()){
                    return ['success' => 2];
                }

                // actualizar informacion del cupon
                Cupones::where('id', $request->id)->update([
                    'texto_cupon' => $request->texto,
                    'uso_limite' => $request->limite,
                    'ilimitado' => $request->ilimitado,
                    'activo' => $request->activo
                    ]);
 
                    CuponDonacion::where('cupones_id', $request->id)->update([
                    'dinero' => $request->dinero,
                    'descripcion' => $request->descripcion                 
                    ]);

                return ['success' => 3];
            }else{
                return ['success' => 1];
            }            
        } 
    }


}
