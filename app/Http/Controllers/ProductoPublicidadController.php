<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\ProductoPublicidad;
use App\Publicidad;
use App\Servicios;
use Illuminate\Support\Facades\DB; 

class ProductoPublicidadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
 
    // id publicidad, id servicio 
    public function index($idservicio, $idpro){

        $dato = Servicios::where('id', $idservicio)->first();

        $nombre = $dato->nombre;
        $identificador = $dato->identificador;

        return view('backend.paginas.publicidad.listapromocionproducto', compact('idservicio', 'idpro', 'nombre', 'identificador'));
    }  

    // tabla de promociones o publicidad
    public function productoPromocion($ids){

        $producto = DB::table('publicidad_producto AS pp')
        ->join('publicidad AS p', 'p.id', '=', 'pp.publicidad_id')
        ->join('producto AS pro', 'pro.id', '=', 'pp.producto_id')
        ->select('pp.id', 'pp.publicidad_id', 'pro.precio', 'p.nombre', 'pro.nombre AS productoNombre', 'pro.descripcion AS descripcion')
        ->where('pp.publicidad_id', $ids)
        ->get(); 

        return view('backend.paginas.publicidad.tablas.tablapromocionproducto', compact('producto'));
    }

    // para ver los productos del servicio
     // id publicidad, id servicio 
     public function index2($idservicio, $idpromo){


        $dato = Servicios::where('id', $idservicio)->first();

        $nombre = $dato->nombre;
        $identificador = $dato->identificador;

        return view('backend.paginas.publicidad.listaproductoservicio', compact('idservicio', 'nombre', 'idpromo', 'identificador'));
    }   
 
    // tabla producto del servicio
    public function productoServicio($ids){
        
        $producto = DB::table('servicios AS s')
        ->join('servicios_tipo AS st', 'st.servicios_1_id', '=', 's.id')
        ->join('producto AS pro', 'pro.servicios_tipo_id', '=', 'st.id')
        ->select('s.id', 'pro.id AS productoid', 'st.nombre AS categoria', 'pro.nombre', 'pro.descripcion', 'pro.precio')
        ->orderBy('st.nombre', 'ASC')
        ->where('s.id', $ids)
        ->get(); 

        return view('backend.paginas.publicidad.tablas.tablaproductoservicio', compact('producto'));
    }


    // revision de la promocion si ya tiene un producto asignado o no
    public function revision(Request $request){
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
 
            $promo = DB::table('publicidad_producto')
            ->where('publicidad_id', $request->id)
            ->get();
           
            if(count($promo) > 0){
                   // sacar nombre del servicio, con algun producto
                   $pro = DB::table('publicidad_producto')
                   ->where('publicidad_id', $request->id)
                   ->first();
                   
                   $datos = DB::table('servicios AS s')
                   ->join('servicios_tipo AS st', 'st.servicios_1_id', '=', 's.id')
                   ->join('producto AS p', 'p.servicios_tipo_id', '=', 'st.id')
                   ->select('p.id', 's.id AS idservicio')
                   ->where('p.id', $pro->producto_id)
                   ->first();

                return ['success' => 1, 'idservicio' => $datos->idservicio];
            }else{
                return ['success' => 2];
            }
        } 
    }

    // borrar producto de publicidad
    public function borrar(Request $request){
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

            ProductoPublicidad::where('id', $request->id)->delete();

            return ['success' => 1];
        } 
    }

    // nuevo registro de promocion y su producto id
    public function nuevo(Request $request){
        if($request->isMethod('post')){

            $regla = array( 
                'idpromo' => 'required',
                'idproducto' => 'required',
            );

            $mensaje = array(
                'idpromo.required' => 'id promo es requerido',
                'idproducto.required' => 'id producto es requerido',
                );
 

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            } 

            if(ProductoPublicidad::where('publicidad_id', $request->idpromo)->where('producto_id', $request->idproducto)->first()){
                return ['success' => 1];
            }

            $p = new ProductoPublicidad();
            $p->publicidad_id = $request->idpromo;
            $p->producto_id = $request->idproducto;
            if($p->save()){
                return ['success' => 2];
            }else{
                return ['success' => 3];
            }
        } 
    }
}
 