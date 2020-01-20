<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\User;
use App\Direccion;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ClientesController extends Controller
{

    // lista de usuarios cliente
    public function index(){
        return view('backend.paginas.cliente.listacliente');
    }

    // tabla para ver clientes
    public function clienteTabla(){
        
        $cliente = DB::table('users AS u')
        ->join('zonas AS z', 'z.id', '=', 'u.zonas_id')
        ->select('u.id','u.name AS nombre', 'u.activo', 'z.identificador', 'u.phone AS telefono', 'u.email AS correo', 'u.fecha')
        ->get();

        return view('backend.paginas.cliente.tablas.tablacliente', compact('cliente'));
    }

    // informacion cliente
    public function informacion(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'                
            );

            $messages = array(                                      
                'id.required' => 'El id direccion es requerido.'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if($validator->fails() )
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            if($cliente = User::where('id', $request->id)->first()){
                return ['success' => 1, 'cliente' => $cliente];
            }else{
                return ['success' => 2];
            }
        }
    }

    // editar cliente disponibilidad
    public function editar(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
                'toggle' => 'required'          
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.',
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
 
            if(User::where('id', $request->id)->first()){
                User::where('id', $request->id)->update(['activo' => $request->toggle]);
            
                return ['success'=>1];
            }else{
                return ['success'=>2];
            }
        }
    }

    // vista de direcciones
    public function direccionesCliente($id){
        return view('backend.paginas.cliente.listaclientedireccion', compact('id'));
    }

    // tabla de direcciones
    public function direccionesTabla($id){        
        $direccion = DB::table('direccion_usuario AS d')            
        ->join('zonas AS z', 'z.id', '=', 'd.zonas_id')              
        ->select('d.id', 'd.nombre', 'd.telefono', 'd.seleccionado', 'z.nombre AS nombreZona')
        ->where('d.user_id', $id)
        ->orderBy('d.seleccionado', 'desc')
        ->get();
        return view('backend.paginas.cliente.tablas.tabladireccion', compact('direccion'));
    }

    // ver ubicacion del usuario en mapa
    public function clienteUbicacion($id){
        $mapa = Direccion::where('id', $id)->select('latitud', 'longitud')->first();
        $api = "AIzaSyB-Iz6I6GtO09PaXGSQxZCjIibU_Li7yOM";
        return view('backend.paginas.cliente.mapacliente', compact('mapa', 'api'));
    }   

    // informacion de una direccion
    public function infoDireccion(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'                
            );

            $messages = array(                                      
                'id.required' => 'El id direccion es requerido.'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if($validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

           if(Direccion::where('id', $request->id)->first()){
            $direccion = DB::table('direccion_usuario AS d')            
            ->join('zonas AS z', 'z.id', '=', 'd.zonas_id')              
            ->select('d.nombre', 'd.direccion', 'd.numero_casa',
                    'd.punto_referencia', 'd.telefono', 'd.seleccionado',
                    'z.identificador')
            ->where('d.id', $request->id)
            ->first();

            return ['success' => 1, 'direccion'=>$direccion];
           }else{
            return ['success' => 2];
           }
        }  
    }

}
