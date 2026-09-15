<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Passkeys\Contracts\PasskeyLoginResponse as PasskeyLoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class PasskeyLoginResponse implements PasskeyLoginResponseContract
{
    public function toResponse($request): Response
    {
        $user = $request->user();

        $redirectUrl = $user->role === 'ADMINISTRATEUR'
            ? route('admin.dashboard')
            : route('client.dashboard');

        return $request->wantsJson()
            ? new JsonResponse(['redirect' => $redirectUrl], 200)
            : redirect()->intended($redirectUrl);
    }
}