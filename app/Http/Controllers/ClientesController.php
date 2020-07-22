<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\User;
use App\Direccion;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\NumeroSMS;
use Illuminate\Support\Carbon;
use App\Zonas;
use App\Ordenes;

class ClientesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    } 

    // lista de usuarios cliente
    public function index(){
        return view('backend.paginas.cliente.listacliente');
    }

    // tabla para ver clientes
    public function clienteTabla(){
        
        $fecha = Carbon::now('America/El_Salvador');

        $cliente = DB::table('users AS u')
        ->join('zonas AS z', 'z.id', '=', 'u.zonas_id')
        ->select('u.id','u.name AS nombre', 'u.activo', 'z.identificador', 
        'u.phone AS telefono', 'u.email AS correo', 'u.fecha')
        ->whereDate('u.fecha', $fecha)
        ->get();

        return view('backend.paginas.cliente.tablas.tablacliente', compact('cliente'));
    } 
 
     // lista de numeros registrados
     public function index2(){
        return view('backend.paginas.temporales.listatemporales');
    }

    // tabla para ver temporales
    public function tablaTemporales(){
        
        $registro = DB::table('numeros_sms')
        ->latest('id')
        ->take(100)
        ->get();
        
        return view('backend.paginas.temporales.tablas.tablatemporales', compact('registro'));
    }

    // nuevo registro de numeros temporales
    public function nuevoRegistro(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'area' => 'required',
                'numero' => 'required'                
            );

            $messages = array(                                      
                'area.required' => 'El area es requerido.',
                'numero.required' => 'el numero es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if($validator->fails() )
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            $codigo = '';
            $pattern = '1234567890';
            $max = strlen($pattern)-1; 
            for($i=0;$i <6; $i++)           
            {
                $codigo .= $pattern{mt_rand(0,$max)};
            }

            $fecha = Carbon::now('America/El_Salvador');

            $n = new NumeroSMS();
            $n->area = $request->area;
            $n->numero = $request->numero;
            $n->codigo = $codigo;
            $n->codigo_fijo = $codigo;
            $n->contador = 0;
            $n->fecha = $fecha;
            if($n->save()){
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }

        }
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

    // historial del cliente
    public function historialCliente(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'                
            );

            $messages = array(                                      
                'id.required' => 'El id cliente es requerido.'
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
                // cuantas veces ha ordenado
                $total = Ordenes::where('users_id', $request->id)->count();
                $completadas = Ordenes::where('users_id', $request->id)->where('estado_7', 1)->count();
                $cancelocliente = Ordenes::where('users_id', $request->id)->where('cancelado_cliente', 1)->count();
                $cancelopropi = Ordenes::where('users_id', $request->id)->where('cancelado_propietario', 1)->count();

                $dato = Ordenes::where('users_id', $request->id)->where('estado_7', 1)->get(); 
                $gastado = collect($dato)->sum('precio_total');

                $gastado = number_format((float)$gastado, 2, '.', '');  
            
                return ['success' => 1, 'total' => $total, 'completadas' => $completadas, 
                        'cancelocliente' => $cancelocliente, 'cancelopropi' => $cancelopropi,
                        'gastado' => $gastado];
 
            }else{
                return ['success' => 2];
            }
        }
    }

     // informacion registro de numero temporal
     public function infoNumTemporal(Request $request){
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

            if($info = NumeroSMS::where('id', $request->id)->first()){
                return ['success' => 1, 'info' => $info];
            }else{
                return ['success' => 2];
            }
        }
    }

    public function editarRegistro(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
                'area' => 'required',
                'numero' => 'required'
            );

            $messages = array( 
                'id.required' => 'El id es requerido.',
                'area.required' => 'El Area es requerido.',
                'numero.required' => 'Numero es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if($validator->fails() )
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
 
            if(NumeroSMS::where('id', $request->id)->first()){
                NumeroSMS::where('id', $request->id)->update(['area' => $request->area, 'numero' => $request->numero]);
            
                return ['success'=>1];
            }else{
                return ['success'=>2];
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

                User::where('id', $request->id)->update(['activo' => $request->toggle,
                'name' => $request->nombre, 'email' => $request->correo, 'codigo_correo' => $request->codigo]);
            
                return ['success'=>1]; 
            }else{
                return ['success'=>2];
            }
        }
    }

    // vista de direcciones
    public function direccionesCliente($id){

        $nombre = User::where('id', $id)->pluck('name')->first();

        return view('backend.paginas.cliente.listaclientedireccion', compact('id', 'nombre'));
    }

    // tabla de direcciones
    public function direccionesTabla($id){        
        $direccion = DB::table('direccion_usuario AS d')            
        ->join('zonas AS z', 'z.id', '=', 'd.zonas_id')              
        ->select('d.id', 'd.nombre', 'd.seleccionado', 'z.nombre AS nombreZona')
        ->where('d.user_id', $id)
        ->orderBy('d.seleccionado', 'desc')
        ->get();
        return view('backend.paginas.cliente.tablas.tabladireccion', compact('direccion'));
    } 
 

    // ver ubicacion del usuario en mapa
    public function clienteUbicacion($id){
        $d = Direccion::where('id', $id)->first();

        $latitud = $d->latitud;
        $longitud = $d->longitud;

        $api = "AIzaSyB-Iz6I6GtO09PaXGSQxZCjIibU_Li7yOM";
        return view('backend.paginas.cliente.mapacliente', compact('latitud', 'longitud', 'api'));
    }   

    public function clienteUbicacion2($id){
        
        $d = Direccion::where('id', $id)->first();

        $latitud = $d->latitud_real;
        $longitud = $d->longitud_real;

        $api = "AIzaSyB-Iz6I6GtO09PaXGSQxZCjIibU_Li7yOM";
        return view('backend.paginas.cliente.mapacliente', compact('latitud', 'longitud', 'api'));
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
            ->select('d.id', 'd.nombre', 'd.direccion', 'd.numero_casa',
                    'd.punto_referencia', 'd.seleccionado',
                    'z.identificador', 'd.latitud_real', 'd.longitud_real', 
                    'd.latitud', 'd.longitud', 'd.revisado')
            ->where('d.id', $request->id)
            ->first();

            return ['success' => 1, 'direccion'=>$direccion];
           }else{
            return ['success' => 2];
           }
        }  
    }


    // vista para buscar un cliente
    public function vistaBuscarCliente(Request $request){
        return view('backend.paginas.cliente.vistabuscarcliente');
    }

    // buscar cliente
    public function buscarClienteConNumero($id){

        $info = User::where('phone', $id)->get();

        foreach($info as $l){ 
            $l->nombrezona = Zonas::where('id', $l->zonas_id)->pluck('identificador')->first();

            $l->fecha = date("d-m-Y h:i A", strtotime($l->fecha));
        }

        return view('backend.paginas.cliente.tablas.tablaclienteinfo', compact('info'));
    } 


    // actualizar la direccion del cliente
    public function actualizarDireccionCliente(Request $request){
        
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

            $numcasa = "";
            if($request->numcasa != null){
                $numcasa = $request->numcasa;
            }

            $referencia = "";
            if($request->referencia != null){
                $referencia = $request->referencia;
            }

            $latitud = "";
            if($request->latitud != null){
                $latitud = $request->latitud;
            }

            $longitud = "";
            if($request->longitud != null){
                $longitud = $request->longitud;
            }

            $latitudreal = "";
            if($request->latitudreal != null){
                $latitudreal = $request->latitudreal;
            }

            $longitudreal = "";
            if($request->longitudreal != null){
                $longitudreal = $request->longitudreal;
            } 
            
            Direccion::where('id', $request->id)->update([
                'nombre' => $request->nombre, 'direccion' => $request->direccion,
                'numero_casa' => $numcasa, 'punto_referencia' => $referencia,
                'latitud' => $latitud, 'longitud' => $longitud,
                'latitud_real' => $latitudreal, 'longitud_real' => $longitudreal,
                'revisado' => $request->verificado
                ]);
          
            return ['success' => 1];
           }else{
            return ['success' => 2]; // direccion no encontrada
           }
        }  
    }

     

}
