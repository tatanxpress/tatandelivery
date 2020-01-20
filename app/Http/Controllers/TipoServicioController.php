<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TipoServicios;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TipoServicioController extends Controller
{
     // controlador protegido
    public function __construct()
    {
         $this->middleware('auth:admin'); 
    }

    public function index(){
        return view('backend.paginas.tiposervicios.listatiposervicios');
    }

    // tabla para ver zonas
    public function serviciotabla(){
        $tipo = TipoServicios::all();

        return view('backend.paginas.tiposervicios.tablas.tablatiposervicio', compact('tipo'));
    }

    // nuevo tipo servicio
    public function nuevoTipoServicio(Request $request){
        if($request->isMethod('post')){  

            $regla = array( 
                'nombre' => 'required',
                'descripcion' => 'required',
            );

            $mensaje = array(
                'nombre.required' => 'Nombre es requerido',
                'descripcion.required' => 'Descripcion es requerida'
                );

            $validar = Validator::make($request->all(), $regla, $mensaje );

            if ($validar->fails()) 
            {
                return [
                    'success' => 0, 
                    'message' => $validar->errors()->all()
                ];
            } 

            $cadena = Str::random(15);
            $tiempo = microtime(); 
            $union = $cadena.$tiempo;
            $nombre = str_replace(' ', '_', $union);
             
            $extension = '.'.$request->imagen->getClientOriginalExtension();
            $nombreFoto = $nombre.strtolower($extension);
            $avatar = $request->file('imagen'); 
            $upload = Storage::disk('listaservicios')->put($nombreFoto, \File::get($avatar)); 
     
            if($upload){

                $tipo = new TipoServicios();
                $tipo->nombre = $request->nombre;
                $tipo->descripcion = $request->descripcion;
                $tipo->imagen = $nombreFoto;

                if($tipo->save()){
                    return ['success' => 1];
                }else{
                    return ['success' => 2];
                } 
            }else{
                return ['success' => 3];
            }
        }
    }

    // informacion tipo servicio
    public function informacionTipo(Request $request){
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

            if($tipo = TipoServicios::where('id', $request->id)->first()){
                return['success' => 1, 'tipo' => $tipo];
            }else{
                return['success' => 2];
            }
        }
    }

    // editar tipo servicio
    public function editarTipo(Request $request){
        if($request->isMethod('post')){   
            $rules = array( 
                'id' => 'required',               
                'nombre' => 'required',
                'descripcion' => 'required'                
            );    

            $messages = array(   
                'id.required' => 'El id es requerido.',                                   
                'nombre.required' => 'El nombre es requerido.',
                'descripcion.required' => 'la descripcion es requerido.'               
                );

            $validator = Validator::make($request->all(), $rules, $messages );

            if ( $validator->fails() ) 
            {
                return [
                    'success' => 0,
                    'message' => $validator->errors()->all()
                ];
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
                        'success' => 0, 
                        'message' => $validar2->errors()->all()
                    ];
                }
            }
                        
            if($tipo = TipoServicios::where('id', $request->id)->first()){                        

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
                        $imagenOld = $tipo->imagen; //nombre de imagen a borrar
                        
                        TipoServicios::where('id', $request->id)->update(['nombre' => $request->nombre, 
                        'descripcion' => $request->descripcion, 'imagen' =>$nombreFoto]);
                            
                        if(Storage::disk('listaservicios')->exists($imagenOld)){
                            Storage::disk('listaservicios')->delete($imagenOld);                                
                        }     
                        return ['success' => 1];
                    }else{
                        return ['success' => 2];
                    }
                }else{                
                    TipoServicios::where('id', $request->id)->update(['nombre' => $request->nombre, 
                        'descripcion' => $request->descripcion]);
                    
                    return ['success' => 1];                    
                }
            }else{
                return ['success' => 3]; // tipo servicio no encontrado
            }
        }
    }
}
 