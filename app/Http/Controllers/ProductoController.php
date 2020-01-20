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
        ->select('p.id', 'p.nombre', 'p.descripcion', 'p.posicion', 'p.precio', 'p.disponibilidad', 'p.activo', 'p.utiliza_cantidad')
        ->where('s.id', $id)
        ->orderBy('p.posicion', 'ASC')
        ->get();

        return view('backend.paginas.servicios.tablas.tablaproductos', compact('producto'));
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
                'nota' => 'required',
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
                'nota.required' => 'nota es requerido',
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
           
            // guardar imagen si trae 
            if($request->hasFile('imagen')){

                $cadena = Str::random(15);
                $tiempo = microtime(); 
                $union = $cadena.$tiempo;
                $nombre = str_replace(' ', '_', $union);
                
                $extension = '.'.$request->imagen->getClientOriginalExtension();
                $nombreFoto = $nombre.strtolower($extension);
                $avatar = $request->file('imagen'); 
                $upload = Storage::disk('productos')->put($nombreFoto, \File::get($avatar));

                $conteo = Producto::where('servicios_tipo_id', $request->idcategoria)->count();
                $posicion = 1;
    
                if($conteo >= 1){
                    $registro = Producto::where('servicios_tipo_id', $request->idcategoria)->orderBy('id', 'DESC')->first();
                    $posicion = $registro->posicion;
                    $posicion++;
                } 

                if($upload){

                    $fecha = Carbon::now('America/El_Salvador');

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
                    $ca->nota = $request->nota;
                    $ca->utiliza_imagen = $request->cbimagen;
        
                    if($ca->save()){
                        return ['success' => 2]; // guardado
                    }else{
                        return ['success' => 3]; // error la guardar
                    }
                }else{
                    return ['success' => 4]; // error al guardar imagen
                }

            }else{
                // solo datos

                if($request->cbimagen == 1){
                    return ['success' => 5]; // mostrara imagen pero no hay
                }

                $conteo = Producto::where('servicios_tipo_id', $request->idcategoria)->count();
                $posicion = 1;
    
                if($conteo >= 1){
                    $registro = Producto::where('servicios_tipo_id', $request->idcategoria)->orderBy('id', 'DESC')->first();
                    $posicion = $registro->posicion;
                    $posicion++;
                } 

                $fecha = Carbon::now('America/El_Salvador');


                $ca = new Producto();
                $ca->servicios_tipo_id = $request->idcategoria;
                $ca->nombre = $request->nombre;
                $ca->imagen = '';
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
                $ca->nota = $request->nota;
                $ca->utiliza_imagen = $request->cbimagen;
    
                if($ca->save()){
                    return ['success' => 2]; // guardado
                }else{
                    return ['success' => 3]; // error la guardar
                }
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
                'nota' => 'required',
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
                'nota.required' => 'la nota es requerido',
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
                            'nota' => $request->nota,
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
                        'nota' => $request->nota,
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
}
  