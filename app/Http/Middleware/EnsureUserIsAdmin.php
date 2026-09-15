<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== 'ADMINISTRATEUR') {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        return $next($request);
    }
}