<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Producto;
use App\ServiciosTipo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\MultiplesImagenes;

class ProductoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    } 

    // lista de productos
    public function index($id){   
        // recibo id del servicio

        $dato = DB::table('servicios_tipo AS st')
        ->join('servicios AS s', 's.id', '=', 'st.servicios_1_id')
        ->select('s.nombre')
        ->where('st.id', $id)
        ->first();

        $nombre = $dato->nombre;

        return view('backend.paginas.servicios.listaproductos', compact('id', 'nombre'));
    } 

    // tabla de productos
    public function tablaProductos($id){
        
        $producto = DB::table('producto AS p')
        ->join('servicios_tipo AS s', 's.id', '=', 'p.servicios_tipo_id')
        ->select('p.id', 'p.nombre', 'p.descripcion', 'p.posicion', 'p.precio', 'p.es_promocion', 'p.disponibilidad', 'p.activo', 'p.utiliza_cantidad')
        ->where('s.id', $id)
        ->orderBy('p.posicion', 'ASC')
        ->get();

        return view('backend.paginas.servicios.tablas.tablaproductos', compact('producto'));
    }

    // ver todos los productos
    public function indexTodos($id){
        // recivimos el id del servicio
        $dato = DB::table('servicios_tipo AS st')
        ->join('servicios AS s', 's.id', '=', 'st.servicios_1_id')
        ->select('s.nombre')
        ->where('st.servicios_1_id', $id) 
        ->first();   

        $nombre = $dato->nombre;

        return view('backend.paginas.servicios.listaproductostodos', compact('id', 'nombre'));    
    }

    public function tablaTodosLosProductos($id){
        $producto = DB::table('producto AS p')
        ->join('servicios_tipo AS s', 's.id', '=', 'p.servicios_tipo_id')
        ->select('p.id', 'p.nombre', 'p.descripcion', 'p.posicion', 'p.precio',
         'p.es_promocion', 'p.disponibilidad', 'p.activo', 'p.utiliza_cantidad', 'p.imagen',
         's.nombre AS categoria')
        ->where('s.servicios_1_id', $id)
        ->orderBy('p.nombre', 'ASC')
        ->get(); 
 
        return view('backend.paginas.servicios.tablas.tablaproductostodos', compact('producto'));
    } 
 
    // nuevo producto
    public function nuevo(Request $request){        
        if($request->isMethod('post')){  
 
            $regla = array( 
                'idcategoria' => 'required',
                'nombre' => 'required',
                'descripcion' => 'required',
                'precio' => 'required',
                'unidades' => 'required',
                'cbdisponibilidad' => 'required',
                'cbactivo' => 'required',
                'cbcantidad' => 'required',
                'cblimite' => 'required',
                'cantidadorden' => 'required',
                'cbnota' => 'required',
                
                'cbimagen' => 'required',
                'cbpromocion' => 'required'
            );
 
            $mensaje = array(   
                'idcategoria.required' => 'id categoria es requerido',             
                'nombre.required' => 'nombre es requerido', 
                'descripcion.required' => 'descripcion es requerido',
                'precio.required' => 'precio es requerido',
                'unidades.required' => 'unidades es requerido',
                'cbdisponibilidad.required' => 'cbdisponibilidad es requerido',
                'cbactivo.required' => 'cbactivo es requerido',
                'cbcantidad.required' => 'cbcantidad es requerido',
                'cblimite.required' => 'cblimite es requerido',
                'cantidadorden.required' => 'cantidadorden es requerido',
                'cbnota.required' => 'cbnota es requerido',
               
                'cbimagen.required' => 'utiliza imagen es requerido',
                'cbpromocion.required' => 'promocion es requerida'
            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            } 
            
            // validar imagen
            if($request->hasFile('imagen')){                

                // validaciones para los datos
                $regla2 = array( 
                    'imagen' => 'required|image', 
                );    
         
                $mensaje2 = array(
                    'imagen.required' => 'La imagen es requerida',
                    'imagen.image' => 'El archivo debe ser una imagen',
                    );
    
                $validar2 = Validator::make($request->all(), $regla2, $mensaje2 );
    
                if ( $validar2->fails()) 
                {
                    return ['success' => 1]; // imagen no valida
                }
            }

                $cadena = Str::random(15);
                $tiempo = microtime(); 
                $union = $cadena.$tiempo;
                $nombre = str_replace(' ', '_', $union);
                
                $extension = '.'.$request->imagen->getClientOriginalExtension();
                $nombreFoto = $nombre.strtolower($extension);
                $avatar = $request->file('imagen'); 
                $upload = Storage::disk('productos')->put($nombreFoto, \File::get($avatar));

                if(Producto::where('imagen', $nombreFoto)->first()){
                    // este nombre de imagen ya existe, reintentar subir
                    return ['success' => 3];
                }

                $conteo = Producto::where('servicios_tipo_id', $request->idcategoria)->count();
                $posicion = 1;
    
                if($conteo >= 1){
                    $registro = Producto::where('servicios_tipo_id', $request->idcategoria)->orderBy('id', 'DESC')->first();
                    $posicion = $registro->posicion;
                    $posicion++;
                } 

                if($upload){

                    $fecha = Carbon::now('America/El_Salvador');

                    $nota = $request->nota;
                    if($request->nota == null){
                        $nota = "";
                    }

                    $ca = new Producto();
                    $ca->servicios_tipo_id = $request->idcategoria;
                    $ca->nombre = $request->nombre;
                    $ca->imagen = $nombreFoto;
                    $ca->descripcion = $request->descripcion;
                    $ca->precio = $request->precio;
                    $ca->unidades = $request->unidades;
                    $ca->disponibilidad = $request->cbdisponibilidad;
                    $ca->activo = $request->cbactivo;
                    $ca->posicion = $posicion;
                    $ca->utiliza_cantidad = $request->cbcantidad;
                    $ca->fecha = $fecha;
                    $ca->es_promocion = $request->cbpromocion;
                    $ca->limite_orden = $request->cblimite;
                    $ca->cantidad_por_orden = $request->cantidadorden;
                    $ca->utiliza_nota = $request->cbnota;
                    $ca->nota = $nota;
                    $ca->utiliza_imagen = $request->cbimagen;
                    $ca->utiliza_video = 0;
        
                    if($ca->save()){
                        return ['success' => 2]; // guardado
                    }else{
                        return ['success' => 3]; // error la guardar
                    }
                }else{
                    return ['success' => 4]; // error al guardar imagen
                }            
            
        }
    } 

    // informacion del producto
    public function informacion(Request $request){
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

            
          if($p = Producto::where('id', $request->id)->first()){

            // sacar todas las categorias, para poder cambiar el producto
            $idcategoria = $p->servicios_tipo_id;

            $idservicio = ServiciosTipo::where('id', $idcategoria)->pluck('servicios_1_id')->first();

            $categorias = ServiciosTipo::where('servicios_1_id', $idservicio)->get();

            return ['success' => 1, 'categoria' => $categorias, 'producto' => $p]; 
          }else{
              return ['success' => 2];
          }
        }
    }

    // editar producto 
    public function editar(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'nombre' => 'required',
                'descripcion' => 'required',
                'selectcategoria' => 'required',
                'precio' => 'required',
                'unidades' => 'required',
                'cbdisponibilidad' => 'required',
                'cbactivo' => 'required',
                'cbcantidad' => 'required',
                'cbpromocion' => 'required',
                'cblimite' => 'required',
                'cbutilizanota' => 'required',
                'cbimagen' => 'required',
                
                'cantidadorden' => 'required',
            );

            $messages = array(   
                'id.required' => 'El id es requerido',
                'nombre.required' => 'El nombre es requerido',
                'descripcion.required' => 'la descripcion es requerido',
                'selectcategoria.required' => 'la categoria es requerido',
                'precio.required' => 'el precio es requerido',
                'unidades.required' => 'las unidades es requerido',
                'cbdisponibilidad.required' => 'El check disponibilidad es requerido',
                'cbactivo.required' => 'El check activo es requerido',
                'cbcantidad.required' => 'El check cantidad por orden es requerido',
                'cbpromocion.required' => 'El check promocion es requerido',
                'cblimite.required' => 'El check limite requerido',
                'cbutilizanota.required' => 'El check utiliza nota es requerido',
                'cbimagen.required' => 'El check utiliza imagen requerido',
                'cantidadorden.required' => 'la cantidad por orden es requerido',
                
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if($request->hasFile('imagen')){                

                // validaciones para los datos
                $regla2 = array( 
                    'imagen' => 'required|image', 
                );     
         
                $mensaje2 = array(
                    'imagen.required' => 'La imagen es requerida',
                    'imagen.image' => 'El archivo debe ser una imagen',
                    );
    
                $validar2 = Validator::make($request->all(), $regla2, $mensaje2 );
    
                if ( $validar2->fails()) 
                {
                    return ['success' => 1]; // imagen no valida
                }
            }

            if($po = Producto::where('id', $request->id)->first()){

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

                        $nota = $request->nota;
                        if($request->nota == null){
                            $nota = "";
                        }
                        
                        Producto::where('id', $request->id)->update([
                            'servicios_tipo_id' => $request->selectcategoria,
                            'nombre' => $request->nombre,
                            'imagen' => $nombreFoto,
                            'descripcion' => $request->descripcion,
                            'precio' => $request->precio,
                            'unidades' => $request->unidades,
                            'disponibilidad' => $request->cbdisponibilidad,
                            'activo' => $request->cbactivo,
                            'utiliza_cantidad' => $request->cbcantidad,
                            'es_promocion' => $request->cbpromocion,
                            'limite_orden' => $request->cblimite,
                            'cantidad_por_orden' => $request->cantidadorden,
                            'utiliza_nota' => $request->cbutilizanota,
                            'nota' => $nota,
                            'utiliza_imagen' => $request->cbimagen,
                            ]);
                            
                        if(Storage::disk('productos')->exists($imagenOld)){
                            Storage::disk('productos')->delete($imagenOld);                                
                        } 
                        
                        return ['success' => 2];

                    }else{
                        return ['success' => 3]; // error subir imagen
                    }             
                }else{
                    // solo guardar datos

                    $nota = $request->nota;
                        if($request->nota == null){
                            $nota = "";
                        }
                        
                    Producto::where('id', $request->id)->update([
                        'servicios_tipo_id' => $request->selectcategoria,
                        'nombre' => $request->nombre,
                        'descripcion' => $request->descripcion, 
                        'precio' => $request->precio,
                        'unidades' => $request->unidades,
                        'disponibilidad' => $request->cbdisponibilidad,
                        'activo' => $request->cbactivo,
                        'utiliza_cantidad' => $request->cbcantidad,
                        'es_promocion' => $request->cbpromocion,
                        'limite_orden' => $request->cblimite,
                        'cantidad_por_orden' => $request->cantidadorden,
                        'utiliza_nota' => $request->cbutilizanota,
                        'nota' => $nota,
                        'utiliza_imagen' => $request->cbimagen,
                        ]);

                    return ['success' => 2];
                } 

            }else{
                return ['success' => 4]; // producto no encontrado 
            }
        }  
    }
 
    // ordenar producto
    public function ordenar(Request $request){

        $idproducto = 0;
        // sacar id del primer producto
        foreach ($request->order as $order) {

            $idproducto = $order['id'];
            break;
        }

        $idcategoria = Producto::where('id', $idproducto)->pluck('servicios_tipo_id')->first();

        // todos los productos que pertenecen a esta categoria
        $tasks = Producto::where('servicios_tipo_id', $idcategoria)->get();
    
        foreach ($tasks as $task) {
            $id = $task->id;
    
            foreach ($request->order as $order) {
                if ($order['id'] == $id) {
                    $task->update(['posicion' => $order['posicion']]);
                }
            }
        }
        return ['success' => 1];
    }


    public function indexMasFotos($id){
       
        $nombre = Producto::where('id', $id)->pluck('nombre')->first();
        
        return view('backend.paginas.servicios.listamultiplesfotos', compact('nombre', 'id'));
    }


    public function indexMasFotosTabla($id){
        $foto = MultiplesImagenes::where('producto_id', $id)->get();
        
        return view('backend.paginas.servicios.tablas.tablaproductoimagenes', compact('foto'));
    }

     public function indexMasVideo($id){
       
        $data = Producto::where('id', $id)->first();

        $nombre = $data->nombre;
        $video = $data->video_url;
        
        return view('backend.paginas.servicios.listaproductovideo', compact('nombre', 'video', 'id'));
    }


    // nueva foto extra de producto
    public function nuevaFotoExtra(Request $request){        
        if($request->isMethod('post')){  
 
            $regla = array( 
                'id' => 'required'
            );
 
            $mensaje = array(   
                'id.required' => 'id producto es requerido'       
                
            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            } 
            
            // validar imagen
            if($request->hasFile('imagen')){                

                // validaciones para los datos
                $regla2 = array( 
                    'imagen' => 'required|image', 
                );    
         
                $mensaje2 = array(
                    'imagen.required' => 'La imagen es requerida',
                    'imagen.image' => 'El archivo debe ser una imagen',
                    );
    
                $validar2 = Validator::make($request->all(), $regla2, $mensaje2 );
    
                if ( $validar2->fails()) 
                {
                    return ['success' => 1]; // imagen no valida
                }
            }

                $cadena = Str::random(15);
                $tiempo = microtime(); 
                $union = $cadena.$tiempo;
                $nombre = str_replace(' ', '_', $union);
                            
                $extension = '.'.$request->imagen->getClientOriginalExtension();
                $nombreFoto = $nombre.strtolower($extension);
                $avatar = $request->file('imagen'); 

                if(MultiplesImagenes::where('imagen_extra', $nombreFoto)->first()){
                    // este nombre de imagen ya existe, reintentar subir
                    return ['success' => 3];
                }

                $upload = Storage::disk('productos')->put($nombreFoto, \File::get($avatar));

              

                if($upload){
                  
                    $ca = new MultiplesImagenes();
                    $ca->producto_id = $request->id;
                    $ca->imagen_extra = $nombreFoto;
                    $ca->posicion = 1;

                    if($ca->save()){
                        return ['success' => 2]; // guardado
                    }else{
                        return ['success' => 3]; // error la guardar
                    }
                }else{
                    return ['success' => 4]; // error al guardar imagen
                }     
        }
    } 


    // borrar imagen de producto extra
    public function borrarImagenExtra(Request $request){

        if($request->isMethod('post')){  
 
            $regla = array( 
                'id' => 'required'
            );
 
            $mensaje = array(   
                'id.required' => 'id producto es requerido'       
                
            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            } 
            
            if($mi = MultiplesImagenes::where('id', $request->id)->first()){

                if(Storage::disk('productos')->exists($mi->imagen_extra)){
                    Storage::disk('productos')->delete($mi->imagen_extra);                                
                }

                $idpro = $mi->producto_id;

                MultiplesImagenes::where('id', $request->id)->delete();

                // buscar si hay imagenes extra, sino desactivar
                if(MultiplesImagenes::where('producto_id', $request->id)->first()){
                    // si hay aun
                }else{
                    Producto::where('id', $idpro)->update([
                        'utiliza_imagen_extra' => 0 
                        ]);
                }

                return ['success' => 1];
            }
        }
    }

    public function editarProductoImagenExtra(Request $request){

        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required'                
            );

            $messages = array(   
                'id.required' => 'El id es requerido' 
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            // por lo menos hay 1 foto extra
            if(MultiplesImagenes::where('producto_id', $request->id)->first()){
                Producto::where('id', $request->id)->update([
                    'utiliza_imagen_extra' => $request->check              
                    ]);

                    return ['success' => 1];
    
            }else{
                return ['success' => 2];
            }           
        } 
    }


    public function ordenarImagenesExtra(Request $request){

        foreach ($request->order as $order) {

            $tipoid = $order['id'];

            DB::table('zonas_publicidad')
            ->where('publicidad_id', $tipoid) 
            ->update(['posicion' => $order['posicion']]); // actualizar posicion
        }           

        return ['success' => 1];
    }
    

    public function agregarVideoProducto(Request $request){

        if($request->isMethod('post')){  
 
            $regla = array( 
                'id' => 'required'
            );
 
            $mensaje = array(   
                'id.required' => 'id producto es requerido'       
                
            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            } 

            $cadena = Str::random(15);
            $tiempo = microtime(); 
            $union = $cadena.$tiempo;
            $nombre = str_replace(' ', '_', $union);
                        
            $extension = '.'.$request->video->getClientOriginalExtension();
            $nombreVideo = $nombre.strtolower($extension);
            $avatar = $request->file('video'); 

            if(Producto::where('video_url', $nombreVideo)->first()){
                // este nombre de video ya existe, reintentar subir
                return ['success' => 1];
            }

            $upload = Storage::disk('productos')->put($nombreVideo, \File::get($avatar));

            if($upload){

                // obtener url anterior, sino habia nada, pues no eliminara nada
                $dd = Producto::where('id', $request->id)->pluck('video_url')->first();

                if(Storage::disk('productos')->exists($dd)){
                    Storage::disk('productos')->delete($dd);                                
                }

                // agregar nueva url
                 Producto::where('id', $request->id)->update([                   
                  'video_url' => $nombreVideo,
                ]);
                
                return ['success' => 2]; 
                
            }else{
                return ['success' => 3]; // error al guardar
            }     
        }
    }


    public function borrarVideoProducto(Request $request){

        if($request->isMethod('post')){  
 
            $regla = array( 
                'id' => 'required'
            );
 
            $mensaje = array(   
                'id.required' => 'id producto es requerido'       
                
            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            } 
            
            if($p = Producto::where('id', $request->id)->first()){

                if(Storage::disk('productos')->exists($p->video_url)){
                    Storage::disk('productos')->delete($p->video_url);                                
                }
                
                Producto::where('id', $p->id)->update([
                    'utiliza_video' => 0,
                    'video_url' => ""
                    ]);

                return ['success' => 1];
            }
        }
    }


    public function editarProductoVideo(Request $request){

        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required'                
            );

            $messages = array(   
                'id.required' => 'El id es requerido' 
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            // por lo menos hay 1 foto extra
            if($p = Producto::where('id', $request->id)->first()){

                if($p->video_url == null || $p->video_url == ""){
                    return ['success' => 1];
                }

                Producto::where('id', $request->id)->update([
                    'utiliza_video' => $request->check              
                    ]);

                    return ['success' => 2];
    
            }else{
                return ['success' => 3];
            }           
        } 

    }


}
  