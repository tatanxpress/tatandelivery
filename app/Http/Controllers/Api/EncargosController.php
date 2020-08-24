<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\User;
use Carbon\Carbon;
use DateTime;
use Exception; 
use App\ProductoCategoriaNegocio;
use App\CarritoEncargo;
use App\CarritoEncargoProducto;
use App\Direccion;
use App\Encargos;
use App\EncargosZona;
use App\OrdenesEncargo;
use App\OrdenesEncargoProducto;
use App\EncargoAsignarMoto;
use App\ListaEncargo;
use App\ListaProductoEncargo;
use App\OrdenesEncargoDireccion;
use App\CategoriasNegocio;
use App\EncargoAsignadoServicio;
use App\Servicios;

class EncargosController extends Controller
{
    // tarjetas de encargos por zona servicio 

    public function encargosPorZona(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(                
                'userid' => 'required',               
            );
        
            $mensajeDatos = array(                                      
                'userid.required' => 'El id del usuario es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }
 
            // obtener zona segun id del usuario
            $idzona = User::where('id', $request->userid)->pluck('zonas_id')->first();
                             
            $encargos = DB::table('encargos AS e')
            ->join('encargos_zona AS ez', 'ez.encargos_id', '=', 'e.id')
            ->select('e.id', 'e.nombre', 'e.descripcion', 'e.tipo_vista', 
            'e.imagen', 'e.fecha_finaliza', 'e.fecha_entrega'. 'e.fecha_estimada')
            ->where('ez.zonas_id', $idzona)
            ->where('e.activo', '1') // encargo activo en general
            ->where('e.vista_cliente', '1')  // cuando encargo termina, se oculta al cliente, porque tenemos todas las opciones
                                             // en la ventana activos, asi revisar sin mostrar el encargo finalizado
            ->orderBy('ez.posicion', 'ASC')     
            ->get();

            $tiempoHoy = Carbon::now('America/El_Salvador');
 
            $tiempoReal = new DateTime($tiempoHoy);

            

            $daysSpanish = [
                0 => 'lunes',
                1 => 'martes',
                2 => 'miércoles',
                3 => 'jueves',
                4 => 'viernes',
                5 => 'sábado',
                6 => 'domingo',
            ];
            

            foreach($encargos as $e){
             
                // verificar si no ha finalizado
                $finaliza = new DateTime($e->fecha_finaliza);

                $estado = 0; // hay tiempo
                $packTiempo = 0;
                if($tiempoReal >= $finaliza){
                    $estado = 1;
                }else{                                                      
                    $to = Carbon::createFromFormat('Y-m-d H:i:s', $tiempoHoy);
                    $from = Carbon::createFromFormat('Y-m-d H:i:s', $e->fecha_finaliza); // mayor

                    $packTiempo = $to->diffInMilliseconds($from);
                    $e->fecha_finaliza = date("d-m-Y h:i A", strtotime($e->fecha_finaliza));                  
                }

                // obtener el mes
                /*setlocale(LC_ALL, 'es_ES');
                $mesfecha = date("d-m-Y", strtotime($e->fecha_entrega));
                $fecha = Carbon::parse('03-04-2018');
                $fecha->format("F"); 
                $mes = $fecha->formatLocalized('%B');*/

                // FECHA ENTREGA
                $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                $fecha = Carbon::parse($e->fecha_finaliza);
                $mes = $meses[($fecha->format('n')) - 1];
                
                //$dianumero = date("d", strtotime($e->fecha_entrega));
                //$hora = date("h:i A", strtotime($e->fecha_entrega));               

                // MENSAJE PERSONALIZADO PARA FECHA ESTIMADA 
                //$fechaNombre = $dianumero . " de " . $mes . " a las " . $hora;
                $e->fecha_entrega = $e->fecha_estimada; // mejor un texto para decirle fecha de entrega 
  
                // fecha estatica para iphone

               /* setlocale(LC_ALL, 'es_ES');
                $mesfechaf = date("d-m-Y", strtotime($e->fecha_finaliza));
                $fechaf = Carbon::parse($mesfechaf);
                $fechaf->format("F"); 
                $mesf = $fechaf->formatLocalized('%B');*/

                //$fechar = Carbon::parse($e->fecha_finaliza);
                //$mesf = $meses[($fechar->format('n')) - 1];
                
                // FECHA IPHONE
                $dianumerof = date("d", strtotime($e->fecha_finaliza));
                $horaf = date("h:i A", strtotime($e->fecha_finaliza));               

                $fechaiphone = $dianumerof . " de " . $mes . " a las " . $horaf;
                $e->fechaiphone = $fechaiphone;

                $e->estado = $estado; // si es 1, esto ha finalizado
                $e->packTiempo = $packTiempo;
            } 

            return ['success' => 1, 'encargos' => $encargos];
        }
    }

