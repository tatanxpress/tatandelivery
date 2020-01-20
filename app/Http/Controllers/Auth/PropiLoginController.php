<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class PropiLoginController extends Controller
{
    public function __construct()
  {
    $this->middleware('guest:propi', ['except' => ['logout']]);
  }
  public function showLoginForm()
  {
    return view('auth.propi-login');
  }
  public function login(Request $request)
  {
    // Validate the form data
    $this->validate($request, [
      'email'   => 'required|email',
      'password' => 'required|min:6'
    ]);
    // Attempt to log the user in
    if (Auth::guard('propi')->attempt(['correo' => $request->email, 'password' => $request->password], $request->remember)) {
      // if successful, then redirect to their intended location
      return redirect()->intended(route('propi.dashboard'));
    }
    // if unsuccessful, then redirect back to the login with the form data
    return redirect()->back()->withInput($request->only('email', 'remember'));
  }
  public function logout()
  {
      Auth::guard('propi')->logout();
      return redirect('/');
  }
}
