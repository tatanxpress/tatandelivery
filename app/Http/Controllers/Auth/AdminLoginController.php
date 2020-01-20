<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth; 
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller
{
  // constructor
  public function __construct()
  {
    $this->middleware('guest:admin', ['except' => ['logout']]);
  }

  // mostrar login
  public function showLoginForm(){
    return view('auth.admin-login');
  }

  // inicio de sesion
  public function login(Request $request){
    
    $rules = array(                
      'correo' => 'required',
      'password' => 'required|max:16',
    );    

    $messages = array(                                      
      'correo.required' => 'El correo es requerido.',      
      'password.required' => 'La contraseña es requerida.',
      'password.max' => '16 caracteres máximo para contraseña',
      );

      $validator = Validator::make($request->all(), $rules, $messages );

      if ( $validator->fails())
      {
          return [
              'success' => 0, 
              'message' => $validator->errors()->all()
          ];
      }
    
    if (Auth::guard('admin')->attempt(['email' => $request->correo, 'password' => $request->password])) {
     
      return [
        'success'=> 1,           
        'message'=> route('admin.dashboard')
         ];     
    }
    
    return ['success' => 2]; // datos incorrectos
  }

  // cerrar sesion administrador
  public function logout(){
      Auth::guard('admin')->logout();
      return redirect('/');
  }
}