    // ver lista de categorias y productos

    public function listaDeCategorias(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(                
                'encargoid' => 'required'           
            ); 
        
            $mensajeDatos = array(                                      
                'encargoid.required' => 'El encargoid es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            $producto = DB::table('lista_encargo AS le')    
                ->join('categorias_negocios AS cn', 'cn.id', '=', 'le.categorias_negocios_id')
                ->select('le.id', 'cn.nombre') // 
                ->where('le.encargos_id', $request->encargoid) // bueno
                ->where('le.activo', 1) // categoria activa
                ->orderBy('le.posicion', 'ASC') // bueno
                ->get(); 
 
                $resultsBloque = array();
                $index = 0;

                foreach($producto as $secciones){
                    array_push($resultsBloque, $secciones);          
                
                    $subSecciones = DB::table('lista_producto_encargo AS lp')  
                    ->join('producto_categoria_negocio AS pc', 'pc.id', '=', 'lp.producto_cate_nego_id')
                    ->select('lp.id', 'pc.nombre', 'pc.imagen', 'pc.descripcion', 'pc.precio')
                    ->where('lp.lista_encargo_id', $secciones->id)         
                    ->where('lp.activo', 1) // producto activo
                    ->orderBy('lp.posicion', 'ASC')
                    ->get(); 
                   
                    $resultsBloque[$index]->productos = $subSecciones; //agregar los productos en la sub seccion
                    $index++;
                }

            return ['success' => 1, 'categorias' => $producto];
        }
    }


