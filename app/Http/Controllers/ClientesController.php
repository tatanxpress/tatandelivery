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
use App\CrediPuntos;
use App\Ciudades;
use App\AreasPermitidas;
use Log;

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

        foreach($cliente as $c){
            $c->fecha = date("d-m-Y h:i A", strtotime($c->fecha));
        } 

        return view('backend.paginas.cliente.tablas.tablacliente', compact('cliente'));
    } 

    public function indexTodos(){
        return view('backend.paginas.cliente.listaclientetodos'); 
    }

    public function clienteTablaTodos(){
        $cliente = DB::table('users AS u')
        ->join('zonas AS z', 'z.id', '=', 'u.zonas_id')
        ->select('u.id','u.name AS nombre', 'u.activo', 'z.identificador', 
        'u.phone AS telefono', 'u.email AS correo', 'u.fecha')
        ->get();

        return view('backend.paginas.cliente.tablas.tablaclientetodos', compact('cliente'));
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
                'name' => $request->nombre, 'email' => $request->correo, 
                'codigo_correo' => $request->codigo, 'activo_tarjeta' => $request->cbcredito]);

                if($request->cbpass == 1){
                    User::where('id', $request->id)->update(['password' => bcrypt('12345678')]);                    
                }
            
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
                    'd.latitud', 'd.longitud', 'd.revisado', 'd.estado', 'd.precio_envio', 
                    'd.mensaje_rechazo', 'd.ganancia_motorista')
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
    public function buscarClienteConNumero($tel){

        $info = User::where('phone', $tel)->get();

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
                'revisado' => $request->verificado, 'estado' => $request->estado,
                'precio_envio' => $request->cargoenvio, 'mensaje_rechazo' => $request->mensaje,
                'ganancia_motorista' => $request->ganmotorista, 
                ]);


          
            return ['success' => 1];
           }else{
            return ['success' => 2]; // direccion no encontrada
           }
        }  
    }

    public function actualizarExtranjero(Request $request){
        
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
                Direccion::where('id', $request->id)->update([
                    'estado' => $request->estado,
                    'precio_envio' => $request->cargoenvio,
                    'mensaje_rechazo' => $request->mensaje,
                    'ganancia_motorista' => $request->ganmotorista, 
                    ]);
          
                return ['success' => 1];
           }else{
            return ['success' => 2]; // direccion no encontrada
           }
        }  
    }


    //** CREDI PUNTOS QUE ESPERAN REVISION **/

    public function vistaCrediPuntos(){
        return view('backend.paginas.credipuntos.listaingresos');
    } 

    public function obtenerListaCrediPuntosClientes(){

        $cliente = DB::table('users AS u')
        ->join('usuarios_credipuntos AS c', 'c.usuario_id', '=', 'u.id')
        ->select('c.id', 'u.name', 'u.phone', 'c.fecha', 'c.credi_puntos', 'c.pago_total',
            'c.comision', 'c.idtransaccion', 'c.codigo', 'c.esreal', 'c.esaprobada')
        ->where('c.revisada', 0)
        ->get();

        foreach($cliente as $c){
            $c->fecha = date("d-m-Y h:i A", strtotime($c->fecha));
        }

        return view('backend.paginas.credipuntos.tablas.tablacredipuntos', compact('cliente'));
    }  



    // lista para quitar credito
    public function indexCreditoParaQuitar(){
        return view('backend.paginas.credipuntos.listaparaquitar');
    }

    public function tablaCreditoParaQuitar($phone){
        $cliente = User::where('phone', $phone)->get(); 
  
        return view('backend.paginas.credipuntos.tablas.tablacliente', compact('cliente'));
    }

    // obtener nombre con el area + numero
    public function buscarClienteAreaNumero(Request $request){

        if($request->isMethod('post')){   
            $rules = array(
                'numero' => 'required' // id de usuarios_credipuntos
            );

            $messages = array( 
                'numero.required' => 'El numero es requerido.',
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if($validator->fails() )
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
 
            if($cc = User::where('phone', $request->numero)->first()){

                return ['success' => 1, 'nombre' => $cc->name];
            }else{
                return ['success'=> 2];
            }
        }

    }

    // aprobar credi puntos al cliente
    public function aprobarCrediPuntos(Request $request){
        if($request->isMethod('post')){   
            $rules = array(
                'id' => 'required' // id de usuarios_credipuntos
            );

            $messages = array( 
                'id.required' => 'El id es requerido.',
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if($validator->fails() )
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
               
            if($cc = CrediPuntos::where('id', $request->id)->first()){

                // registrar que se verifico
                                  
                $fecha = Carbon::now('America/El_Salvador');

                CrediPuntos::where('id', $request->id)->update(['revisada' => 1,
                    'fecha_revisada' => $fecha, 'nota' => $request->nota]);

                if($request->estado == 0){
                    // agregar credito al cliente
                    $actual = User::where('id', $cc->usuario_id)->pluck('monedero')->first();
                    $sumado = $actual + $cc->credi_puntos;

                    User::where('id', $cc->usuario_id)->update(['monedero' => $sumado]);
                }
                
                return ['success' => 1];
            }else{
                return ['success'=> 2];
            }
        }
    }

    // ver credito actual con id, usuarios_credipuntos
    public function verCreditoActual(Request $request){
 
        if($request->isMethod('post')){   
            $rules = array(
                'id' => 'required' // id de usuarios_credipuntos
            );

            $messages = array( 
                'id.required' => 'El id es requerido.',
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if($validator->fails() )
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
 
            if($cc = CrediPuntos::where('id', $request->id)->first()){
             
                // agregar credito al cliente
                $actual = User::where('id', $cc->usuario_id)->pluck('monedero')->first();
                              
                return ['success' => 1, 'monedero' => $actual];
            }else{
                return ['success'=> 2];
            }
        } 
    }

    // por id del cliente
    public function verCreditoActual2(Request $request){
 
        if($request->isMethod('post')){   
            $rules = array(
                'id' => 'required' // id de usuarios_credipuntos
            );

            $messages = array( 
                'id.required' => 'El id es requerido.',
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
             
                // agregar credito al cliente
                $actual = User::where('id', $request->id)->pluck('monedero')->first();
                              
                return ['success' => 1, 'monedero' => $actual];
            }else{
                return ['success'=> 2];
            }
        } 
    }

    public function agregarCreditoManual(Request $request){
        if($request->isMethod('post')){   
            $rules = array(
                'numero' => 'required',
                'credito' => 'required'
            );

            $messages = array( 
                'numero.required' => 'El numero es requerido.',
                'credito.required' => 'credito es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if($validator->fails() )
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
 
            if($uu = User::where('phone', $request->numero)->first()){
                
                DB::beginTransaction();
           
                try {
                    
                    $fecha = Carbon::now('America/El_Salvador');
 
                    $reg = new CrediPuntos;
                    $reg->usuario_id = $uu->id;
                    $reg->credi_puntos = $request->credito;
                    $reg->pago_total = $request->credito;
                    $reg->fecha = $fecha;
                    $reg->nota = $request->nota;
                    $reg->idtransaccion = "Ingreso manual";
                    $reg->codigo = "Ingreso manual";
                    $reg->esreal = 1;
                    $reg->esaprobada = 1;
                    $reg->comision = 0;
                    $reg->revisada = 1;

                    if($reg->save()){

                        $suma = $uu->monedero + $request->credito;
                        User::where('id', $uu->id)->update(['monedero' => $suma]);

                        DB::commit();
                        return ['success' => 1];
                    }else{
                        return ['success' => 2];
                    }
                } catch(\Throwable $e){
                    DB::rollback();
                    return ['success' => 3];
                }

            }else{
                return ['success'=>3];
            }
        }
    }

    // eliminar credito manual
    public function eliminarCreditoManual(Request $request){

        if($request->isMethod('post')){   
            $rules = array(
                'id' => 'required', // id cliente
                'credito' => 'required'
            );
 
            $messages = array( 
                'id.required' => 'El id es requerido.',
                'credito.required' => 'credito es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if($validator->fails() )
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }
 
            if($uu = User::where('id', $request->id)->first()){
                
                DB::beginTransaction();

                $quitar = 0;

                if($request->credito > 0){
                    // es mayor a 0
                    $quitar = $quitar - $request->credito;
                }else{
                    $quitar = $request->credito;
                }

                try {
                    
                    $fecha = Carbon::now('America/El_Salvador');
 
                    $reg = new CrediPuntos;
                    $reg->usuario_id = $request->id;
                    $reg->credi_puntos = $quitar;
                    $reg->pago_total = 0;
                    $reg->fecha = $fecha;
                    $reg->nota = $request->nota;
                    $reg->idtransaccion = "Ingreso manual";
                    $reg->codigo = "Ingreso manual";
                    $reg->esreal = 1;
                    $reg->esaprobada = 1;
                    $reg->comision = 0;
                    $reg->revisada = 1;

                    if($reg->save()){

                        // SIEMPRE VA A RESTAR
                        $suma = $uu->monedero + $quitar;

                        if($suma < 0){
                            $suma = 0;
                        }
                        User::where('id', $uu->id)->update(['monedero' => $suma]);

                        DB::commit();
                        return ['success' => 1];
                    }else{
                        return ['success' => 2];
                    }


                } catch(\Throwable $e){
                    DB::rollback();
                    return ['success' => 3];
                }

            }else{
                return ['success'=>3];
            }
        }

    }

    // ciudades
    public function indexCiudades(){

        $zonas = Zonas::whereNotIn('id', [1,2])->get();
        return view('backend.paginas.ciudades.listaciudades', compact('zonas'));
    }

    public function tablasCiudades(){

        $datos = DB::table('ciudades AS c')
        ->join('zonas AS z', 'z.id', '=', 'c.zonas_id')
        ->select('c.id', 'c.nombre', 'z.identificador')
        ->get();

        return view('backend.paginas.ciudades.tablas.tablaciudades', compact('datos'));
    }


    public function agregarNuevaCiudad(Request $request){

        if($request->isMethod('post')){   
            $rules = array(                
                'zona' => 'required',
                'nombre' => 'required'                
            );

            $messages = array(                                      
                'zona.required' => 'El area es requerido.',
                'nombre.required' => 'el nombre es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if($validator->fails() )
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            $n = new Ciudades();
            $n->nombre = $request->nombre;
            $n->zonas_id = $request->zona;
           
            if($n->save()){
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }

    public function informacionCiudades(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.',
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if($validator->fails() )
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            if($cc = Ciudades::where('id', $request->id)->first()){
                
                return ['success' => 1, 'info' => $cc];
            }else{
                return ['success' => 2];
            }
        }
    }

    public function editarCiudades(Request $request){

        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
                'nombre' => 'required'
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.',
                'nombre.required' => 'El nombre es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if($validator->fails() )
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            if(Ciudades::where('id', $request->id)->first()){

                Ciudades::where('id', $request->id)->update(['nombre' => $request->nombre]);
                
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }

    function borrarCiudad(Request $request){

        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'              
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.'              
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if($validator->fails() )
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            if(Ciudades::where('id', $request->id)->first()){

                Ciudades::where('id', $request->id)->delete();
                
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }
    }


    // extranjeros

    public function indexExtranjeros(){
        return view('backend.paginas.cliente.listaextranjero');
    }

    // mostrarme aquellos usuarios extranjero sin direccion verificada, y no canceladas
    public function tablaExtranjeros(){

        // todos los usuarios 
        $da = DB::table('users')
        ->where('area', '!=', '+503') // no quiero ningun area +503
        ->get();

        $pila = array(); // agregar id del usuario con una direccion sin verificar

        foreach($da as $c){
            // obtener direccion de estos usuarios
            $direcciones = Direccion::where('user_id', $c->id)->get();
            $seguro = true;

            foreach($direcciones as $dd){
                if($dd->estado == 0 && $seguro){ // una direccion sin verificar
                    $seguro = false;
                    array_push($pila, $c->id);
                }
            }
        }

        // Buscar todos los usuarios encontrados pendientes de verificacion

        $datos = DB::table('users')
        ->where('area', '!=', '+503') // no quiero ningun area +503
        ->orderBy('id', 'DESC')
        ->whereIn('id', $pila)
        ->get();
 
        foreach($datos as $d){
            $zona = "";
            $d->fecha = date("d-m-Y h:i A", strtotime($d->fecha));

            $zona = Zonas::where('id', $d->zonas_id)->pluck('identificador')->first();

            $d->identificador = $zona;
        }

        return view('backend.paginas.cliente.tablas.tablaextranjero', compact('datos'));
    }


    // ver todos los credi puntos
    public function verRegistroCredito(){
        return view('backend.paginas.credipuntos.listacrediregistro');
    } 

    // todos los registros de credi puntos
    public function tablaRegistroCredito(){ 

        $cliente = DB::table('users AS u')
        ->join('usuarios_credipuntos AS c', 'c.usuario_id', '=', 'u.id')
        ->select('c.id', 'u.name', 'u.phone', 'c.fecha', 'c.credi_puntos', 'c.pago_total',
            'c.comision', 'c.idtransaccion', 'c.codigo', 'c.esreal', 'c.esaprobada', 'c.nota', 'c.fecha_revisada')
        ->where('c.revisada', 1)
        ->get(); 
 
        foreach($cliente as $c){
            $c->fecha = date("d-m-Y h:i A", strtotime($c->fecha));
            $c->fecha_revisada = date("d-m-Y h:i A", strtotime($c->fecha_revisada));
        }

        return view('backend.paginas.credipuntos.tablas.tablacredipuntosverificados', compact('cliente'));

        return view('backend.paginas.credipuntos.tablas.tablaregistrotodos', compact('datos'));
    }
 
    
    
    // obtener todas las direcciones del usuario extranjero,
    // aqui se vera cual falta por verificar
    public function todasLasDirecciones($id){ // id del usuario
        return view('backend.paginas.cliente.listadireccionextranjero', compact('id'));
    }
 
    // todas las direccion de un cliente
    public function tablaTodasLasDirecciones($id){ // id del usuario

        $datos = Direccion::where('user_id', $id)->where('estado', 0)->get(); // aun no verificado

        return view('backend.paginas.cliente.tablas.tablalistadirecciones', compact('datos'));
    }
 
    public function informacionExtrajero(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required',
            );

            $messages = array(                                      
                'id.required' => 'El id es requerido.',
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if($validator->fails() )
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            if($cc = Direccion::where('id', $request->id)->first()){
                
                return ['success' => 1, 'direccion' => $cc];
            }else{
                return ['success' => 2];
            }
        }
    }

 
    


}
