<?php

use App\Http\Middleware\SetTeamUrlDefaults;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
    // Dit à Laravel de faire confiance au proxy de Render (toutes les IP, "*"),
    // pour qu'il détecte correctement que la connexion d'origine est en HTTPS.
    $middleware->trustProxies(at: '*');

    $middleware->web(append: [
        SetTeamUrlDefaults::class,
    ]);
    $middleware->validateCsrfTokens(except: [
        'paiement/cinetpay/notify',   // ← c'est exactement ça
    ]);
})

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();