    public function listaDeCategoriasHorizontal(Request $request){

        if($request->isMethod('post')){ 
            $reglaDatos = array(                
                'encargoid' => 'required'           
            );
        
            $mensajeDatos = array(                                      
                'encargoid.required' => 'El encargoid es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }   
 
                $tipo = DB::table('lista_encargo AS le')    
                ->join('categorias_negocios AS cn', 'cn.id', '=', 'le.categorias_negocios_id')
                ->select('le.id', 'cn.id AS cnc', 'cn.nombre')
                ->where('le.encargos_id', $request->encargoid)
                ->where('le.activo', 1)      
                ->orderBy('le.posicion', 'ASC')
                ->get();

                $t = 0;

                // obtener total de productos por seccion
                foreach ($tipo as $user){

                    $t = $t + 1;
    
                    // contar cada seccion
                    $producto = DB::table('lista_encargo AS le')
                    ->join('lista_producto_encargo AS lp', 'lp.lista_encargo_id', '=', 'le.id')
                    ->select('le.id')
                    ->where('le.activo', 1) // categoria activa
                    ->where('lp.activo', 1) // producto activo
                    ->where('le.id', $user->id)
                    ->get(); 
    
                    $contador = count($producto);
                    $user->total = $contador;    
                }
    
                $resultsBloque = array();        
                $index = 0;


                foreach($tipo  as $secciones){
                    array_push($resultsBloque,$secciones);                          
                 
                    $subSecciones = DB::table('lista_producto_encargo AS lp') 
                    ->join('producto_categoria_negocio AS pc', 'pc.id', '=', 'lp.producto_cate_nego_id')
                    ->select('lp.id', 'pc.nombre', 'pc.imagen', 'pc.descripcion', 'pc.precio')
                    ->where('lp.lista_encargo_id', $secciones->id)      
                    ->take(5) //maximo 5 productos por seccion
                    ->where('lp.activo', 1) // producto activo
                    ->get();
                     
                   
                    $resultsBloque[$index]->productos = $subSecciones;
                    $index++;
                }
        
            return ['success' => 1, 'producto' => $tipo];
        }
    }


    public function listaDeCategoriasHorizontalSeccion(Request $request){

        if($request->isMethod('post')){ 
            $reglaDatos = array(               
                'seccionid' => 'required'
            );    
                  
            $mensajeDatos = array(   
                'seccionid.required' => 'El id de la seccion es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );
            if($validarDatos->fails())
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  
        
            if($ll = ListaEncargo::where('id', $request->seccionid)->first()){

                // si pudo ver esta pantalla, porque categoria estaba activa
                $productos = DB::table('lista_producto_encargo AS l')
                ->join('producto_categoria_negocio AS pc', 'pc.id', '=', 'l.producto_cate_nego_id')
                ->select('l.id', 'pc.nombre', 'pc.precio', 'pc.imagen')
                ->where('l.activo', 1) // producto unicamente activo
                ->where('l.lista_encargo_id', $request->seccionid)            
                ->get();

                $categoria = CategoriasNegocio::where('id', $ll->categorias_negocios_id)->pluck('nombre')->first();

                return ['success' => 1, 'productos' => $productos, 'categoria' => $categoria];
            }else{
                return ['success' => 2];
            }                                 
        }
    }


    public function productoIndividual(Request $request){
        if($request->isMethod('post')){ 
 
            // validaciones para los datos
            $reglaDatos = array(                
                'productoid' => 'required',
            );    
        
            $mensajeDatos = array(                                      
                'productoid.required' => 'El id del producto es requerido.',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }
 
            if(ListaProductoEncargo::where('id', $request->productoid)->first()){

                $producto = DB::table('lista_producto_encargo AS l')
                ->join('producto_categoria_negocio AS pc', 'pc.id', '=', 'l.producto_cate_nego_id')
                ->select('l.id', 'pc.nombre', 'pc.imagen', 'pc.descripcion',
                 'pc.precio', 'pc.utiliza_nota', 'pc.nota', 'l.producto_cate_nego_id')
                ->where('l.id', $request->productoid)
                ->get();

                foreach($producto as $p){
                    $data = ProductoCategoriaNegocio::where('id', $p->producto_cate_nego_id)->first();
                    $datacate = CategoriasNegocio::where('id', $data->categorias_negocio_id)->first();
                    $p->categoria = $datacate->nombre;
                }
                              
                return ['success' => 1, 'producto' => $producto];

            }else{
                return ['success' => 2];
            }
        }
    }


    // agregar productos al carrito de compras para encargos
    public function agregarProductoEncargo(Request $request){
        if($request->isMethod('post')){ 
            // validaciones para los datos
            $reglaDatos = array(                
                'userid' => 'required',                
                'productoid' => 'required', // id de la lista: lista_producto_encargo
                'mismoservicio' => 'required' // para preguntar si borra contenido anterior y crear nuevo carrito
            );    
                    
            $mensajeDatos = array(  
                'userid.required' => 'El id del usuario es requerido.',
                'productoid.required' => 'El id del producto es requerido.',
                'mismoservicio.required' => 'El ID del mismo servicio requerido.', 
                );
            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            } 
 
            // verificar primero si el encargo finalizo

            $tiempoHoy = Carbon::now('America/El_Salvador');
            $tiempoReal = new DateTime($tiempoHoy);

            DB::beginTransaction();
        
            try {                   
                    // sacar id del encargo a cual esta asignado este producto
                    $datos = DB::table('lista_encargo AS s')
                    ->join('lista_producto_encargo AS st', 'st.lista_encargo_id', '=', 's.id')
                    ->select('s.encargos_id AS idEncargo', 'st.producto_cate_nego_id')
                    ->where('st.id', $request->productoid) // este es el id de la fila
                    ->first();

                    $idencargo = $datos->idEncargo; //id encargo

                    // obtener el id del producto de la fila lista_producto
                    $idproducto = $datos->producto_cate_nego_id;
                   
                    
                    $fecha_finaliza = Encargos::where('id', $idencargo)->pluck('fecha_finaliza')->first();

                    // verificar si no ha finalizado
                    $finaliza = new DateTime($fecha_finaliza);
                  
                    if($tiempoReal >= $finaliza){
                        return [ // este encargo a finalizado
                            'success' => 4
                        ]; 
                    }

                   
                // verificar si el usuario va a borrar la tabla de carrito de compras
                if($request->mismoservicio == 1){ // borrar tablas
                    $tabla1 = CarritoEncargo::where('users_id', $request->userid)->first();
                    CarritoEncargoProducto::where('carrito_encargo_id', $tabla1->id)->delete();
                    CarritoEncargo::where('users_id', $request->userid)->delete();
                    DB::commit();
                }
                // preguntar si usuario ya tiene un carrito de compras
                if($cart = CarritoEncargo::where('users_id', $request->userid)->first()){

                       
                        // ver limite de unidades del producto que quiere agregar y comparar si esta el mismo producto en carrito
                        // no esta agregando del mismo servicio
                        if($cart->encargos_id != $idencargo){

                            $nombreServicio = Encargos::where('id', $cart->encargos_id)->pluck('nombre')->first();

                            return [
                                'success' => 1, // no agregando del mismo encargo
                                'nombre' => $nombreServicio // nombre del encargo que ya tenia en el carrito
                            ];
                        }

                        // si esta agregando del mismo servicio
                        $extra = new CarritoEncargoProducto();
                        $extra->carrito_encargo_id = $cart->id;
                        $extra->producto_cate_nego_id = $idproducto;
                        $extra->cantidad = $request->cantidad; // siempre sera 1 el minimo
                        $extra->nota_producto = $request->notaproducto;
                        $extra->save();
                        DB::commit();

                        return [ //producto guardado
                            'success' => 2
                        ];                                    
                }else{
                   
                    // crear carrito encargo nuevo

                    // obtener zona del usuario donde pide
                    $di = Direccion::where('user_id', $request->userid)->where('seleccionado', 1)->first();
                   
                    $carrito = new CarritoEncargo();
                    $carrito->users_id = $request->userid;
                    $carrito->encargos_id = $idencargo;
                    $carrito->zonas_id = $di->zonas_id;
                    $carrito->save();

                    // guardar producto
                    $idcarrito = $carrito->id;
 
                    $extra = new CarritoEncargoProducto();
                    $extra->carrito_encargo_id = $idcarrito;
                    $extra->producto_cate_nego_id = $idproducto;
                    $extra->cantidad = $request->cantidad; // siempre sera 1 el minimo
                    $extra->nota_producto = $request->notaproducto;
                    $extra->save();
                  
                    DB::commit();
                    
                    return [
                        'success' => 2 // producto agregado
                    ];
                }  
                       
            }catch(\Error $e){
                DB::rollback();

                return [
                    'success' => 5
                ];
            }
        } 
    }


    // ver carrito de compras
    public function verCarritoDeCompras(Request $request){

        if($request->isMethod('post')){
            $reglaDatos = array(
                'userid' => 'required',
            );
                  
            $mensajeDatos = array(                                      
                'userid.required' => 'El id del usuario es requerido.'
                );
            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );
            if($validarDatos->fails())
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if(User::where('id', $request->userid)->first()){

                DB::beginTransaction();
                try {
 
                    // preguntar si usuario ya tiene un carrito de compras
                    if($cart = CarritoEncargo::where('users_id', $request->userid)->first()){

                        
                        $producto = DB::table('producto_categoria_negocio AS p')
                        ->join('carrito_encargo_pro AS c', 'c.producto_cate_nego_id', '=', 'p.id')          
                        ->select('p.id AS productoID', 'p.nombre', 'p.imagen', 'c.cantidad', 'p.precio', 'c.id AS idFila')
                        ->where('c.carrito_encargo_id', $cart->id)
                        ->get();
  
                        // sub total de la orden
 
                        foreach($producto as $p){
                            $cantidad = $p->cantidad;
                            $precio = $p->precio;
                            $multi = $cantidad * $precio;
                            $p->multiplicado = number_format((float)$multi, 2, '.', '');
                        }

                        // verificar unidades de cada producto
                        foreach ($producto as $pro) {                            
                            $pro->precio = number_format((float)$pro->precio, 2, '.', '');
                        } 

                        $subTotal = collect($producto)->sum('multiplicado'); 
                        $subTotal = number_format((float)$subTotal, 2, '.', '');

                        $data = Encargos::where('id', $cart->encargos_id)->first();

                        $botonTexto = $data->texto_boton;
                        $requiereNota = $data->requiere_nota;
                        $notaEncargo = $data->nota_encargo;

                        return [
                            'success' => 1,
                            'producto' => $producto,
                            'subtotal' => $subTotal,
                            'boton' => $botonTexto,
                            'requiere_nota' => $requiereNota,
                            'nota_encargo' => $notaEncargo               
                        ];

                    }else{
                         // no tiene carrito de compras
                        return ['success' => 2, 'producto' => []];
                    }
                }catch(\Error $e){
                    return [
                        'success' => 3 // error
                    ];
                }
            }
            else{
                return ['success' => 3]; // error
            }
        }
    }


