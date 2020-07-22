<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Publicidad;
use App\Servicios;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicidadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
 
    // lista de promociones o publicidad
    public function index(){

        $zonas = Servicios::all();
        return view('backend.paginas.publicidad.listapublicidad', compact('zonas'));
    } 

    // tabla 
    public function publicidadtabla(){ 
         
        $publicidad = DB::table('publicidad')
        ->select('id', 'nombre', 'descripcion', 'identificador', 'tipo_publicidad', 'fecha_inicio', 'fecha_fin')
        ->where('activo', 1)
        ->orderBy('id', 'DESC')
        ->get();

        return view('backend.paginas.publicidad.tablas.tablapublicidad', compact('publicidad'));
    }

    // ver promociones o publicidad inactiva
    public function indexinactivo(){

        $zonas = Servicios::all();
        return view('backend.paginas.publicidad.listapublicidadinactivo', compact('zonas'));
    } 

    // tabla 
    public function publicidadtablainactivo(){ 
        
        $publicidad = DB::table('publicidad')         
        ->select('id', 'nombre', 'descripcion', 'tipo_publicidad', 'fecha_inicio', 'fecha_fin')
        ->where('activo', 0)
        ->orderBy('id', 'DESC')
        ->get();

        return view('backend.paginas.publicidad.tablas.tablapublicidadinactivo', compact('publicidad'));
    }

    // nueva promocion
    public function nuevoPromocion(Request $request){

        if($request->isMethod('post')){

            $regla = array( 
                'nombre' => 'required',
                'identificador' => 'required',
                'descripcion' => 'required',
                'fechainicio' => 'required',
                'fechafin' => 'required',
            );

            $mensaje = array(
                'nombre.required' => 'Nombre es requerido',
                'identificador.required' => 'Identificador es requerido',
                'descripcion.required' => 'Descripcion es requerida',
                'fechainicio.required' => 'fecha inicio es requerido',
                'fechafin.required' => 'fecha fin es requerida'
                );


            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            } 

            if(Publicidad::where('identificador', $request->identificador)->first()){
                return ['success' => 4];
            }

            $cadena = Str::random(15);
            $tiempo = microtime(); 
            $union = $cadena.$tiempo;
            $nombre = str_replace(' ', '_', $union);
             
            $extension = '.'.$request->imagen->getClientOriginalExtension();
            $nombreFoto = $nombre.strtolower($extension);
            $avatar = $request->file('imagen'); 
            $upload = Storage::disk('listaservicios')->put($nombreFoto, \File::get($avatar)); 
            
            $cadena2 = Str::random(15);
            $tiempo2 = microtime(); 
            $union2 = $cadena2.$tiempo2;
            $nombre2 = str_replace(' ', '_', $union2);
             
            $extension2 = '.'.$request->logo->getClientOriginalExtension();
            $nombreFoto2 = $nombre2.strtolower($extension2);
            $avatar2 = $request->file('logo'); 
            $upload2 = Storage::disk('listaservicios')->put($nombreFoto2, \File::get($avatar2)); 
     
            if($upload && $upload2){

                $identi = str_replace(' ', '_', $request->identificador);
 
                $p = new Publicidad();
                $p->nombre = $request->nombre;
                $p->identificador = $identi;
                $p->descripcion = $request->descripcion;
                $p->imagen = $nombreFoto;
                $p->logo = $nombreFoto2;
                $p->tipo_publicidad = 1;
                $p->url_facebook = "";
                $p->utiliza_facebook = 0;
                $p->url_youtube = "";
                $p->utiliza_youtube = 0;
                $p->url_instagram = "";
                $p->utiliza_instagram = 0;
                $p->titulo = "";
                $p->utiliza_titulo = 0;
                $p->titulo_descripcion = "";
                $p->utiliza_descripcion = 0;
                $p->telefono = "";
                $p->utiliza_telefono = 0;
                $p->activo = 1;
                $p->utiliza_visitanos = 0;
                $p->visitanos = "";
                $p->fecha_inicio = $request->fechainicio;
                $p->fecha_fin = $request->fechafin;

                if($p->save()){
                    return ['success' => 1];
                }else{
                    return ['success' => 2];
                } 
            }else{
                return ['success' => 3];
            }
        }
    }

    // nueva publicidad
    public function nuevoPublicidad(Request $request){

        if($request->isMethod('post')){  

            $regla = array( 
                'nombre' => 'required'
            );

            $mensaje = array(
                'nombre.required' => 'Nombre es requerido'                
                );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            } 

            if(Publicidad::where('identificador', $request->identificador)->first()){
                return ['success' => 4];
            }

            $cadena = Str::random(15);
            $tiempo = microtime(); 
            $union = $cadena.$tiempo;
            $nombre = str_replace(' ', '_', $union);
             
            $extension = '.'.$request->imagen->getClientOriginalExtension();
            $nombreFoto = $nombre.strtolower($extension);
            $avatar = $request->file('imagen'); 
            $upload = Storage::disk('listaservicios')->put($nombreFoto, \File::get($avatar)); 
            
            $cadena2 = Str::random(15);
            $tiempo2 = microtime(); 
            $union2 = $cadena2.$tiempo2;
            $nombre2 = str_replace(' ', '_', $union2);
             
            $extension2 = '.'.$request->logo->getClientOriginalExtension();
            $nombreFoto2 = $nombre2.strtolower($extension2);
            $avatar2 = $request->file('logo'); 
            $upload2 = Storage::disk('listaservicios')->put($nombreFoto2, \File::get($avatar2)); 
     
            if($upload && $upload2){

                $identi = str_replace(' ', '_', $request->identificador);

                $facebook = "";
                if($request->urlfacebook != null){                    
                    $facebook = $request->urlfacebook;
                }

                $youtube = "";
                if($request->urlyoutube != null){
                    $youtube = $request->urlyoutube;
                }

                $instagram = "";
                if($request->urlinstagram != null){
                    $instagram = $request->urlinstagram;
                }
 
                $titulo = "";
                if($request->titulo != null){
                    $titulo = $request->titulo;
                }

                $titulodescripcion = "";
                if($request->titulodescripcion != null){
                    $titulodescripcion = $request->titulodescripcion;
                }

                $telefono = "";
                if($request->telefono != null){
                    $telefono = $request->telefono;
                }

                $visitanos = "";
                if($request->visitanos != null){
                    $visitanos = $request->visitanos;
                }

                $p = new Publicidad();
                $p->nombre = $request->nombre;
                $p->descripcion = $request->descripcion;
                $p->imagen = $nombreFoto;
                $p->logo = $nombreFoto2;
                $p->identificador = $identi;
                $p->tipo_publicidad = 2;
                $p->url_facebook = $facebook;
                $p->utiliza_facebook = $request->cbfacebook;
                $p->url_youtube = $youtube;
                $p->utiliza_youtube = $request->cbyoutube;
                $p->url_instagram = $instagram;
                $p->utiliza_instagram = $request->cbinstagram;
                $p->titulo = $titulo;
                $p->utiliza_titulo = $request->cbtitulo;
                $p->titulo_descripcion = $titulodescripcion;
                $p->utiliza_descripcion = $request->cbdescripcion;
                $p->telefono = $telefono;
                $p->utiliza_telefono = $request->cbtelefono;
                $p->activo = 1;
                $p->utiliza_visitanos = $request->cbvisitanos;
                $p->visitanos = $visitanos;
                $p->fecha_inicio = $request->fechainicio;
                $p->fecha_fin = $request->fechafin; 
 
                if($p->save()){
                    return ['success' => 1];
                }else{
                    return ['success' => 2];
                } 
            }else{
                return ['success' => 3];
            }
        }
    }

    // informacion de publicidad
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

            
          if($p = Publicidad::where('id', $request->id)->first()){

            return ['success' => 1, 'publicidad' => $p]; 
          }else{
              return ['success' => 2];
          }
        }
    }

    // editar producto 
    public function editarPromo(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',
                'nombre' => 'required',
                'descripcion' => 'required',
                'fechainicio' => 'required',
                'fechafin' => 'required',
                'activo' => 'required'                
            );

            $messages = array(   
                'id.required' => 'El id es requerido',
                'nombre.required' => 'El nombre es requerido',
                'descripcion.required' => 'la descripcion es requerido',
                'fechainicio.required' => 'fecha inicio es requerida',
                'fechafin.required' => 'fecha fin es requerida',
                'activo.required' => 'activo es requerida'
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
            }

            if($po = Publicidad::where('id', $request->id)->first()){

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
                            $imagenOld = $po->imagen;
                            
                            Publicidad::where('id', $request->id)->update(['imagen' => $nombreFoto]);
                                
                            if(Storage::disk('listaservicios')->exists($imagenOld)){
                                Storage::disk('listaservicios')->delete($imagenOld);                                
                            }     
                        }else{
                            return ['success' => 1]; // error subir imagen
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
                            $imagenOld = $po->logo;
                            
                            Publicidad::where('id', $request->id)->update(['logo' => $nombreFoto2]);
                                
                            if(Storage::disk('listaservicios')->exists($imagenOld)){
                                Storage::disk('listaservicios')->delete($imagenOld);                                
                            }

                        }else{
                            return ['success' => 1]; // error subir imagen
                        }
                    }

                    Publicidad::where('id', $request->id)->update([
                        'nombre' => $request->nombre,
                        'descripcion' => $request->descripcion,
                        'fecha_inicio' => $request->fechainicio,
                        'fecha_fin' => $request->fechafin,
                        'activo' => $request->activo,
                        'identificador' => $request->identificador
                        ]);

                    DB::commit();  

                    return ['success' => 2];

                } catch(\Throwable $e){
                    DB::rollback();
                    return ['success' => "error" . $e];
                }
            }else{
                return ['success' => 4]; // promocion no encontrado 
            }
        }  
    }

    // editar publicidad
    public function editarPubli(Request $request){
        if($request->isMethod('post')){  

            $regla = array( 
                'id' => 'required',
                'nombre' => 'required',
                'descripcion' => 'required',
                'fechainicio' => 'required',
                'fechafin' => 'required',
                'cbfacebook' => 'required',
                'cbyoutube' => 'required',
                'cbinstagram' => 'required',
                'cbdescripcion' => 'required',
                'cbtelefono' => 'required',
                'cbvisitanos' => 'required',
                'cbtitulo' => 'required',
                'urlfacebook' => 'required',
                'urlyoutube' => 'required',
                'urlinstagram' => 'required',
                'titulo' => 'required',
                'activo' => 'required',
                'titulodescripcion' => 'required',
                'telefono' => 'required',
                'visitanos' => 'required',
            );

            $mensaje = array(
                'id.required' => 'id es requerido',
                'nombre.required' => 'Nombre es requerido',
                'descripcion.required' => 'Descripcion es requerida',
                'fechainicio.required' => 'fecha inicio es requerido',
                'fechafin.required' => 'fecha fin es requerida',
                'cbfacebook.required' => 'check facebook requerido',
                'cbyoutube.required' => 'check youtube requerido',
                'cbinstagram.required' => 'check instagram requerido',
                'cbdescripcion.required' => 'check descripcion requerido',
                'cbtelefono.required' => 'check telefono requerido',
                'cbvisitanos.required' => 'check visitanos requerido',
                'cbtitulo.required' => 'check titulo requerido',
                'urlfacebook.required' => 'url facebook requerido',
                'urlyoutube.required' => 'url youtube requerido',
                'urlinstagram.required' => 'url instagram requerido',
                'titulo.required' => 'titulo requerido',
                'titulodescripcion.required' => 'titulo descripcion requerido',
                'telefono.required' => 'telefono requerido',
                'visitanos.required' => 'visitanos requerido',
                'activo.required' => 'activo es requerido',
                );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            } 

            if($po = Publicidad::where('id', $request->id)->first()){

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
                            $imagenOld = $po->imagen;
                            
                            Publicidad::where('id', $request->id)->update(['imagen' => $nombreFoto]);
                                
                            if(Storage::disk('listaservicios')->exists($imagenOld)){
                                Storage::disk('listaservicios')->delete($imagenOld);                                
                            }     
                        }else{
                            return ['success' => 1]; // error subir imagen
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
                            $imagenOld = $po->logo;
                            
                            Publicidad::where('id', $request->id)->update(['logo' => $nombreFoto2]);
                                
                            if(Storage::disk('listaservicios')->exists($imagenOld)){
                                Storage::disk('listaservicios')->delete($imagenOld);                                
                            }
        
                        }else{
                            return ['success' => 1]; // error subir imagen
                        }
                    }

                        Publicidad::where('id', $request->id)->update([
                            'nombre' => $request->nombre,
                            'descripcion' => $request->descripcion,
                            'url_facebook' => $request->urlfacebook,
                            'utiliza_facebook' => $request->cbfacebook,
                            'url_youtube' => $request->urlyoutube,
                            'utiliza_youtube' => $request->cbyoutube,
                            'url_instagram' => $request->urlinstagram,
                            'utiliza_instagram' => $request->cbinstagram,
                            'titulo' => $request->titulo,
                            'utiliza_titulo' => $request->cbtitulo,
                            'titulo_descripcion' => $request->titulodescripcion,
                            'utiliza_descripcion' => $request->cbdescripcion,
                            'telefono' => $request->telefono,
                            'utiliza_telefono' => $request->cbtelefono,
                            'activo' => $request->activo,
                            'utiliza_visitanos' => $request->cbvisitanos,
                            'visitanos' => $request->visitanos,
                            'fecha_inicio' => $request->fechainicio,
                            'fecha_fin' => $request->fechafin,
                            'identificador' => $request->identificador
                            ]);

                        DB::commit();  

                        return ['success' => 2];
    
                } catch(\Throwable $e){
                    DB::rollback();
                    return ['success' => "error" . $e];
                }
            } 
        }
    } 

}
