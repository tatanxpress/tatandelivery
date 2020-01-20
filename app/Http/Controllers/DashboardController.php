<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Servicios;
use App\Ordenes;
use App\Motoristas;
use App\Revisadores;
use App\Producto;
use Illuminate\Support\Facades\DB; 

class DashboardController extends Controller
{
    // controlador protegido
    public function __construct()
    {
        $this->middleware('auth:admin'); 
    }
 
    // inicio del panel
    public function getInicio(){

        $totaldineroordenes = 0; // total dinero ordenes con estado 5
        $totaldineroenvios = 0; // total dinero envios
 
        $orden = DB::table('ordenes')
        ->where('estado_5', 1)
        ->get(); 

        $pagoordenes = DB::table('servicio_pago')
        ->get(); 

        $pagomotorista = DB::table('motorista_pago')
        ->get(); 

        $totalproducto = DB::table('producto')
        ->count();

        $totalservicio = DB::table('servicios')
        ->count();

        $totalservicioinactivo = DB::table('servicios')
        ->where('activo', 1)
        ->count();

        $totalordentardio = DB::table('ordenes')
        ->where('tardio', 1)
        ->count();

        $totalordencanceladacliente = DB::table('ordenes')
        ->where('cancelado_cliente', 1)
        ->count();

        $totalordencanceladapropietario = DB::table('ordenes')
        ->where('cancelado_propietario', 1)
        ->count();

        $totalmotorista = DB::table('motoristas')
        ->count();

        $productomasvendido = DB::table('ordenes_descripcion AS od')
        ->join('producto AS p', 'p.id', '=', 'od.producto_id')
        ->select('p.id', 'p.nombre')
        ->groupBy('p.id')
        ->orderByRaw('COUNT(*) DESC')
        ->limit(1)
        ->get();

        $nombre = "";
        $idp = 0;
        foreach($productomasvendido as $p){           
            $idp = $p->id;
            $nombre = $p->nombre;
            break;
        }
         
        $datos =  DB::table('servicios AS s')
        ->join('servicios_tipo AS st', 'st.servicios_1_id', '=', 's.id')
        ->join('producto AS p', 'p.servicios_tipo_id', '=', 'st.id')
        ->select('s.identificador', 's.nombre')
        ->where('p.id', $idp)
        ->first();
        
        $identificador = "";
        $servicio = "";
        if($datos != null){
            $identificador = $datos->identificador;
            $servicio = $datos->nombre;
        }
        

        $totalpagoservicio = collect($pagoordenes)->sum('pago');
        $totalpagomotorista = collect($pagomotorista)->sum('pago');        
 
        foreach($orden as $o){
           $totaldineroordenes = number_format((float) $totaldineroordenes + $o->precio_total, 2, '.', '');

           $totaldineroenvios = number_format((float)$totaldineroenvios + $o->precio_envio, 2, '.', '');
        }

        return view('backend.paginas.inicio', compact('totaldineroordenes', 'totaldineroenvios', 
        'totalpagoservicio', 'totalpagomotorista', 'totalproducto', 'totalservicio', 'totalmotorista',
        'totalservicioinactivo', 'totalordentardio', 'totalordencanceladacliente', 'totalordencanceladapropietario',
        'identificador', 'servicio', 'nombre'));
    }

    // mostrar inicio del backend, y enviar nombre 
    public function index()
    {
        $nombre = Auth::user()->nombre;

        return view('backend.index', compact('nombre'));
    }
}