    // eliminar producto individual del encargo
    public function eliminarProductoEncargo(Request $request){
        
        if($request->isMethod('post')){ 
            $reglaDatos = array(                
                'userid' => 'required',
                'carritoid' => 'required'
            );
                  
            $mensajeDatos = array(                              
                'userid.required' => 'El id del usuario es requerido',
                'carritoid.required' => 'El id del carrito es requerido'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails())
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  


            // verificar si tenemos carrito
            if($ctm = CarritoEncargo::where('users_id', $request->userid)->first()){
                
                // encontrar el producto a borrar
                if(CarritoEncargoProducto::where('id', $request->carritoid)->first()){
                    CarritoEncargoProducto::where('id', $request->carritoid)->delete();

                    // saver si tenemos mas productos aun
                    $dato = CarritoEncargoProducto::where('carrito_encargo_id', $ctm->id)->get();

                    if(count($dato) == 0){
                        CarritoEncargo::where('id', $ctm->id)->delete();

                        return ['success' => 1]; // carrito de compras borrado
                    }

                    return ['success' => 2]; // producto eliminado
                }else{
                    // producto a borrar no encontrado
                    return [
                        'success' => 3
                    ];
                }
            }else{              
                return [
                    'success' => 4   // sin carrito
                ];
            }
        }

    }

