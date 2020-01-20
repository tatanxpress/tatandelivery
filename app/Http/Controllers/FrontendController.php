<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{

    // pagina inicio
    public function index(){
        return view('frontend.index');
    }

    // pagina informacion de quienes somos
    public function verSomos(){
        return view('frontend.paginas.somos');
    }

    // pagina de preguntas frecuentes
    public function verFAQ(){
        return view('frontend.paginas.faq');
    }

}
