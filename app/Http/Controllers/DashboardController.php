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
        
        // total de productos agregados
        $totalproducto = DB::table('producto')
        ->count();

        return view('backend.paginas.inicio', compact('totalproducto'));
    }

    // mostrar inicio del backend, y enviar nombre 
    public function index()
    {
        $nombre = Auth::user()->nombre; // nombre de quien inicio sesion
        return view('backend.index', compact('nombre')); 
    }
}