    // eliminar carrito de encargo
    public function eliminarCarritoEncargo(Request $request){

        if($request->isMethod('post')){ 
            $reglaDatos = array(                
                'userid' => 'required',                
            );    
                  
            $mensajeDatos = array(                                      
                'userid.required' => 'El id del usuario es requerido.'
                );
            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );
            if($validarDatos->fails())
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }   
            if($carrito = CarritoEncargo::where('users_id', $request->userid)->first()){
                CarritoEncargoProducto::where('carrito_encargo_id', $carrito->id)->delete();
                CarritoEncargo::where('users_id', $request->userid)->delete();
                return [
                    'success' => 1 // carrito borrado
                ];
            }else{
                return [
                    'success' => 2 // el carrito esta vacio
                ];
            }
        } 
    }


    // guardar encargos temporales, esto no se borraran, solo se ocultan
    public function guadarEncargoModoEspera(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'userid' => 'required'              
            );
        
            $mensajeDatos = array(                                      
                'userid.required' => 'El id del usuario es requerido.'               
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            { 
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            $tiempoHoy = Carbon::now('America/El_Salvador');
            $tiempoReal = new DateTime($tiempoHoy);
            DB::beginTransaction();
           
            try { 

                 // verificar si tengo carrito
                if($cart = CarritoEncargo::where('users_id', $request->userid)->first()){

                    // verificar si hay tiempo para agregar el encargo
                    $fecha_finaliza = Encargos::where('id', $cart->encargos_id)->pluck('fecha_finaliza')->first();

                    // verificar si no ha finalizado
                    $finaliza = new DateTime($fecha_finaliza);

                    if($tiempoReal >= $finaliza){
                        return [ // este encargo a finalizado
                            'success' => 3
                        ]; 
                    }

                    $producto = DB::table('carrito_encargo_pro AS cp')
                    ->where('cp.carrito_encargo_id', $cart->id)
                    ->get();
                  
                    $pila = array();

                     // recorrer cada producto para saver cantidad y precio
                    foreach($producto as $p){
                        $cantidad = $p->cantidad; // cantidad

                        $dato = ProductoCategoriaNegocio::where('id', $p->producto_cate_nego_id)->first(); // info de ese producto
                        $multi = $cantidad * $dato->precio; //multiplicar cantidad por precio
                        array_push($pila, $multi); // unir para subtotal $                        
                    } 

                    $resultado=0;
                    foreach ($pila as $valor){
                        $resultado=$resultado+$valor;
                    }

                    $convertir = number_format((float)$resultado, 2, '.', '');
                    $precioTotal = (string) $convertir;

                    // obtener precio envio del encargo
                    $datosZona = EncargosZona::where('encargos_id', $cart->encargos_id)->first();

                    $fecha = Carbon::now('America/El_Salvador');

                    $idservicio = EncargoAsignadoServicio::where('encargos_id', $cart->encargos_id)->pluck('servicios_id')->first();

                    $pagopropi = Servicios::where('id', $idservicio)->first();
                    
                    $orden = new OrdenesEncargo();
                    $orden->encargos_id = $cart->encargos_id;
                    $orden->users_id = $request->userid;
                    $orden->precio_subtotal = $precioTotal;
                    $orden->revisado = 1; // pendiente
                    $orden->precio_envio = $datosZona->precio_envio;
                    $orden->fecha_orden = $fecha;
                    $orden->ganancia_motorista = $datosZona->ganancia_motorista;
                    $orden->visible_cliente = 1;
                    $orden->visible_motorista = 1; // es visible, pero hasta que admin coloque permiso podra ver en tabla encargos
                    $orden->visible_propietario = 1;
                    $orden->cancelado_por = 0;
                    $orden->calificacion = 0;
                    $orden->fecha_cancelado = null;
                    $orden->estado_0 = 0; 
                    $orden->fecha_0 = null;
                    $orden->estado_1 = 0;
                    $orden->fecha_1 = null;
                    $orden->estado_2 = 0;
                    $orden->fecha_2 = null;
                    $orden->estado_3 = 0;
                    $orden->fecha_3 = null;
                    $orden->pago_a_propi = $pagopropi->pago_a_encargos;
                    $orden->nota_encargo = $request->nota;

                    $orden->save();

                    // obtener direccion
                    $d = Direccion::where('user_id', $request->userid)->where('seleccionado', 1)->first();

                    // guardar la direccion
                    $dir = new OrdenesEncargoDireccion();
                    $dir->ordenes_encargo_id = $orden->id;
                    $dir->zonas_id = $d->zonas_id;
                    $dir->nombre = $d->nombre;
                    $dir->direccion = $d->direccion;
                    $dir->numero_casa = $d->numero_casa;
                    $dir->punto_referencia = $d->punto_referencia;
                    $dir->latitud = $d->latitud;
                    $dir->longitud = $d->longitud;
                    $dir->latitud_real = $d->latitud_real;
                    $dir->longitud_real = $d->longitud_real;
                    $dir->revisado = $d->revisado;

                    $dir->save();
 
                     // guardar todos los productos de esa orden
                     foreach($producto as $p){
 
                        // obtener nombre de ese producto y descripcion para tener registro
                        $ppd = ProductoCategoriaNegocio::where('id', $p->producto_cate_nego_id)->first();

                        $data = array('ordenes_encargo_id' => $orden->id,
                                    'producto_cate_nego_id' => $p->producto_cate_nego_id,
                                    'cantidad' => $p->cantidad,
                                    'nota' => $p->nota_producto,
                                    'precio' => $ppd->precio,
                                    'nombre' => $ppd->nombre,
                                    'descripcion' => $ppd->descripcion);
                            OrdenesEncargoProducto::insert($data);
                    }  

                    // hoy borrar el carrito de encargos
                    if($carrito = CarritoEncargo::where('users_id', $request->userid)->first()){
                        CarritoEncargoProducto::where('carrito_encargo_id', $carrito->id)->delete();
                        CarritoEncargo::where('users_id', $request->userid)->delete();
                    }

                    DB::commit();
                    
                    return ['success' => 1];

                }else{
                    return ['success' => 2];
                }
            
            }catch(\Error $e){
                DB::rollback();

                return [
                    'success' => 4
                ];
            }
        }
    }


    // ver lista de encargos
    public function verListaDeEncargos(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'userid' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'userid.required' => 'El id del usuario es requerido.'  
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  
            
            if(User::where('id', $request->userid)->first()){
                $orden = DB::table('ordenes_encargo AS o')
                    ->join('encargos AS e', 'e.id', '=', 'o.encargos_id')
                    ->select('o.id', 'e.nombre',
                    'e.fecha_finaliza', 'o.precio_envio', 
                    'o.revisado', 'o.precio_subtotal', 'o.fecha_orden', 
                    'e.fecha_entrega', 'o.nota_encargo', 'e.fecha_estimada') 
                    ->where('o.users_id', $request->userid)
                    ->where('o.visible_cliente', 1)
                    ->get();
                  
                foreach($orden as $o){                    
                    // fecha estimada de entrega al cliente
                    $o->fecha_finaliza = date("h:i A d-m-Y", strtotime($o->fecha_entrega)); 

                    $o->fecha_orden = date("h:i A d-m-Y", strtotime($o->fecha_orden)); 

                    $total = $o->precio_subtotal + $o->precio_envio;

                    $o->precio_envio = number_format((float)$o->precio_envio, 2, '.', '');
                    
                    $o->precio_total = number_format((float)$total, 2, '.', '');

                    // tipos de estado
                    if($o->revisado == 1){
                        $o->tipoestado = "Pendiente";
                    }else if($o->revisado == 2){
                        $o->tipoestado = "En Proceso";
                    }else if($o->revisado == 3){
                        $o->tipoestado = "En Entrega";
                    }else if($o->revisado == 4){
                        $o->tipoestado = "Entregado";
                    }else if($o->revisado == 5){
                        $o->tipoestado = "Cancelado";
                    }else{
                        $o->tipoestado = "Pendiente";
                    }   
                    
                    // cambiar formato a fecha de entrega

                    
                    /*setlocale(LC_ALL, 'es_ES');
                    $mesfechaf = date("d-m-Y", strtotime($o->fecha_entrega));
                    $fechaf = Carbon::parse($mesfechaf);
                    $fechaf->format("F"); 
                    $mesf = $fechaf->formatLocalized('%B');*/

                    //$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                    //$fechaf = Carbon::parse($o->fecha_entrega);
                    //$mesf = $meses[($fechaf->format('n')) - 1];
                    
                   // $dianumerof = date("d", strtotime($o->fecha_entrega));
                    //$horaf = date("h:i A", strtotime($o->fecha_entrega));               

                    //$o->fecha_entrega = $dianumerof . " de " . $mesf . " a las " . $horaf;
                      $o->fecha_entrega = $o->fecha_estimada; // un texto para poner la fecha
                                         
                    $o->direccion = OrdenesEncargoDireccion::where('ordenes_encargo_id', $o->id)->pluck('direccion')->first();
                }

                return ['success' => 1, 'ordenes' => $orden];
            }else{
                return ['success' => 2];
            }
        }
    }
  
    public function buscadorProductoPorEncargo(Request $request){

        if($request->isMethod('post')){ 
            $reglaDatos = array(                
                'encargoid' => 'required',   
                'nombre' => 'required',             
            );    
                  
            $mensajeDatos = array(                                      
                'encargoid.required' => 'El id del encargo es requerido', 
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

            $a = $request->nombre;
 
          

            $producto = DB::table('lista_encargo AS le')
            ->join('lista_producto_encargo AS lp', 'lp.lista_encargo_id', '=', 'le.id')
            ->join('producto_categoria_negocio AS pc', 'pc.id', '=', 'lp.producto_cate_nego_id')        
            ->select('lp.id', 'pc.id AS idproducto', 'pc.nombre', 'pc.descripcion', 'pc.imagen', 'pc.precio')
            ->where('le.encargos_id', $request->encargoid)
            //->where('pc.nombre', 'like', '%' . $request->nombre . '%')     
            ->where('le.activo', 1) // categoria activar
            ->where('lp.activo', 1) // producto activo
            ->where(function ($query) use ($a) {
                $query->where('pc.nombre', 'like', '%' . $a . '%')
                      ->orWhere('pc.descripcion', 'like', '%' . $a . '%');
            })
            ->orderBy('le.posicion', 'ASC')
            ->get(); 
             
            foreach($producto as $p){

                // buscar categoria
                $categoria = DB::table('categorias_negocios AS c')
                ->join('producto_categoria_negocio AS pc', 'pc.categorias_negocio_id', '=', 'c.id')
                ->select('c.nombre')
                ->where('pc.id', $p->idproducto)
                ->first();

                if($categoria != null){
                    $p->nombrecategoria = $categoria->nombre;
                }else{
                    $p->nombrecategoria = "";
                }
            }

            return ['success' => 1, 'productos' => $producto];
        }
    }
   

    // ver lista de productos del encargo
    public function verListaProductosDeEncargo(Request $request){
        
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'orden_encargoid' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'orden_encargoid.required' => 'El id de la orden del encargo es requerido.'  
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  


            $producto = DB::table('ordenes_encargo AS le')    
                ->join('ordenes_encargo_producto AS op', 'op.ordenes_encargo_id', '=', 'le.id')
                ->join('producto_categoria_negocio AS pc', 'pc.id', '=', 'op.producto_cate_nego_id')
                ->select('op.id', 'pc.nombre', 'pc.imagen', 'pc.precio', 'op.cantidad')
                ->where('le.id', $request->orden_encargoid)                
                ->orderBy('op.id', 'ASC')
                ->get();

                                
                foreach($producto as $p){
                    $cantidad = $p->cantidad;
                    $precio = $p->precio;
                    $multi = $cantidad * $precio;
                    $p->multiplicado = number_format((float)$multi, 2, '.', '');
                }

                return ['success' => 1, 'producto' => $producto];
        }
    }

    // ver producto individual del encagado
    public function verProductoDelEncargoIndividual(Request $request){

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
            ->select('pc.id', 'pc.nombre', 'pc.imagen', 'pc.precio', 'pc.descripcion', 'pc.utiliza_nota', 'op.cantidad', 'op.nota')
            ->where('op.id', $request->productoid)
            ->orderBy('op.id', 'ASC')
            ->get();

            foreach($producto as $p){
                $cantidad = $p->cantidad;
                $precio = $p->precio;
                $multi = $cantidad * $precio;
                $p->multiplicado = number_format((float)$multi, 2, '.', '');
            }

            return ['success' => 1, 'producto' => $producto];
        }
    }

    // actualizar producto del encargo
    public function actualizarProductoDelEncargo(Request $request){
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'productoid' => 'required', // es el id de la fila de ese producto
                'cantidad' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'productoid.required' => 'El id del producto es requerido.',
                'cantidad.required' => 'cantidad es requerida' 
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            if(OrdenesEncargoProducto::where('id', $request->productoid)->first()){

                $nota = "";
                if($request->nota != null){
                    $nota = $request->nota;
                }

                OrdenesEncargoProducto::where('id', $request->productoid)->update(['cantidad' => $request->cantidad, 'nota' => $nota]);

                return ['success' => 1];

            }else{
                return ['success' => 2];
            }          
        }
    }


    // cancelar el encargo
    public function cancelarEncargo(Request $request){

        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'encargoid' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'encargoid.required' => 'El id del encargoid es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }  

            if($oe = OrdenesEncargo::where('id', $request->encargoid)->first()){
                   
                if($oe->revisado == 1){ // puede cancelar
                    OrdenesEncargo::where('id', $request->encargoid)->update(['revisado' => 5, 
                    'visible_cliente' => 0, 'visible_propietario' => 0, 'visible_motorista' => 0, 'cancelado_por' => 1]);

                    return ['success' => 1];
                }else if($oe->revisado == 5){

                    // fue cancelada por el administrador, asi que solo setear vista usuario
                    OrdenesEncargo::where('id', $request->encargoid)->update(['visible_cliente' => 0]);

                    return ['success' => 1];
                }
                else{
                    // no puede ser cancelada
                    return ['success' => 2];
                }
               

            }else{
                return ['success' => 3];
            }          
        }
    }

    // completar encargo
    public function completarEncargo(Request $request){
        
        if($request->isMethod('post')){ 

            // validaciones para los datos
            $reglaDatos = array(
                'encargoid' => 'required',
                'calificacion' => 'required'
            );
        
            $mensajeDatos = array(                                      
                'encargoid.required' => 'El id del encargoid es requerido.',
                'calificacion.required' => 'La calificacion es requerido.'
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );

            if($validarDatos->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

            if($oo = OrdenesEncargo::where('id', $request->encargoid)->first()){
                   
                OrdenesEncargo::where('id', $request->encargoid)->update(['visible_cliente' => 0]);

                if($oo->calificacion == 0){
                    OrdenesEncargo::where('id', $request->encargoid)->update(['calificacion' => $request->calificacion, 'mensaje' => $request->mensaje]);
                }

                return ['success' => 1];

            }else{
                return ['success' => 0];
            }          
        }
    }


    public function verProductoDeCarrito(Request $request){
       
        if($request->isMethod('post')){ 
            $reglaDatos = array(                
                'id' => 'required', // id de la fila de ese producto del carrito
            );
                  
            $mensajeDatos = array(                              
                'id.required' => 'El id es requerido',
                );

            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos);

            if($validarDatos->fails())
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }

           
            // buscar si tiene carrito
            if($data = CarritoEncargoProducto::where('id', $request->id)->first()){

                // informacion del producto + cantidad elegida
                $producto = DB::table('carrito_encargo_pro AS o')
                ->join('producto_categoria_negocio AS p', 'p.id', '=', 'o.producto_cate_nego_id')
                ->select('o.id', 'o.cantidad', 'o.nota_producto', 'p.imagen', 'p.utiliza_nota', 'p.nota', 'p.precio', 'p.nombre', 'p.descripcion')
                ->where('o.id', $request->id)
                ->get();
               
                // obtener nombre de la categoria
                $dd = ProductoCategoriaNegocio::where('id', $data->producto_cate_nego_id)->first();
                $categoria = CategoriasNegocio::where('id', $dd->categorias_negocio_id)->pluck('nombre')->first();

                return [
                    'success' => 1,
                    'producto' => $producto,
                    'categoria' => $categoria
                ];

                
            }else{
                return [
                    'success' => 2 // producto no encontrado
                ];
            }
        }
    }


    public function verProductoDeCarritoActualizar(Request $request){
        if($request->isMethod('post')){ 
           
            $reglaDatos = array(
                'id' => 'required', // id fila de ese producto
                'cantidad' => 'required'
            );
            $mensajeDatos = array(
                'id.required' => 'El id del usuario es requerido',
                'cantidad.required' => 'La cantidad es requerido'
            );
            $validarDatos = Validator::make($request->all(), $reglaDatos, $mensajeDatos );
            if($validarDatos->fails())
            {
                return [
                    'success' => 0, 
                    'message' => $validarDatos->errors()->all()
                ];
            }
            
            if(CarritoEncargoProducto::where('id', $request->id)->first()){                
                
                CarritoEncargoProducto::where('id', $request->id)->update(['cantidad' => $request->cantidad,
                'nota_producto' => $request->nota]);

                return [
                    'success' => 1 // cantidad actualizada
                ];
                
            }else{                    
                return [
                    'success' => 2 //producto no encontrado
                ];
            }
              
        }    
    }

}
