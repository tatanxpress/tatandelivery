<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Servicios;
use App\TipoServicios;
use App\HorarioServicio;
use App\TiempoAproximado;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ServiciosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    } 

    public function index(){

        // lista tipo servicios
        $tiposervicio = TipoServicios::all();
        return view('backend.paginas.servicios.listaservicio', compact('tiposervicio'));
    }

    // tabla para ver codigo temporales
    public function serviciotabla(){
        
        $servicio = DB::table('servicios AS s')
        ->join('tipo_servicios AS ts', 'ts.id', '=', 's.tipo_servicios_id')          
        ->select('s.id','s.nombre', 's.descripcion', 's.imagen', 
        's.cerrado_emergencia', 's.activo', 's.identificador', 'ts.nombre AS nombreServicio')
        ->get();

        return view('backend.paginas.servicios.tablas.tablaservicio', compact('servicio'));
    }

    // vista agregar servicio
    public function vistaAgregar(Request $request){
           $tiposervicio = TipoServicios::all();
        return view('backend.paginas.servicios.listaservicio', compact('tiposervicio'));
    }

    //nuevo servicio
    public function nuevo(Request $request){
       
        if($request->isMethod('post')){  

            $regla = array( 
                'multa' => 'required',
                'comision' => 'required',
                'nombre' => 'required',
                'identificador' => 'required',
                'descripcion' => 'required',
                'descripcioncorta' => 'required',
                'tiposervicio' => 'required',
                'telefono' => 'required',
                'latitud' => 'required',
                'longitud' => 'required',
                'direccion' => 'required',
                'tipovista' => 'required',
                'tiempoorden' => 'required',
                'tiempo' => 'required',

                'cbenviogratis' => 'required',
                'cbminimo' => 'required',
                'minimocompra' => 'required',
                'cbproducto' => 'required',
                'cbautomatica' => 'required',

                'lunes' => 'required',
                'martes' => 'required',
                'miercoles' => 'required',
                'jueves' => 'required',
                'viernes' => 'required',
                'sabado' => 'required',
                'domingo' => 'required',

                'horalunes1' => 'required',
                'horalunes2' => 'required',
                'horalunes3' => 'required',
                'horalunes4' => 'required',
                'cblunessegunda' => 'required',
                'cbcerradolunes' => 'required',
                
                'horamartes1' => 'required',
                'horamartes2' => 'required',
                'horamartes3' => 'required',
                'horamartes4' => 'required',
                'cbmartessegunda' => 'required',
                'cbcerradomartes' => 'required',
                
                'horamiercoles1' => 'required',
                'horamiercoles2' => 'required',
                'horamiercoles3' => 'required',
                'horamiercoles4' => 'required',
                'cbmiercolessegunda' => 'required',
                'cbcerradomiercoles' => 'required',
                
                'horajueves1' => 'required',
                'horajueves2' => 'required',
                'horajueves3' => 'required',
                'horajueves4' => 'required',
                'cbjuevessegunda' => 'required',
                'cbcerradojueves' => 'required',
                
                'horaviernes1' => 'required',
                'horaviernes2' => 'required',
                'horaviernes3' => 'required',
                'horaviernes4' => 'required',
                'cbviernessegunda' => 'required',
                'cbcerradoviernes' => 'required',
                 
                'horasabado1' => 'required',
                'horasabado2' => 'required',
                'horasabado3' => 'required',
                'horasabado4' => 'required',
                'cbsabadosegunda' => 'required',
                'cbcerradosabado' => 'required',
                
                'horadomingo1' => 'required',
                'horadomingo2' => 'required',
                'horadomingo3' => 'required',
                'horadomingo4' => 'required',
                'cbdomingosegunda' => 'required',
                'cbcerradodomingo' => 'required',

            );

            $mensaje = array(
                'multa.required' => 'comision es requerido',
                'comision.required' => 'comision es requerido',
                'nombre.required' => 'Nombre es requerido',
                'identificador.required' => 'identificador es requerido',
                'descripcion.required' => 'Descripcion es requerida',
                'descripcioncorta.required' => 'descripcion corta es requerido',
                'tiposervicio.required' => 'tipo servicio es requerida',
                'telefono.required' => 'telefono es requerido',
                'latitud.required' => 'latitud es requerida',
                'longitud.required' => 'longitud es requerido',
                'direccion.required' => 'Direccion es requerida',
                'tipovista.required' => 'tipo vista es requerido',
                'tiempoorden.required' => 'tiempo orden es requerido',
                'tiempo.required' => 'tiempo es requerido',

                'cbenviogratis.required' => 'es requerido 1',
                'cbminimo.required' => 'es requerido 2',
                'minimocompra.required' => 'es requerido 3',
                'cbproducto.required' => 'es requerido 4',
                'cbautomatica.required' => 'es requerido 5',

                'lunes.required' => 'es requerido 6',
                'martes.required' => 'es requerido 7',
                'miercoles.required' => 'es requerido 8',
                'jueves.required' => 'es requerido 9',
                'viernes.required' => 'es requerido 10',
                'sabado.required' => 'es requerido 11',
                'domingo.required' => 'es requerido 12',

                'horalunes1.required' => 'es requerido 13',
                'horalunes2.required' => 'es requerido 14',
                'horalunes3.required' => 'es requerido 15',
                'horalunes4.required' => 'es requerido 16',
                'cblunessegunda.required' => 'es requerido 17',
                'cbcerradolunes.required' => 'es requerido 18',

                'horamartes1.required' => 'es requerido 19',
                'horamartes2.required' => 'es requerido 20',
                'horamartes3.required' => 'es requerido 21',
                'horamartes4.required' => 'es requerido 22',
                'cbmartessegunda.required' => 'es requerido 23',
                'cbcerradomartes.required' => 'es requerido 24',

                'horamiercoles1.required' => 'es requerido 25',
                'horamiercoles2.required' => 'es requerido 26',
                'horamiercoles3.required' => 'es requerido 27',
                'horamiercoles4.required' => 'es requerido 28',
                'cbmiercolessegunda.required' => 'es requerido 29',
                'cbcerradomiercoles.required' => 'es requerido 30',

                'horajueves1.required' => 'es requerido 31',
                'horajueves2.required' => 'es requerido 32',
                'horajueves3.required' => 'es requerido 33',
                'horajueves4.required' => 'es requerido 34',
                'cbjuevessegunda.required' => 'es requerido 35',
                'cbcerradojueves.required' => 'es requerido 36',

                'horaviernes1.required' => 'es requerido 37',
                'horaviernes2.required' => 'es requerido 38',
                'horaviernes3.required' => 'es requerido 39',
                'horaviernes4.required' => 'es requerido 40',
                'cbviernessegunda.required' => 'es requerido 41',
                'cbcerradoviernes.required' => 'es requerido 42',

                'horasabado1.required' => 'es requerido 43',
                'horasabado2.required' => 'es requerido 44',
                'horasabado3.required' => 'es requerido 45',
                'horasabado4.required' => 'es requerido 46',
                'cbsabadosegunda.required' => 'es requerido 47',
                'cbcerradosabado.required' => 'es requerido 48',

                'horadomingo1.required' => 'es requerido 49',
                'horadomingo2.required' => 'es requerido 50',
                'horadomingo3.required' => 'es requerido 51',
                'horadomingo4.required' => 'es requerido 52',
                'cbdomingosegunda.required' => 'es requerido 53',
                'cbcerradodomingo.required' => 'es requerido 54',
            );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            } 

            if(Servicios::where('identificador', $request->identificador)->first()){
                return ['success' => 4];
            }

            DB::beginTransaction();
           
            try { 

                $cadena = Str::random(15);
                $tiempo = microtime(); 
                $union = $cadena.$tiempo;
                $nombre = str_replace(' ', '_', $union);
                
                $extension = '.'.$request->logo->getClientOriginalExtension();
                $nombreFoto = $nombre.strtolower($extension);
                $avatar = $request->file('logo'); 
                $upload = Storage::disk('listaservicios')->put($nombreFoto, \File::get($avatar)); 

                $cadena2 = Str::random(15);
                $tiempo2 = microtime(); 
                $union2 = $cadena2.$tiempo2;
                $nombre2 = str_replace(' ', '_', $union2);
                
                $extension2 = '.'.$request->imagen->getClientOriginalExtension();
                $nombreFoto2 = $nombre2.strtolower($extension2);
                $avatar2 = $request->file('imagen'); 
                $upload2 = Storage::disk('listaservicios')->put($nombreFoto2, \File::get($avatar2)); 
     
                if($upload && $upload2){


                    $fecha = Carbon::now('America/El_Salvador');
                
                    $tipo = new Servicios();
                    $tipo->multa = $request->multa;
                    $tipo->comision = $request->comision;
                    $tipo->nombre = $request->nombre;
                    $tipo->identificador = $request->identificador;
                    $tipo->descripcion = $request->descripcion;
                    $tipo->descripcion_corta = $request->descripcioncorta;
                    $tipo->logo = $nombreFoto;
                    $tipo->imagen = $nombreFoto2;
                    $tipo->cerrado_emergencia = 0;
                    $tipo->fecha = $fecha;
                    $tipo->activo = 0; 
                    $tipo->tiempo = $request->tiempo; 
                    $tipo->tipo_servicios_id = $request->tiposervicio;
                    $tipo->envio_gratis = $request->cbenviogratis;
                    $tipo->telefono = $request->telefono;
                    $tipo->latitud = $request->latitud;
                    $tipo->longitud = $request->longitud;
                    $tipo->direccion = $request->direccion;
                    $tipo->tipo_vista = $request->tipovista;
                    $tipo->minimo = $request->minimocompra;
                    $tipo->utiliza_minimo = $request->cbminimo;
                    $tipo->orden_automatica = $request->cbautomatica;
                    $tipo->tiempo_orden_max = $request->tiempoorden;
                    $tipo->producto_visible = $request->cbproducto;
                    $tipo->privado = 0;
                    $tipo->prestar_motorista = 0;

                    $tipo->save();

                    $idservicio = $tipo->id;
                    $tiempo1 = new TiempoAproximado();
                    $tiempo1->servicios_id = $idservicio;
                    $tiempo1->dia = 1;
                    $tiempo1->tiempo = $request->lunes;
                    $tiempo1->save();

                    $tiempo2 = new TiempoAproximado();
                    $tiempo2->servicios_id = $idservicio;
                    $tiempo2->dia = 2;
                    $tiempo2->tiempo = $request->martes;
                    $tiempo2->save();

                    $tiempo3 = new TiempoAproximado();
                    $tiempo3->servicios_id = $idservicio;
                    $tiempo3->dia = 3;
                    $tiempo3->tiempo = $request->miercoles;
                    $tiempo3->save();

                    $tiempo4 = new TiempoAproximado();
                    $tiempo4->servicios_id = $idservicio;
                    $tiempo4->dia = 4;
                    $tiempo4->tiempo = $request->jueves;
                    $tiempo4->save();

                    $tiempo5 = new TiempoAproximado();
                    $tiempo5->servicios_id = $idservicio;
                    $tiempo5->dia = 5;
                    $tiempo5->tiempo = $request->viernes;
                    $tiempo5->save();

                    $tiempo6 = new TiempoAproximado();
                    $tiempo6->servicios_id = $idservicio;
                    $tiempo6->dia = 6;
                    $tiempo6->tiempo = $request->sabado;
                    $tiempo6->save();

                    $tiempo7 = new TiempoAproximado();
                    $tiempo7->servicios_id = $idservicio;
                    $tiempo7->dia = 7;
                    $tiempo7->tiempo = $request->domingo;
                    $tiempo7->save();

                    $hora1 = new HorarioServicio();
                    $hora1->hora1 = $request->horalunes1;
                    $hora1->hora2 = $request->horalunes2;
                    $hora1->hora3 = $request->horalunes3;
                    $hora1->hora4 = $request->horalunes4;
                    $hora1->dia = 1;
                    $hora1->servicios_id = $idservicio;
                    $hora1->segunda_hora = $request->cblunessegunda;
                    $hora1->cerrado = $request->cbcerradolunes;
                    $hora1->save();

                    $hora2 = new HorarioServicio();
                    $hora2->hora1 = $request->horamartes1;
                    $hora2->hora2 = $request->horamartes2;
                    $hora2->hora3 = $request->horamartes3;
                    $hora2->hora4 = $request->horamartes4;
                    $hora2->dia = 2;
                    $hora2->servicios_id = $idservicio;
                    $hora2->segunda_hora = $request->cbmartessegunda;
                    $hora2->cerrado = $request->cbcerradomartes;
                    $hora2->save();

                    $hora3 = new HorarioServicio();
                    $hora3->hora1 = $request->horamiercoles1;
                    $hora3->hora2 = $request->horamiercoles2;
                    $hora3->hora3 = $request->horamiercoles3;
                    $hora3->hora4 = $request->horamiercoles4;
                    $hora3->dia = 3;
                    $hora3->servicios_id = $idservicio;
                    $hora3->segunda_hora = $request->cbmiercolessegunda;
                    $hora3->cerrado = $request->cbcerradomiercoles;
                    $hora3->save();


                    $hora4 = new HorarioServicio();
                    $hora4->hora1 = $request->horajueves1;
                    $hora4->hora2 = $request->horajueves2;
                    $hora4->hora3 = $request->horajueves3;
                    $hora4->hora4 = $request->horajueves4;
                    $hora4->dia = 4;
                    $hora4->servicios_id = $idservicio;
                    $hora4->segunda_hora = $request->cbjuevessegunda;
                    $hora4->cerrado = $request->cbcerradojueves;
                    $hora4->save();

                    $hora5 = new HorarioServicio();
                    $hora5->hora1 = $request->horaviernes1;
                    $hora5->hora2 = $request->horaviernes2;
                    $hora5->hora3 = $request->horaviernes3;
                    $hora5->hora4 = $request->horaviernes4;
                    $hora5->dia = 5;
                    $hora5->servicios_id = $idservicio;
                    $hora5->segunda_hora = $request->cbviernessegunda;
                    $hora5->cerrado = $request->cbcerradoviernes;
                    $hora5->save();


                    $hora6 = new HorarioServicio();
                    $hora6->hora1 = $request->horasabado1;
                    $hora6->hora2 = $request->horasabado2;
                    $hora6->hora3 = $request->horasabado3;
                    $hora6->hora4 = $request->horasabado4;
                    $hora6->dia = 6;
                    $hora6->servicios_id = $idservicio;
                    $hora6->segunda_hora = $request->cbsabadosegunda;
                    $hora6->cerrado = $request->cbcerradosabado;
                    $hora6->save();


                    $hora7 = new HorarioServicio();
                    $hora7->hora1 = $request->horadomingo1;
                    $hora7->hora2 = $request->horadomingo2;
                    $hora7->hora3 = $request->horadomingo3;
                    $hora7->hora4 = $request->horadomingo4;
                    $hora7->dia = 7;
                    $hora7->servicios_id = $idservicio;
                    $hora7->segunda_hora = $request->cbdomingosegunda;
                    $hora7->cerrado = $request->cbcerradodomingo;
                    $hora7->save();

                    DB::commit();            

                    return ['success' => 1];
                }else{
                    return ['success' => 2];
                }

            } catch(\Throwable $e){
                DB::rollback();
                return ['success' => 3, 'error' => $e];
            }
        }
    }

    // informacion del servicio
    public function informacionServicio(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'
            );    

            $messages = array(                                      
                'id.required' => 'El ID tipo servicio es requerido.'                        
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            if($servicio = Servicios::where('id', $request->id)->first()){

                $tipo = TipoServicios::all();

                return['success' => 1, 'servicio' => $servicio, 'tipo' => $tipo];
            }else{
                return['success' => 2];
            }
        }
    }

    // informacion sobre tiempo
    public function informacionTiempo(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'
            );    

            $messages = array(                                      
                'id.required' => 'El ID tipo servicio es requerido.'                        
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            if(TiempoAproximado::where('id', $request->id)->first()){

                $tiempo = TiempoAproximado::where('servicios_id', $request->id)->get();

                return['success' => 1, 'tiempo' => $tiempo];
            }else{
                return['success' => 2];
            }
        }
    }

    // informacion sobre horarios
    public function informacionHorario(Request $request){
        if($request->isMethod('post')){   
            $rules = array(                
                'id' => 'required'
            );

            $messages = array(
                'id.required' => 'El ID tipo servicio es requerido.'                        
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0, 
                    'message' => $validator->errors()->all()
                ];
            }

            if(HorarioServicio::where('id', $request->id)->first()){

                $horario = HorarioServicio::where('servicios_id', $request->id)->get();

                return['success' => 1, 'horario' => $horario];
            }else{
                return['success' => 2];
            }
        }
    }

    // editar servicio
    public function editarServicio(Request $request){       

        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'comision' => 'required',
                'multa' => 'required',
                'tiempo' => 'required',
                'nombre' => 'required',
                'identificador' => 'required',
                'descripcion' => 'required',
                'descripcioncorta' => 'required',
                'tiposervicio' => 'required',
                'telefono' => 'required',
                'latitud' => 'required',
                'longitud' => 'required',
                'direccion' => 'required',
                'tipovista' => 'required',
                'tiempoorden' => 'required',
                'cbenviogratis' => 'required',
                'cbminimo' => 'required',
                'minimocompra' => 'required',
                'cbproducto' => 'required',
                'cbautomatica' => 'required',
                'cbcerradoemergencia' => 'required',
                'cbactivo' => 'required',
                'prestado' => 'required',
                'privado' => 'required'
            );

            $messages = array(   
                'id.required' => 'El id es requerido',
                'comision.required' => 'comision es requerido',
                'multa.required' => 'multa es requerido',
                'tiempo.required' => 'tiempo es requerido',
                'nombre.required' => 'El nombre es requerido',
                'identificador.required' => 'El identificador es requerido',
                'descripcion.required' => 'la descripcion es requerido',
                'descripcioncorta.required' => 'descripcion corta requerida',
                'tiposervicio.required' => 'tipo servicio requerido',
                'telefono.required' => 'telefono requerido',
                'latitud.required' => 'latitud requerido',
                'longitud.required' => 'longitud requerido',
                'direccion.required' => 'direccion requerido',
                'tipovista.required' => 'tipo vista requerida',
                'tiempoorden.required' => 'tiempo orden requerido',
                'cbenviogratis.required' => 'check envio gratis requerido',
                'cbminimo.required' => 'check minimo requerido',
                'minimocompra.required' => 'minimo compra requerido',
                'cbproducto.required' => 'check producto requerido',
                'cbautomatica.required' => 'check automatico requerido',
                'cbcerradoemergencia.required' => 'check cerrado emergencia requerido',
                'cbactivo.required' => 'activo es requerido',
                'prestado.required' => 'prestado es requerido',
                'privado.required' => 'opcion privado es requerido'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

             
            if(Servicios::where('id', '!=', $request->id)->where('identificador', $request->identificador)->first()){
                return ['success' => 5];
            }

            // validar solamente si mando la imagen
            if($request->hasFile('imagen')){                

                // validaciones para los datos
                $regla2 = array( 
                    'imagen' => 'required|image', 
                );    
         
                $mensaje2 = array(
                    'imagen.required' => 'La imagen es requerida',
                    'imagen.image' => 'El archivo debe ser una imagen',
                    );
    
                $validar2 = Validator::make($request->all(), $regla2, $mensaje2 );
    
                if ( $validar2->fails()) 
                {
                    return [
                        'success' => 1
                    ];
                }
            }

             // validar solamente si mando el logo
             if($request->hasFile('logo')){                

                // validaciones para los datos
                $regla2 = array( 
                    'logo' => 'required|image', 
                );    
         
                $mensaje2 = array(
                    'logo.required' => 'El logo es requerida',
                    'logo.image' => 'El archivo debe ser una imagen',
                    );
    
                $validar2 = Validator::make($request->all(), $regla2, $mensaje2 );
    
                if ( $validar2->fails()) 
                {
                    return [
                        'success' => 1
                    ];
                }
            }

            if($serviDatos = Servicios::where('id', $request->id)->first()){ 

                DB::beginTransaction();
           
                try {  

                    if($request->hasFile('imagen')){

                        $cadena = Str::random(15);
                        $tiempo = microtime();
                        $union = $cadena.$tiempo;
                        $nombre = str_replace(' ', '_', $union);
                        
                        $extension = '.'.$request->imagen->getClientOriginalExtension();
                        $nombreFoto = $nombre.strtolower($extension);
                        $avatar = $request->file('imagen'); 
                        $upload = Storage::disk('listaservicios')->put($nombreFoto, \File::get($avatar));
                        
                        if($upload){
                            $imagenOld = $serviDatos->imagen;
                            
                            Servicios::where('id', $request->id)->update(['imagen' => $nombreFoto]);
                                
                            if(Storage::disk('listaservicios')->exists($imagenOld)){
                                Storage::disk('listaservicios')->delete($imagenOld);                                
                            }     
                        }else{
                            return ['success' => 2]; // error subir imagen
                        }             
                    } 

                    if($request->hasFile('logo')){

                        $cadena2 = Str::random(15);
                        $tiempo2 = microtime();
                        $union2 = $cadena2.$tiempo2;
                        $nombre2 = str_replace(' ', '_', $union2);
                        
                        $extension2 = '.'.$request->logo->getClientOriginalExtension();
                        $nombreFoto2 = $nombre2.strtolower($extension2);
                        $avatar2 = $request->file('logo'); 
                        $upload2 = Storage::disk('listaservicios')->put($nombreFoto2, \File::get($avatar2));
                        
                        if($upload2){
                            $imagenOld = $serviDatos->logo;
                            
                            Servicios::where('id', $request->id)->update(['logo' => $nombreFoto2]);
                                
                            if(Storage::disk('listaservicios')->exists($imagenOld)){
                                Storage::disk('listaservicios')->delete($imagenOld);                                
                            }

                        }else{
                            return ['success' => 2]; // error subir imagen
                        }
                    }
                    

                    Servicios::where('id', $request->id)->update([
                        'nombre' => $request->nombre,
                        'comision' => $request->comision,
                        'multa' => $request->multa,
                        'identificador' => $request->identificador,
                        'descripcion' => $request->descripcion,
                        'descripcion_corta' => $request->descripcioncorta,
                        'cerrado_emergencia' => $request->cbcerradoemergencia,
                        'activo' => $request->cbactivo,
                        'tipo_servicios_id' => $request->tiposervicio,
                        'envio_gratis' => $request->cbenviogratis,
                        'telefono' => $request->telefono,
                        'tiempo' => $request->tiempo,
                        'latitud' => $request->latitud,
                        'longitud' => $request->longitud,
                        'direccion' => $request->direccion,
                        'tipo_vista' => $request->tipovista,
                        'minimo' => $request->minimocompra,
                        'utiliza_minimo' => $request->cbminimo,
                        'orden_automatica' => $request->cbautomatica,
                        'tiempo_orden_max' => $request->tiempoorden,
                        'producto_visible' => $request->cbproducto,
                        'privado' => $request->privado,
                        'prestar_motorista' => $request->prestado]);

                    DB::commit();  

                    return ['success' => 3];

                } catch(\Throwable $e){
                    DB::rollback();
                    return ['success' => 4];
                }

            }else{
                return ['success' => 6]; 
            }
        }         
    }

    // editar solamente el tiempo
    public function editarTiempo(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'lunes' => 'required',
                'martes' => 'required',
                'miercoles' => 'required',
                'jueves' => 'required',
                'viernes' => 'required',
                'sabado' => 'required',
                'domingo' => 'required'        
            );
 
            $messages = array(   
                'id.required' => 'El id es requerido',
                'lunes.required' => 'El lunes es requerido',
                'martes.required' => 'El martes es requerido',
                'miercoles.required' => 'El miercoles es requerido',
                'jueves.required' => 'El jueves es requerido',
                'viernes.required' => 'El viernes es requerido',
                'sabado.required' => 'El sabado es requerido',
                'domingo.required' => 'El domingo es requerido',                
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            DB::beginTransaction();
            
            try {       

                TiempoAproximado::where('servicios_id', $request->id)->where('dia', 1)->update(['tiempo' => $request->domingo]);
                TiempoAproximado::where('servicios_id', $request->id)->where('dia', 2)->update(['tiempo' => $request->lunes]);
                TiempoAproximado::where('servicios_id', $request->id)->where('dia', 3)->update(['tiempo' => $request->martes]);
                TiempoAproximado::where('servicios_id', $request->id)->where('dia', 4)->update(['tiempo' => $request->miercoles]);
                TiempoAproximado::where('servicios_id', $request->id)->where('dia', 5)->update(['tiempo' => $request->jueves]);
                TiempoAproximado::where('servicios_id', $request->id)->where('dia', 6)->update(['tiempo' => $request->viernes]);
                TiempoAproximado::where('servicios_id', $request->id)->where('dia', 7)->update(['tiempo' => $request->sabado]);

                DB::commit();  

                return ['success' => 1];

            } catch(\Throwable $e){
                DB::rollback();
                return ['success' => 2];
            }

        }
    }
 
    // editar solamente horas
    public function editarHoras(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'horalunes1' => 'required',
                'horalunes2' => 'required',
                'horalunes3' => 'required',
                'horalunes4' => 'required',
                'cblunessegunda' => 'required',
                'cbcerradolunes' => 'required',
                
                'horamartes1' => 'required',
                'horamartes2' => 'required',
                'horamartes3' => 'required',
                'horamartes4' => 'required',
                'cbmartessegunda' => 'required',
                'cbcerradomartes' => 'required',
                
                'horamiercoles1' => 'required',
                'horamiercoles2' => 'required',
                'horamiercoles3' => 'required',
                'horamiercoles4' => 'required',
                'cbmiercolessegunda' => 'required',
                'cbcerradomiercoles' => 'required',
                
                'horajueves1' => 'required',
                'horajueves2' => 'required',
                'horajueves3' => 'required',
                'horajueves4' => 'required',
                'cbjuevessegunda' => 'required',
                'cbcerradojueves' => 'required',
                
                'horaviernes1' => 'required',
                'horaviernes2' => 'required',
                'horaviernes3' => 'required',
                'horaviernes4' => 'required',
                'cbviernessegunda' => 'required',
                'cbcerradoviernes' => 'required',
                 
                'horasabado1' => 'required',
                'horasabado2' => 'required',
                'horasabado3' => 'required',
                'horasabado4' => 'required',
                'cbsabadosegunda' => 'required',
                'cbcerradosabado' => 'required',
                
                'horadomingo1' => 'required',
                'horadomingo2' => 'required',
                'horadomingo3' => 'required',
                'horadomingo4' => 'required',
                'cbdomingosegunda' => 'required',
                'cbcerradodomingo' => 'required',
       
            );

            $messages = array(   
                'id.required' => 'El id es requerido',
         
                'horalunes1.required' => 'es requerido 13',
                'horalunes2.required' => 'es requerido 14',
                'horalunes3.required' => 'es requerido 15',
                'horalunes4.required' => 'es requerido 16',
                'cblunessegunda.required' => 'es requerido 17',
                'cbcerradolunes.required' => 'es requerido 18',

                'horamartes1.required' => 'es requerido 19',
                'horamartes2.required' => 'es requerido 20',
                'horamartes3.required' => 'es requerido 21',
                'horamartes4.required' => 'es requerido 22',
                'cbmartessegunda.required' => 'es requerido 23',
                'cbcerradomartes.required' => 'es requerido 24',

                'horamiercoles1.required' => 'es requerido 25',
                'horamiercoles2.required' => 'es requerido 26',
                'horamiercoles3.required' => 'es requerido 27',
                'horamiercoles4.required' => 'es requerido 28',
                'cbmiercolessegunda.required' => 'es requerido 29',
                'cbcerradomiercoles.required' => 'es requerido 30',

                'horajueves1.required' => 'es requerido 31',
                'horajueves2.required' => 'es requerido 32',
                'horajueves3.required' => 'es requerido 33',
                'horajueves4.required' => 'es requerido 34',
                'cbjuevessegunda.required' => 'es requerido 35',
                'cbcerradojueves.required' => 'es requerido 36',

                'horaviernes1.required' => 'es requerido 37',
                'horaviernes2.required' => 'es requerido 38',
                'horaviernes3.required' => 'es requerido 39',
                'horaviernes4.required' => 'es requerido 40',
                'cbviernessegunda.required' => 'es requerido 41',
                'cbcerradoviernes.required' => 'es requerido 42',

                'horasabado1.required' => 'es requerido 43',
                'horasabado2.required' => 'es requerido 44',
                'horasabado3.required' => 'es requerido 45',
                'horasabado4.required' => 'es requerido 46',
                'cbsabadosegunda.required' => 'es requerido 47',
                'cbcerradosabado.required' => 'es requerido 48',

                'horadomingo1.required' => 'es requerido 49',
                'horadomingo2.required' => 'es requerido 50',
                'horadomingo3.required' => 'es requerido 51',
                'horadomingo4.required' => 'es requerido 52',
                'cbdomingosegunda.required' => 'es requerido 53',
                'cbcerradodomingo.required' => 'es requerido 54',               
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [ 
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }


          //  return $request->all();


            DB::beginTransaction();
           
            try {       

                HorarioServicio::where('servicios_id', $request->id)->where('dia', 1)->update(['hora1' => $request->horadomingo1, 'hora2' => $request->horadomingo2, 'hora3' => $request->horadomingo3, 'hora4' => $request->horadomingo4, 'segunda_hora' => $request->cbdomingosegunda, 'cerrado' => $request->cbcerradodomingo]);
                HorarioServicio::where('servicios_id', $request->id)->where('dia', 2)->update(['hora1' => $request->horalunes1, 'hora2' => $request->horalunes2, 'hora3' => $request->horalunes3, 'hora4' => $request->horalunes4, 'segunda_hora' => $request->cblunessegunda, 'cerrado' => $request->cbcerradolunes]);
                HorarioServicio::where('servicios_id', $request->id)->where('dia', 3)->update(['hora1' => $request->horamartes1, 'hora2' => $request->horamartes2, 'hora3' => $request->horamartes3, 'hora4' => $request->horamartes4, 'segunda_hora' => $request->cbmartessegunda, 'cerrado' => $request->cbcerradomartes]);
                HorarioServicio::where('servicios_id', $request->id)->where('dia', 4)->update(['hora1' => $request->horamiercoles1, 'hora2' => $request->horamiercoles2, 'hora3' => $request->horamiercoles3, 'hora4' => $request->horamiercoles4, 'segunda_hora' => $request->cbmiercolessegunda, 'cerrado' => $request->cbcerradomiercoles]);
                HorarioServicio::where('servicios_id', $request->id)->where('dia', 5)->update(['hora1' => $request->horajueves1, 'hora2' => $request->horajueves2, 'hora3' => $request->horajueves3, 'hora4' => $request->horajueves4, 'segunda_hora' => $request->cbjuevessegunda, 'cerrado' => $request->cbcerradojueves]);
                HorarioServicio::where('servicios_id', $request->id)->where('dia', 6)->update(['hora1' => $request->horaviernes1, 'hora2' => $request->horaviernes2, 'hora3' => $request->horaviernes3, 'hora4' => $request->horaviernes4, 'segunda_hora' => $request->cbviernessegunda, 'cerrado' => $request->cbcerradoviernes]);
                HorarioServicio::where('servicios_id', $request->id)->where('dia', 7)->update(['hora1' => $request->horasabado1, 'hora2' => $request->horasabado2, 'hora3' => $request->horasabado3, 'hora4' => $request->horasabado4, 'segunda_hora' => $request->cbsabadosegunda, 'cerrado' => $request->cbcerradosabado]);
                          
                DB::commit();  

                return ['success' => 1];

            } catch(\Throwable $e){
                DB::rollback();
                return ['success' => 2];
            }

        }
    }

    // ubicacion del servicio
    public function servicioUbicacion($id){
        $mapa = Servicios::where('id', $id)->select('latitud', 'longitud')->first();
        $api = "AIzaSyB-Iz6I6GtO09PaXGSQxZCjIibU_Li7yOM";
        return view('backend.paginas.cliente.mapacliente', compact('mapa', 'api'));
    }

    
}
 