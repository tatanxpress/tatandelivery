<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DineroOrden;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Validator;

class ConfiguracionesController extends Controller
{
    public function index(){
         
        $limite = DB::table('dinero_orden')->where('id', 1)->pluck('limite')->first();

        return view('backend.paginas.configuracion.listadinerolimite', compact('limite'));
    } 

    public function informacion(Request $request){
        $limite = DB::table('dinero_orden')->where('id', 1)->first();

        return ['success' => 1, 'info' => $limite];
    }

    public function actualizar(Request $request){
        if($request->isMethod('post')){   
            
            $regla = array(                 
                'cupones' => 'required',
                'sms' => 'required',
                'correo' => 'required'
            );

            $mensaje = array(
                'cupones.required' => 'Cupones es requerido',
                'sms.required' => 'SMS es requerido',
                'correo.required' => 'Correo es requerido'
                );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            } 

            DineroOrden::where('id', 1)->update([             
                'ver_cupones' => $request->cupones,
                'correo' => $request->correo,
                'activo_sms' => $request->sms
                ]);

            return ['success' => 1];
        }  
    }

    
}
