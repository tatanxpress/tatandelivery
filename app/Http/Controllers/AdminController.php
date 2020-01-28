<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Admin;
use App\Administradores;
  
class AdminController extends Controller
{
    // controlador protegido
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    
    // lista revisadores admin para revisar ordenes pendiente de motorista
    public function index3(){
        return view('backend.paginas.revisador.listaadminrevisador');
    } 

    // tabla lista de admin revisadores
    public function tablaadminrevisador(){ 
         
        $administradores = DB::table('administradores')
        ->get();

        return view('backend.paginas.revisador.tablas.tablaadminrevisador', compact('administradores'));
    } 
 
    // agregar nuevo revisador admin para ver ordenes pendiente
    public function  nuevoadmin(Request $request){
        if($request->isMethod('post')){

            $regla = array(
                'nombre' => 'required',
                'telefono' => 'required',
                'password' => 'required',
            );

            $mensaje = array(
                'nombre.required' => 'Nombre es requerido',
                'telefono.required' => 'telefono es requerido',
                'password.required' => 'Password es requerido'
                );
                
            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ]; 
            }  

            if(Administradores::where('telefono', $request->telefono)->first()){
                return ['success' => 2];
            }
 
            $m = new Administradores();
            $m->nombre = $request->nombre;
            $m->telefono = $request->telefono;
            $m->device_id = "0000";
            $m->password = bcrypt($request->password);
            $m->activo = 1;
            $m->disponible = 0;
           
            if($m->save()){ 

                return ['success' => 3];
            }else{
                return ['success' => 4];
            }                    
            
        }
    }

    public function reseteo(Request $request){
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

            if(Administradores::where('id', $request->id)->first()){
               
                Administradores::where('id', $request->id)->update([
                        'password' => bcrypt('12345678')                  
                        ]);

                        return ['success' => 1];
            
            }else{
                return ['success' => 2];  
            }
        }  
    }

    // informacion de revisador admin
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
 
           
            if($a = Administradores::where('id', $request->id)->first()){

                return ['success' => 1, 'admin' => $a];
               
            }else{
                return ['success' => 3];
            }                    
            
        }
    }
  
    // editar informacion de revisador admin
    public function editar(Request $request){
        if($request->isMethod('post')){

            $regla = array(
                'id' => 'required',
                'activo' => 'required',
                'nombre' => 'required'
            );

            $mensaje = array(
                'id.required' => 'id es requerido',
                'activo.required' => 'activo es requerido',
                'nombre.required' => 'nombre es requerido'
                );
                
            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ]; 
            }
           
            Administradores::where('id', $request->id)->update([
                'activo' => $request->activo,
                'nombre' => $request->nombre
                ]);
                
            return ['success' => 1];
            
        }
    }
   
    // vista de editar datos del administrador
    public function index4(){

        $nombre = Auth::user()->nombre;
        $correo = Auth::user()->email;


        return view('backend.paginas.admin.listaadmin', compact('nombre', 'correo'));
    }
    
    // edita solamente datos del administrador logueado
    public function editardatos(Request $request){
        if($request->isMethod('post')){

            $regla = array(
                'nombre' => 'required',    
                'correo' => 'required'            
            );

            $mensaje = array(
                'nombre.required' => 'nombre es requerido',
                'correo.required' => 'correo es requerido'
                );
                
            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ]; 
            }

            $id = Auth::user()->id;

            Admin::where('id', $id)->update([
                'nombre' => $request->nombre,
                'email' => $request->correo    
                ]);

            return ['success' => 1];
        }
    }

     // edita solamente contrasena
     public function editarpassword(Request $request){
        if($request->isMethod('post')){

            $regla = array(
                'password' => 'required',    
            );

            $mensaje = array(
                'password.required' => 'password es requerido',
                );
                
            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ]; 
            }
 
            $id = Auth::user()->id;

            Admin::where('id', $id)->update([
                'password' => bcrypt($request->password)
                ]);

            return ['success' => 1];
        }
    }


   


}
 