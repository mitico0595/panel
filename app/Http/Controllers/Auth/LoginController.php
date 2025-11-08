<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    // IMPORTANTE: ahora recibimos Request aquí
    public function show(Request $request)
    {
        // Si ya está logueado, mandamos directo al panel
        if ($request->session()->get('panel_logged')) {
            return redirect()->route('panel.index');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Por si alguien intenta postear al login ya estando logueado
        if ($request->session()->get('panel_logged')) {
            return redirect()->route('panel.index');
        }

        if ($request->username === 'admin' && $request->password === 'tiemporeal') {
            $request->session()->put('panel_logged', true);
            $request->session()->regenerate();

            return redirect()->route('panel.index');
        }

        return back()
            ->withErrors(['username' => 'Credenciales inválidas'])
            ->withInput();
    }

    public function logout(Request $request)
    {
        $request->session()->forget('panel_logged');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.show');
    }
}
