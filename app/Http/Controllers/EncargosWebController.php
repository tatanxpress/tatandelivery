<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use App\CarritoEncargo;
use App\CarritoEncargoProducto;
use App\Direccion;
use App\Encargos;
use App\EncargosZona;
use App\OrdenesEncargo;
use App\OrdenesEncargoProducto;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\NegociosEncargo;
use App\CategoriasNegocio;
use App\ProductoCategoriaNegocio;
use App\ListaEncargo;
use App\ListaProductoEncargo;
use App\Zonas;
use App\OrdenesEncargoDireccion;
use App\MotoristaOrdenEncargo;
use App\MotoristaEncargoAsignado;
use App\Motoristas;
use App\EncargoAsignadoServicio; 
use App\Servicios;
use OneSignal;



class EncargosWebController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    // vista de encargos
    public function verListaActivos(){

        $servicios = Servicios::all();

        return view('backend.paginas.encargos.listaencargos', compact('servicios'));
    }

    // vista tabla de encargos
    public function verListaActivosTabla(){
       
        $encargos = DB::table('encargos')
        ->where('activo', 1)
        ->orderBy('id', 'DESC')
        ->get();
 
        foreach($encargos as $e){
            $e->fecha_inicia = date("d-m-Y", strtotime($e->fecha_inicia));
            $e->fecha_finaliza = date("d-m-Y h:i A", strtotime($e->fecha_finaliza));

            // buscar servicio asignado a este encargo
            $tengo = 0;
            $servicio = "";

            if($aa = EncargoAsignadoServicio::where('encargos_id', $e->id)->first()){
                $tengo = 1;
                $servicio = Servicios::where('id', $aa->servicios_id)->pluck('identificador')->first();
            }

            $e->tengo = $tengo;
            $e->servicio = $servicio;
        }
 
        return view('backend.paginas.encargos.tablas.tablaencargos', compact('encargos'));
    }

    public function verListaFinalizado(){

        $servicios = Servicios::all();

        return view('backend.paginas.encargos.listaencargosfinalizo', compact('servicios'));
    }

    public function verListaFinalizadoTabla(){
        $encargos = DB::table('encargos')
        ->where('activo', 0)
        ->orderBy('id', 'DESC')
        ->get();
 
        foreach($encargos as $e){
            $e->fecha_inicia = date("d-m-Y", strtotime($e->fecha_inicia));
            $e->fecha_finaliza = date("d-m-Y h:i A", strtotime($e->fecha_finaliza));

            // buscar servicio asignado a este encargo
            $tengo = 0;
            $servicio = "";

            if($aa = EncargoAsignadoServicio::where('encargos_id', $e->id)->first()){
                $tengo = 1;
                $servicio = Servicios::where('id', $aa->servicios_id)->pluck('identificador')->first();
            }

            $e->tengo = $tengo;
            $e->servicio = $servicio;
        }

        return view('backend.paginas.encargos.tablas.tablaencargosfinalizo', compact('encargos'));
    }
 
    public function verListaNegocios(){
        return view('backend.paginas.encargos.listanegocios');
    }

    public function VerListaNegociosTabla(){
        $negocios = DB::table('negocios_encargo')->orderBy('id', 'DESC')->get();

        foreach($negocios as $e){
            $e->fecha = date("d-m-Y h:i A", strtotime($e->fecha));
        }

        return view('backend.paginas.encargos.tablas.tablanegocios', compact('negocios'));
    }

    public function informacionEncargos(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id encargo es requerido.'
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ]; 
            }

            if(Encargos::where('id', $request->id)->first()){

                $encargo = Encargos::where('id', $request->id)->get();

                foreach($encargo as $e){
                    $e->fecha_entrega = date("Y-m-d\TH:i", strtotime($e->fecha_entrega));
                    $e->fecha_finaliza = date("Y-m-d\TH:i", strtotime($e->fecha_finaliza));

                    $e->idservicio = EncargoAsignadoServicio::where('encargos_id', $e->id)->pluck('servicios_id')->first();
                }
               
                $servicios = Servicios::all();

                return ['success' => 1, 'encargo' => $encargo, 'servicios' => $servicios];
            }else{
                return ['success' => 2];
            }
        }
    }
 
    // crear nuevo encargo
    public function nuevoEncargo(Request $request){
       
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'identificador' => 'required',
                'nombre' => 'required',
                'fechainicio' => 'required',
                'fechafin' => 'required',
                'tipovista' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'identificador.required' => 'El identificador es requerido.',
                'nombre.required' => 'El nombre es requerido.',
                'fechainicio.required' => 'La fecha inicio encargo es requerido.',
                'fechafin.required' => 'La fecha fin encargo es requerido.',
                'tipovista.required' => 'El tipo de vista es requerido.',
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if(Encargos::where('identificador', $request->identificador)->first()){
                return ['success' => 1];
            }

            $cadena = Str::random(15);
            $tiempo = microtime();  
            $union = $cadena.$tiempo;
            $nombre = str_replace(' ', '_', $union);
             
            $extension = '.'.$request->imagen->getClientOriginalExtension();
            $nombreFoto = $nombre.strtolower($extension);
            $avatar = $request->file('imagen'); 
            $upload = Storage::disk('listaservicios')->put($nombreFoto, \File::get($avatar)); 
            
            $fecha = Carbon::now('America/El_Salvador');

            if($upload){ 

                DB::beginTransaction();
           
                try {
 
                    $e = new Encargos();
                    $e->identificador = $request->identificador;
                    $e->nombre = $request->nombre;
                    $e->descripcion = $request->descripcion;
                    $e->ingreso = $fecha;
                    $e->fecha_inicia = $request->fechainicio;
                    $e->fecha_finaliza = $request->fechafin;
                    $e->fecha_entrega = $request->fechaentrega;
                    $e->activo = 1;
                    $e->imagen = $nombreFoto;
                    $e->tipo_vista = $request->tipovista;
                    $e->permiso_motorista = 0; // aun el moto asignado no puede ver el encargo
                    $e->vista_cliente = 0; // encargo activo pero no visible al cliente aun
                    $e->visible_propietario = 1; // visible al propietario la tarjeta si esta asignado. 
                                                // una vez complete las ordenes_encargo podra ocultar la tarjeta
                    
                    $e->texto_boton = $request->boton;
                    $e->requiere_nota = $request->checknota;
                    $e->nota_encargo = $request->nota;
                    
                    if($e->save()){
  
                        $n = new EncargoAsignadoServicio();
                        $n->encargos_id = $e->id;
                        $n->servicios_id = $request->servicio;

                        if($n->save()){

                            DB::commit();

                            return ['success' => 2];
                        }else{
                            return ['success' => 3];
                        }                        
                    }else{
                        return ['success' => 3];
                    }

                } catch(\Throwable $e){
                    DB::rollback();
                    return [
                        'success' => 3
                    ];
                }
            }
        }
    } 

    // editar encargo
    public function editarEncargo(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos 
            $reglaDatos = array(
                'id' => 'required',
                'nombre' => 'required',
                'descripcion' => 'required',
                'identificador' => 'required',
                'fechainicio' => 'required',
                'tipovista' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id es requerido.',
                'nombre.required' => 'El nombre es requerido',
                'descripcion.required' => 'descripcion es requerido',
                'identificador.required' => 'identificador es requerido',
                'fechainicio.required' => 'fecha inicio es requerido',
                'fechafin.required' => 'fecha fin es requerido',
                'tipovista.required' => 'tipo de vista es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }


            if($po = Encargos::where('id', $request->id)->first()){

                if(Encargos::where('id', '!=', $request->id)->where('identificador', $request->identificador)->first()){
                    return ['success' => 1];
                }

                if($request->hasFile('imagen')){

                    $cadena = Str::random(15);
                    $tiempo = microtime();
                    $union = $cadena.$tiempo;
                    $nombre = str_replace(' ', '_', $union);
                    
                    $extension = '.'.$request->imagen->getClientOriginalExtension();
                    $nombreFoto = $nombre.strtolower($extension);
                    $avatar = $request->file('imagen'); 
                    $upload = Storage::disk('listaservicios')->put($nombreFoto, \File::get($avatar));

                    if($upload){
                        $imagenOld = $po->imagen;
                        
                        Encargos::where('id', $request->id)->update([
                            'identificador' => $request->identificador,
                            'nombre' => $request->nombre,
                            'descripcion' => $request->descripcion,
                            'fecha_inicia' => $request->fechainicio,
                            'fecha_finaliza' => $request->fechafin,
                            'fecha_entrega' => $request->fechaentrega,
                            'activo' => $request->activo,
                            'imagen' => $nombreFoto,
                            'tipo_vista' => $request->tipovista,
                            'vista_cliente' => $request->vistacliente,
                            'permiso_motorista' => $request->permisomotorista,
                            'visible_propietario' => $request->visiblepropietario,
                            'texto_boton' => $request->boton,
                            'requiere_nota' => $request->checknota,
                            'nota_encargo' => $request->nota
                            ]);

                            // editar servicio, vendra null en encargos finalizados
                            if($request->servicio != null){
                                if(EncargoAsignadoServicio::where('encargos_id', $request->id)->first()){
                                  
                                    EncargoAsignadoServicio::where('encargos_id', $request->id)->update([
                                    'servicios_id' => $request->servicio
                                    ]);
                                } 
                            }
                            
                            
                        if(Storage::disk('listaservicios')->exists($imagenOld)){
                            Storage::disk('listaservicios')->delete($imagenOld);                                
                        } 
                        
                        return ['success' => 2];

                    }else{
                        return ['success' => 3]; // error subir imagen
                    }         
                }else{

                    // solo guardar datos
                        
                    Encargos::where('id', $request->id)->update([
                        'identificador' => $request->identificador,
                        'nombre' => $request->nombre,
                        'descripcion' => $request->descripcion,
                        'fecha_inicia' => $request->fechainicio,
                        'fecha_finaliza' => $request->fechafin,
                        'fecha_entrega' => $request->fechaentrega,
                        'activo' => $request->activo,
                        'tipo_vista' => $request->tipovista,
                        'vista_cliente' => $request->vistacliente,
                        'permiso_motorista' => $request->permisomotorista,
                        'visible_propietario' => $request->visiblepropietario,
                        'texto_boton' => $request->boton,
                        'requiere_nota' => $request->checknota,
                        'nota_encargo' => $request->nota
                        ]);

                        // editar servicio, vendra null en encargos finalizados
                        if($request->servicio != null){
                            if(EncargoAsignadoServicio::where('encargos_id', $request->id)->first()){
                            
                                EncargoAsignadoServicio::where('encargos_id', $request->id)->update([
                                'servicios_id' => $request->servicio
                                ]);
                            }
                        }
                        

                    return ['success' => 2];
                } 

            }else{
                return ['success' => 3];
            }
        }       
    }

    // asignar servicio al encargo o borrarlo
    public function asignarServicioAlEncargo(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos 
            $reglaDatos = array(
                'idencargo' => 'required',
               
            );
        
            $mensajeDatos = array(                                      
                'idencargo.required' => 'El id encargo es requerido.',
                
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            // si se encuentra, se editara o borrar
            if($po = EncargoAsignadoServicio::where('encargos_id', $request->idencargo)->first()){

                    // editar
                    EncargoAsignadoServicio::where('id', $po->id)->update([
                        'servicios_id' => $request->idservicio
                        ]);

                        return ['success' => 2]; // editado
                
            }else{ 
               
                // verificar que no haya ninguno igual
                if(EncargoAsignadoServicio::where('encargos_id', $request->idencargo)->first()){
                    return ['success' => 3]; // ya existe una asignacion
                }

                // crear asignacion
                $e = new EncargoAsignadoServicio();
                $e->encargos_id = $request->idencargo;
                $e->servicios_id = $request->idservicio;
                if($e->save()){
                    return ['success' => 4]; // guardado
                }else{
                    return ['success' => 5]; // error al guardar
                }
            }
        }    
    }



    // activar 
    public function activarEncargo(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'                
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id es requerido.'              
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if(Encargos::where('id', $request->id)->first()){

                Encargos::where('id', $request->id)->update([
                    'activo' => 1 
                    ]);

                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }


    public function nuevoNegocio(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'identificador' => 'required',
                'nombre' => 'required'               
            );
        
            $mensajeDatos = array(                                      
                'identificador.required' => 'El identificador es requerido.',
                'nombre.required' => 'El nombre es requerido.'           
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if(NegociosEncargo::where('identificador', $request->identificador)->first()){
                return ['success' => 1];
            }
            
            $fecha = Carbon::now('America/El_Salvador');

            $e = new NegociosEncargo();
            $e->identificador = $request->identificador;
            $e->nombre = $request->nombre;
            $e->descripcion = $request->descripcion;
            $e->fecha = $fecha;

            if($e->save()){
                return ['success' => 2];
            }{
                return ['success' => 3];
            }           
        }
    }

    public function informacionNegocio(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id encargo es requerido.'
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ]; 
            }

            if($negocio = NegociosEncargo::where('id', $request->id)->first()){

               return ['success' => 1, 'negocio' => $negocio];
            }else{
                return ['success' => 2];
            }
        }
    }

    public function editarNegocio(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required',
                'nombre' => 'required',
                'identificador' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id es requerido.',
                'nombre.required' => 'El nombre es requerido',
                'identificador.required' => 'identificador es requerido'                
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($po = NegociosEncargo::where('id', $request->id)->first()){

                if(NegociosEncargo::where('id', '!=', $request->id)->where('identificador', $request->identificador)->first()){
                    return ['success' => 1];
                }
                     
                NegociosEncargo::where('id', $request->id)->update([
                    'identificador' => $request->identificador,
                    'nombre' => $request->nombre,
                    'descripcion' => $request->descripcion                           
                    ]);

                return ['success' => 2];

            }else{
                return ['success' => 3];
            }
        }  
    }

    public function verCategoriasNegocio($id){

        $d = NegociosEncargo::where('id', $id)->first();

        $nombre = $d->nombre;
        $id = $d->id;

        return view('backend.paginas.encargos.listanegocioscategorias', compact('nombre', 'id'));
    }

    public function verCategoriasNegocioTabla($id){

        $categorias = CategoriasNegocio::where('negocios_encargo_id', $id)->get();
        return view('backend.paginas.encargos.tablas.tablanegocioscategorias', compact('categorias'));
    }

    public function guardarCategoria(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required',
                'nombre' => 'required'               
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id es requerido.',
                'nombre.required' => 'El nombre es requerido.'           
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            $e = new CategoriasNegocio();
            $e->negocios_encargo_id = $request->id;
            $e->nombre = $request->nombre;

            if($e->save()){
                return ['success' => 1];
            }{
                return ['success' => 2];
            }           
        }
    }

    public function informacionCategoria(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id encargo es requerido.'
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ]; 
            }

            if($categoria = CategoriasNegocio::where('id', $request->id)->first()){

               return ['success' => 1, 'categoria' => $categoria];
            }else{
                return ['success' => 2];
            }
        }
    }

    public function editarCategoria(Request $request){
        if($request->isMethod('post')){

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required',
                'nombre' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id es requerido.',
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

            if($po = CategoriasNegocio::where('id', $request->id)->first()){

              
                CategoriasNegocio::where('id', $request->id)->update([
                    'nombre' => $request->nombre
                    ]);

                return ['success' => 1];

            }else{
                return ['success' => 2];
            }
        }  
    }

    public function verProductosCategoriasNegocio($id){

        $d = CategoriasNegocio::where('id', $id)->first();

        $nombre = $d->nombre;
        $id = $d->id;

        return view('backend.paginas.encargos.listanegocioscategoriasproductos', compact('nombre', 'id'));
    }

    public function verProductosCategoriasNegocioTabla($id){

        $productos = ProductoCategoriaNegocio::where('categorias_negocio_id', $id)->get();
        return view('backend.paginas.encargos.tablas.tablanegocioscategoriasproductos', compact('productos'));
    }

    public function informacionProductoCategoria(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id encargo es requerido.'
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ]; 
            }

            if($producto = ProductoCategoriaNegocio::where('id', $request->id)->first()){

               return ['success' => 1, 'producto' => $producto];
            }else{
                return ['success' => 2];
            }
        }
    }

    public function guadarProductoCategoriaNegocio(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required',
                'nombre' => 'required',
                'precio' => 'required'               
            );
        
            $mensajeDatos = array(
                'id.required' => 'El id es requerido.',
                'nombre.required' => 'El nombre es requerido.',
                'precio.required' => 'El precio es requerido.'
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            $cadena = Str::random(20);
            $tiempo = microtime();  
            $union = $cadena.$tiempo;
            $nombre = str_replace(' ', '_', $union);
             
            $extension = '.'.$request->imagen->getClientOriginalExtension();
            $nombreFoto = $nombre.strtolower($extension);
            $avatar = $request->file('imagen'); 
            $upload = Storage::disk('productos')->put($nombreFoto, \File::get($avatar)); 
            
            if($upload){

                $e = new ProductoCategoriaNegocio();
                $e->categorias_negocio_id = $request->id;
                $e->nombre = $request->nombre;                
                $e->imagen = $nombreFoto;
                $e->descripcion = $request->descripcion;
                $e->precio = $request->precio;
                $e->utiliza_nota = $request->cbnota;
                $e->nota = $request->nota;

                if($e->save()){
                    return ['success' => 1];
                }{
                    return ['success' => 2];
                }
            }
        }
    }

    public function editarProductoCategoria(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required',
                'nombre' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id es requerido.',
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

            if($po = ProductoCategoriaNegocio::where('id', $request->id)->first()){
               
                if($request->hasFile('imagen')){

                    $cadena = Str::random(15);
                    $tiempo = microtime();
                    $union = $cadena.$tiempo;
                    $nombre = str_replace(' ', '_', $union);
                    
                    $extension = '.'.$request->imagen->getClientOriginalExtension();
                    $nombreFoto = $nombre.strtolower($extension);
                    $avatar = $request->file('imagen'); 
                    $upload = Storage::disk('productos')->put($nombreFoto, \File::get($avatar));

                    if($upload){
                        $imagenOld = $po->imagen;
                        
                        ProductoCategoriaNegocio::where('id', $request->id)->update([
                            'nombre' => $request->nombre,
                            'imagen' => $nombreFoto,
                            'descripcion' => $request->descripcion,
                            'precio' => $request->precio,
                            'utiliza_nota' => $request->cbnota,
                            'nota' => $request->nota               
                            ]);
                            
                        if(Storage::disk('productos')->exists($imagenOld)){
                            Storage::disk('productos')->delete($imagenOld);                                
                        } 
                        
                        return ['success' => 1];

                    }else{
                        return ['success' => 2]; // error subir imagen
                    }         
                }else{

                    // solo guardar datos
                        
                    ProductoCategoriaNegocio::where('id', $request->id)->update([
                        'nombre' => $request->nombre,
                        'descripcion' => $request->descripcion,
                        'precio' => $request->precio,
                        'utiliza_nota' => $request->cbnota,
                        'nota' => $request->nota             
                        ]);

                    return ['success' => 1];
                } 

            }else{
                return ['success' => 2];
            }
        }      
    }

    public function verListaZonasEncargo($id){
       
        $d = Encargos::where('id', $id)->first();
        $zonas = Zonas::all();

        $nombre = $d->nombre;
        $id = $d->id;

        return view('backend.paginas.encargos.listazonas', compact('nombre', 'id', 'zonas'));
    }

    public function verListaZonasEncargoTabla($id){

        $zona = DB::table('encargos_zona AS e')
        ->join('zonas AS z', 'z.id', '=', 'e.zonas_id')
        ->select('e.id', 'z.identificador', 'e.precio_envio', 'e.ganancia_motorista')
        ->where('e.encargos_id', $id)
        ->orderBy('e.id', 'ASC')
        ->get();
 
        return view('backend.paginas.encargos.tablas.tablalistazonas', compact('zona'));
    }

    public function borrarZonaEncargo(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'
            );
        
            $mensajeDatos = array(
                'id.required' => 'El id encargo es requerido.'
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ]; 
            }

            if(EncargosZona::where('id', $request->id)->first()){

                EncargosZona::where('id', $request->id)->delete();

               return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }

    public function nuevoEncargoZona(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required', // id de encargo
                'idzona' => 'required',
                'precio' => 'required',
                'ganancia' => 'required'             
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id es requerido.',
                'idzona.required' => 'El id zona es requerido.',
                'precio.required' => 'Precio envio de zona es requerido',
                'ganancia.required' => 'Ganancia motorista envio de zona es requerido'      
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            // verificar que no este repetido
            if(EncargosZona::where('encargos_id', $request->id)->where('zonas_id', $request->idzona)->first()){
                return ['success' => 1];
            }

            $e = new EncargosZona();
            $e->encargos_id = $request->id;
            $e->zonas_id = $request->idzona;
            $e->precio_envio = $request->precio;
            $e->ganancia_motorista = $request->ganancia;
            $e->posicion = 1; // por defecto posicion sera 1
          
            if($e->save()){
                return ['success' => 2];
            }{
                return ['success' => 2];
            }           
        }
    }


    public function informacionZonaEncargo(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id es requerido.'
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ]; 
            }

            if($z = EncargosZona::where('id', $request->id)->first()){

                $nombre = Zonas::where('id', $z->zonas_id)->pluck('identificador')->first();
 
               return ['success' => 1, 'zona' => $z, 'nombre' => $nombre];
            }else{
                return ['success' => 2];
            }
        }
    }

    // editar info de zona encargo
    public function editarZonaEncargo(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id es requerido.'
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ]; 
            }

            if(EncargosZona::where('id', $request->id)->first()){

                EncargosZona::where('id', $request->id)->update([
                    'precio_envio' => $request->precioenvio,
                    'ganancia_motorista' => $request->ganancia             
                    ]);

               return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }
    
    public function listadoCategoriasEncargo($id){
       
        $d = Encargos::where('id', $id)->first();
        $nombre = $d->nombre;
        $id = $d->id;

        $negocios = NegociosEncargo::all();

        return view('backend.paginas.encargos.listaencargocategoria', compact('nombre', 'id', 'negocios'));
    }

    public function listadoCategoriasEncargoTabla($id){

        $lista = DB::table('lista_encargo AS le')
        ->join('categorias_negocios AS cn', 'cn.id', '=', 'le.categorias_negocios_id')
        ->select('le.id', 'cn.nombre AS categoria', 'le.activo', 'le.posicion', 'cn.negocios_encargo_id')
        ->where('le.encargos_id', $id)
        ->orderBy('le.posicion', 'ASC')
        ->get();

        foreach($lista as $l){
            $l->negocio = NegociosEncargo::where('id', $l->negocios_encargo_id)->pluck('nombre')->first();
        }


        return view('backend.paginas.encargos.tablas.tablalistaencargocategoria', compact('lista'));
    }

    public function buscadorCategoriaNegocio(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required',                         
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id es requerido.'    
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            $categoria = CategoriasNegocio::where('negocios_encargo_id', $request->id)->get();

            return ['success' => 1, 'categoria' => $categoria];

        }
    }

    public function infoListaEncargoCategoria(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'
            );
        
            $mensajeDatos = array(
                'id.required' => 'El id es requerido.'
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ]; 
            }

            if($info = ListaEncargo::where('id', $request->id)->first()){

               return ['success' => 1, 'info' => $info];
            }else{
                return ['success' => 2];
            }
        }
    }

    public function editarListaEncargo(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'
            );
        
            $mensajeDatos = array(
                'id.required' => 'El id es requerido.'
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ]; 
            }

            if(ListaEncargo::where('id', $request->id)->first()){

                ListaEncargo::where('id', $request->id)->update([
                    'activo' => $request->valor
                    ]);

               return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }


    public function nuevaListaEncargo(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required', // id de encargo
                'idcategoria' => 'required'                       
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id es requerido.',
                'idcategoria.required' => 'El id categoria es requerido.'    
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }


            if(ListaEncargo::where('encargos_id', $request->id)->where('categorias_negocios_id', $request->idcategoria)->first()){
                return ['success' => 1];
            }

            $e = new ListaEncargo();
            $e->categorias_negocios_id = $request->idcategoria;
            $e->encargos_id = $request->id;
            $e->posicion = 1; 
            $e->activo = 0;

            if($e->save()){
                return ['success' => 2];
            }{
                return ['success' => 3];
            }           
        }
    }

    public function verListaDeProductosCategorias($id){

        $data = ListaEncargo::where('id', $id)->first();

        $d = Encargos::where('id', $data->encargos_id)->first();
        $nombre = $d->nombre;

        $pro = ProductoCategoriaNegocio::where('categorias_negocio_id', $data->categorias_negocios_id)->get();

        return view('backend.paginas.encargos.listaproductos', compact('nombre', 'id', 'pro', 'id2'));
    }

    public function verListaDeProductosCategoriasTabla($id){

        $productos = DB::table('lista_producto_encargo AS l')
        ->join('producto_categoria_negocio AS p', 'p.id', '=', 'l.producto_cate_nego_id')
        ->select('l.id', 'p.nombre', 'p.precio', 'l.posicion', 'l.activo')
        ->where('l.lista_encargo_id', $id)
        ->orderBy('l.posicion', 'ASC')
        ->get();

        return view('backend.paginas.encargos.tablas.tablalistaproducto', compact('productos'));
    }


    public function guardarProductoEnLista(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required', // lista_encargo_id
                'id2' => 'required' // producto_cate_nego_id         
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id es requerido.',
                'id2.required' => 'El id2 es requerido.'
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if(ListaProductoEncargo::where('lista_encargo_id', $request->id)->where('producto_cate_nego_id', $request->id2)->first()){
                return ['success' => 1];                
            }

            $e = new ListaProductoEncargo();
            $e->lista_encargo_id = $request->id;
            $e->producto_cate_nego_id = $request->id2;

            if($e->save()){
                return ['success' => 2];
            }{
                return ['success' => 3];
            }           
        }
    }


    public function borrarProductoEnLista(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required'
            );
        
            $mensajeDatos = array(
                'id.required' => 'El id es requerido.'
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ]; 
            }

            if(ListaProductoEncargo::where('id', $request->id)->first()){

                ListaProductoEncargo::where('id', $request->id)->delete();

               return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }

    // ordenar posiciones
    public function ordenarListaCategorias(Request $request){
       

        foreach ($request->order as $order) {    
            
            DB::table('lista_encargo')
                ->where('id', $order['id'])
                ->update(['posicion' => $order['posicion']]);
        }

        return ['success' => 1];
    }

    public function ordenarListaCategoriasProducto(Request $request){
       

        foreach ($request->order as $order) {    
            
            DB::table('lista_producto_encargo')
                ->where('id', $order['id'])
                ->update(['posicion' => $order['posicion']]);
        }
       
        return ['success' => 1];
    }

    /*public function ordenarPosicionEncargosZona(Request $request){
        

        foreach ($request->order as $order) {    
            
            DB::table('encargos_zona')
                ->where('id', $order['id'])
                ->update(['posicion' => $order['posicion']]);
        }
        
       
        return ['success' => 1];
    }*/

    public function verOrdenesEncargoPendientes($id){

        // viene el id encargo
        $nombre = Encargos::where('id', $id)->pluck('nombre')->first();
                
        return view('backend.paginas.encargos.listaordenesencargo', compact('id', 'nombre')); 
    }


    public function verOrdenesEncargoPendientesTabla($id){

        $ordenes = DB::table('ordenes_encargo AS o')
        ->join('ordenes_encargo_direccion AS oe', 'oe.ordenes_encargo_id', '=', 'o.id')
        ->select('o.id', 'oe.nombre', 'o.precio_subtotal', 'o.revisado', 'o.fecha_orden', 'o.mensaje_cancelado')
        ->where('o.encargos_id', $id)
        ->get(); 
 
        foreach($ordenes as $i){
            $i->fecha_orden = date("d-m-Y h:i A", strtotime($i->fecha_orden));
        }
        
        return view('backend.paginas.encargos.tablas.tablalistaordenesencargo', compact('ordenes'));
    }

    public function informacionOrdenesEncargo(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required' // ide de la orden encargo
            );
        
            $mensajeDatos = array(
                'id.required' => 'El id es requerido.'
            );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ]; 
            }  

            if(OrdenesEncargo::where('id', $request->id)->first()){

                $info = DB::table('ordenes_encargo AS o')
                ->join('ordenes_encargo_direccion AS od', 'od.ordenes_encargo_id', '=', 'o.id')
                ->join('zonas AS z', 'z.id', '=', 'od.zonas_id')
                ->select('o.id', 'od.nombre', 'o.precio_subtotal', 'z.identificador', 'z.nombre AS nombrezona',
                        'od.direccion', 'od.numero_casa', 'od.punto_referencia', 'o.users_id',
                        'od.latitud', 'od.longitud', 'od.latitud_real AS latitudreal', 
                        'od.longitud_real AS longitudreal', 'od.revisado')
                ->where('o.id', $request->id)
                ->first();
 
                $telefono = User::where('id', $info->users_id)->pluck('phone')->first();

               return ['success' => 1, 'info' => $info, 'telefono' => $telefono];
            }else{
                return ['success' => 2];
            }
        }
    }

    // latitud y longitud de la orden encargo
    public function mapaGPS($id){
        
        $mapa = OrdenesEncargoDireccion::where('ordenes_encargo_id', $id)->first();
       
        $api = env("API_GOOGLE_MAPS", "");

        $latitud = $mapa->latitud;
        $longitud = $mapa->longitud;  

        return view('backend.paginas.encargos.mapacliente', compact('latitud', 'longitud', 'api'));
    } 

    // latitud real y longitud real de la orden encargo 
    public function mapaGPS2($id){
        
        $mapa = OrdenesEncargoDireccion::where('ordenes_encargo_id', $id)->first();
        $api = env("API_GOOGLE_MAPS", "");

        $latitud = $mapa->latitud_real;
        $longitud = $mapa->longitud_real;  

        return view('backend.paginas.encargos.mapacliente', compact('latitud', 'longitud', 'api'));
    }


    public function verProductoDeOrdenesEncargo($id){

        // viene el id de ordenes_encargos

        $data = OrdenesEncargo::where('id', $id)->first();

        $nombre = Encargos::where('id', $data->encargos_id)->pluck('nombre')->first();
                
        return view('backend.paginas.encargos.listaordenencargoproducto', compact('id', 'nombre')); 
    }

    public function verProductoDeOrdenesEncargoTabla($id){

        // viene el id de ordenes_encargos

        $producto = DB::table('ordenes_encargo_producto AS o')
        ->join('producto_categoria_negocio AS p', 'p.id', '=', 'o.producto_cate_nego_id')
        ->select('o.id', 'p.imagen', 'o.cantidad', 'o.nota', 'o.precio', 'o.nombre', 'o.descripcion')
        ->where('o.ordenes_encargo_id', $id)
        ->get();

        foreach($producto as $o){
                
            $cantidad = $o->cantidad;
            $precio = $o->precio;
            $multi = $cantidad * $precio;
            $o->multiplicado = number_format((float)$multi, 2, '.', '');  
            $o->precio = number_format((float)$o->precio, 2, '.', '');  
        }
                
        return view('backend.paginas.encargos.tablas.tablalistaordenencargoproducto', compact('producto')); 
    }


     public function verMotoristaAgarroEncargo(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required',
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id es requerido.',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }
            
            if(MotoristaOrdenEncargo::where('ordenes_encargo_id', $request->id)->first()){
                
                $info = DB::table('motorista_ordenes_encargo AS mo')
                ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')
                ->select('m.nombre', 'm.identificador', 'mo.fecha_agarrada')
                ->where('mo.ordenes_encargo_id', $request->id)
                ->get();
               
                foreach($info as $i){
                    $i->fecha_agarrada = date("d-m-Y h:i A", strtotime($i->fecha_agarrada));
                }
                 
                return ['success' => 1, 'orden' => $info];

            }else{
                return ['success' => 2]; // sin motorista aun
            }
        }
    }

    public function cancelarOrdenEncargo(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required',
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id es requerido.',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if(OrdenesEncargo::where('id', $request->id)->first()){

                $fecha = Carbon::now('America/El_Salvador');
                
                OrdenesEncargo::where('id', $request->id)->update([
                    'revisado' => 5, // cancelado
                    'mensaje_cancelado' => $request->mensaje,
                    'cancelado_por' => 0, // administrador
                    'fecha_cancelado' => $fecha,
                    'visible_cliente' => 0,
                    'visible_motorista' => 0,
                    'visible_propietario' => 0                    
                    ]);

                // notificacion al cliente que su encargo fue cancelado    
                
                return ['success' => 1];

            }else{
                return ['success' => 2]; 
            }
        }
    }


    public function confirmarOrdenEncargo(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required',
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id es requerido.',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($od = OrdenesEncargo::where('id', $request->id)->first()){
                
                OrdenesEncargo::where('id', $request->id)->update([
                    'revisado' => 2 // en proceso
                    ]);

                // notificacion al cliente de esta orden
                $dd = User::where('id', $od->users_id)->first();

                if($dd->device_id != "0000"){ // evitar id malos

                    $titulo = "Encargo #".$od->id;
                    $mensaje = "Muchas Gracias por confirmar su Encargo.";

                    try {
                        $this->envioNoticacionCliente($titulo, $mensaje, $dd->device_id); 
                    } catch (Exception $e) {
                        
                    }

                     return ['success' => 1];
                }else{
                    return ['success' => 2];
                }               

            }else{
                return ['success' => 3]; 
            }
        }
    }


    public function asignarMotoristaAlEncargo($id){

        $nombre = Encargos::where('id', $id)->pluck('nombre')->first();

        $motoristas = Motoristas::all();
                
        return view('backend.paginas.encargos.listamotoristasasignadosencargo', compact('id', 'nombre', 'motoristas')); 
    }

    public function asignarMotoristaAlEncargoTabla($id){

        $info = DB::table('motorista_encargo_asignado AS mo')
        ->join('motoristas AS m', 'm.id', '=', 'mo.motoristas_id')
        ->select('mo.id', 'm.identificador', 'm.nombre')
        ->where('mo.encargos_id', $id)
        ->get();
                
        return view('backend.paginas.encargos.tablas.tablalistamotoristasasignadosencargo', compact('info')); 
    }


    public function asignandoMotoristaAlEncargo(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required', // id encargo
                'id2' => 'required' // id motorista
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id es requerido.',
                'id2.required' => 'el id motorista es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if(MotoristaEncargoAsignado::where('encargos_id', $request->id)->where('motoristas_id', $request->id2)->first()){
                return ['success' => 1]; // ya registrado
            }
                
            $e = new MotoristaEncargoAsignado();
            $e->encargos_id = $request->id;
            $e->motoristas_id = $request->id2;

            if($e->save()){
                return ['success' => 2];
            }else{
                return ['success' => 3];
            }            
        }
    }


    public function asignandoMotoristaAlEncargoBorrar(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required' // id encargo
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if(MotoristaEncargoAsignado::where('id', $request->id)->first()){
                
                MotoristaEncargoAsignado::where('id', $request->id)->delete();
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }

    public function activarDesactivarListaProducto(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'id' => 'required',
            );
        
            $mensajeDatos = array(                                      
                'id.required' => 'El id es requerido.',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($e = ListaProductoEncargo::where('id', $request->id)->first()){

                // activar
                if($e->activo == 0){
                    ListaProductoEncargo::where('id', $request->id)->update([
                        'activo' => 1
                        ]);
                }else{
                    // desactivar
                    ListaProductoEncargo::where('id', $request->id)->update([
                        'activo' => 0
                        ]);
                }
                
                return ['success' => 1];

            }else{
                return ['success' => 2]; 
            }
        }

    }

    // vista para buscar # orden encargo
    public function indexVistaNumEncargo(){
        return view('backend.paginas.encargos.listavistanumeroencargo');
    }


    public function buscarNumeroOrdenEncargo($id){
      
        $orden = DB::table('ordenes_encargo')
        ->where('id', $id)
        ->get();

        foreach($orden as $o){
            $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden));

            $identificador = "";
            $nombre = "";
            if($d = EncargoAsignadoServicio::where('encargos_id', $o->encargos_id)->first()){
                $data = Servicios::where('id', $d->servicios_id)->first();
                $identificador = $data->identificador;
                $nombre = $data->nombre;
            }

            $o->identificador = $identificador;
            $o->nombre = $nombre; 
        } 

        return view('backend.paginas.encargos.tablas.tablaencargobuscador', compact('orden'));
    }

    // informacion de la orden encargo
    public function informacionOrdenEncargo(Request $request){
        if($request->isMethod('post')){  

            $regla = array( 
                'id' => 'required', 
            );
 
            $mensaje = array(
                'id.required' => 'id es requerido',                      
            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            }

            
            if(OrdenesEncargo::where('id', $request->id)->first()){

                $orden = DB::table('ordenes_encargo AS o')
                ->join('ordenes_encargo_direccion AS od', 'od.ordenes_encargo_id', '=', 'o.id')
                ->select('o.id', 'o.revisado', 'o.users_id', 'o.encargos_id',  'o.fecha_orden', 'o.precio_subtotal', 
                'o.precio_envio', 'o.ganancia_motorista', 'o.visible_cliente', 'o.visible_motorista', 'o.visible_propietario',
                    'o.mensaje_cancelado', 'o.cancelado_por', 'o.fecha_cancelado', 'o.calificacion', 'o.mensaje',
                    'o.estado_0', 'o.fecha_0', 'o.estado_1', 'o.fecha_1', 'o.estado_2', 'o.fecha_2', 'o.estado_3',
                    'o.fecha_3', 'od.zonas_id', 'od.nombre', 'od.direccion', 'od.numero_casa', 'od.punto_referencia',
                    'od.latitud', 'od.longitud', 'od.latitud_real', 'od.longitud_real') 
                ->where('o.id', $request->id)
                ->get();

                foreach($orden as $o){


                    $o->telefono = User::where('id', $o->users_id)->pluck('phone')->first();

                    $suma = $o->precio_envio + $o->precio_subtotal;
                    $o->total = number_format((float)$suma, 2, '.', '');


                    $o->fecha_orden = date("d-m-Y h:i A", strtotime($o->fecha_orden));
                    if($o->fecha_cancelado != null){
                        $o->fecha_cancelado = date("d-m-Y h:i A", strtotime($o->fecha_cancelado));
                    }
                    
                    if($o->estado_0 == 1){
                        $o->fecha_0 = date("d-m-Y h:i A", strtotime($o->fecha_0));
                    }

                    if($o->estado_1 == 1){
                        $o->fecha_1 = date("d-m-Y h:i A", strtotime($o->fecha_1));
                    } 

                    if($o->estado_2 == 1){
                        $o->fecha_2 = date("d-m-Y h:i A", strtotime($o->fecha_2));
                    }

                    $zz = Zonas::where('id', $o->zonas_id)->first();
                    $o->identificador = $zz->identificador;
                    $o->nombrezona = $zz->nombre;

                    $ee = Encargos::where('id', $o->encargos_id)->first();
                    
                    $o->nombreencargo = $ee->nombre;
                    $o->idencargo = $ee->id;


                }
                      
                return ['success' => 1, 'orden' => $orden];           
            }else{
                return ['success' => 2];
            }
        }
    } 



    public function envioNoticacionCliente($titulo, $mensaje, $pilaUsuarios){
        OneSignal::notificacionCliente($titulo, $mensaje, $pilaUsuarios);
    }


}
