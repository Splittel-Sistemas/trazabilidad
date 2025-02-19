<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class LoginController extends Controller
{
    public function login_view()
    {
        return view('layouts.login');  
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $remember = $request->has('remember');
        $user = User::where('email', $request->email)->first();
        if (!$user || $user->active == 0) {
            return redirect('login')
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Cuenta inactiva o no encontrada.']);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('Home'));
        } else {

            return redirect('login')
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Correo electrónico o contraseña incorrectos.']);
        }
    }
    public function register(Request $request)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        Auth::login($user);
        return redirect(route('Home'));
    }
    public function logout(Request $request)
    {
        Auth::logout(); 
        $request->session()->invalidate(); 
        $request->session()->regenerateToken(); 
        return redirect()->route('login_view');
    }
    public function operador(Request $request)
    {
        $request->validate([
            'clave' => 'required',
        ]);

        $operador = User::where('password', $request->clave)->first(); 

        if ($operador) {
            if ($operador->active == 1) {
                Auth::login($operador);
                $request->session()->regenerate();
                return redirect()->intended(route('Home'));
            } else {
                return redirect()->route('login_view')
                    ->withErrors(['clave' => 'El acceso ha sido restringido. Contacte al administrador.']);
            }
        }

        return redirect()->route('login_view')
            ->withErrors(['clave' => 'Clave incorrecta.']);
    }

}
