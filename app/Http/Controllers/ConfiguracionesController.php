<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DineroOrden;
use App\VersionesApp;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Validator;

class ConfiguracionesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    } 

    public function index(){
         
        $limite = DB::table('dinero_orden')->where('id', 1)->pluck('limite')->first();

        return view('backend.paginas.configuracion.listadinerolimite', compact('limite'));
    } 

    public function informacion(Request $request){
        $limite = DB::table('dinero_orden')->where('id', 1)->first();

        return ['success' => 1, 'info' => $limite];
    }

    public function informacionApp(Request $request){
        $app = DB::table('versiones_app')->where('id', 1)->first();

        return ['success' => 1, 'info' => $app];
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

    public function actualizarAppVersion(Request $request){
        if($request->isMethod('post')){   
            
            $regla = array(                 
                'version' => 'required',
                'android' => 'required',
                'iphone' => 'required'
            );

            $mensaje = array(
                'version.required' => 'version es requerido',
                'android.required' => 'version android es requerido',
                'iphone.required' => 'version iphone es requerido'
                );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            } 

            VersionesApp::where('id', 1)->update([             
                'activo' => $request->version,
                'activo_iphone' => $request->versioniphone,
                'android' => $request->android,
                'iphone' => $request->iphone
                ]);

            return ['success' => 1];
        } 
    }

    
}
