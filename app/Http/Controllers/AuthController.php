<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credenciales = $request->only('mail', 'password');

        if (Auth::attempt($credenciales)) {
            return redirect('/home');
        }

        return back()->with('error', 'Mail o contraseña incorrectos.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}