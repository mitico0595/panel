<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PanelAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->get('panel_logged', false)) {
            return redirect()->route('login.show');
        }

        return $next($request);
    }
}
