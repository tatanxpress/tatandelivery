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
 
        // todas las ordenes completadas
        $orden = DB::table('ordenes')
        ->where('estado_5', 1)
        ->get(); 

        // tabla de servicios pagados
        $pagoordenes = DB::table('servicio_pago')
        ->get(); 

        // tabla de motoristas pagados
        $pagomotorista = DB::table('motorista_pago')
        ->get(); 

        // total de productos agregados
        $totalproducto = DB::table('producto')
        ->count();

        // total de servicios agregados
        $totalservicio = DB::table('servicios')
        ->count();

        // total de servicios inactivos
        $totalservicioinactivo = DB::table('servicios')
        ->where('activo', 1)
        ->count();
    
        // total de ordenes canceladas por cliente
        $totalordencanceladacliente = DB::table('ordenes')
        ->where('cancelado_cliente', 1)
        ->count();

        // total de ordenes canceladas por el propietario
        $totalordencanceladapropietario = DB::table('ordenes')
        ->where('cancelado_propietario', 1)
        ->count();

        // total de motoristas agregados
        $totalmotorista = DB::table('motoristas')
        ->count();

        $ts = collect($pagoordenes)->sum('pago');
        $tm = collect($pagomotorista)->sum('pago'); 

        $totalpagoservicio = number_format((float)$ts, 2, '.', '');
        $totalpagomotorista = number_format((float)$tm, 2, '.', '');
 
        foreach($orden as $o){
           $totaldineroordenes = number_format((float) $totaldineroordenes + $o->precio_total, 2, '.', '');
           $totaldineroenvios = number_format((float)$totaldineroenvios + $o->precio_envio, 2, '.', '');
        }

        return view('backend.paginas.inicio', compact('totaldineroordenes', 'totaldineroenvios', 
        'totalpagoservicio', 'totalpagomotorista', 'totalproducto', 'totalservicio', 'totalmotorista',
        'totalservicioinactivo', 'totalordencanceladacliente', 'totalordencanceladapropietario'));
    }

    // mostrar inicio del backend, y enviar nombre 
    public function index()
    {
        $nombre = Auth::user()->nombre; // nombre de quien inicio sesion
        return view('backend.index', compact('nombre')); 
    }
}
