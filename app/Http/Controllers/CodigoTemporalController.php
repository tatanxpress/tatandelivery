<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CodigoTemporal;
use App\ActivoSms;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CodigoTemporalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    // lista de codigo temporales de cada intento
    public function index(){        
        return view('backend.paginas.codigotemporal.listacodigo');
    }

    // tabla para ver codigo temporales
    public function codigotabla(){
        
        $codigo = CodigoTemporal::all();

        return view('backend.paginas.codigotemporal.tablas.tablacodigotemporal', compact('codigo'));
    }

    // informacion api twilio
    public function informacion(Request $request){
        if($request->isMethod('post')){   
            
           $activo = ActivoSms::all()->first();
           return ['success'=>1, 'dato'=>$activo];            
        }
    } 

    // editar api sms
    public function editar(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'toggle' => 'required'                        
            );

            $messages = array(                                      
                'toggle.required' => 'El toggle es requerido.'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if($validator->fails() )
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            // solo existe un registro
            ActivoSms::where('id', 1)->update(['activo' => $request->toggle]);

            return ['success' => 1];
        }
    }
}
 