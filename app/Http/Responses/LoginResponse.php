<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        $user = $request->user();

        $redirectUrl = $user->role === 'ADMINISTRATEUR'
            ? route('admin.dashboard')
            : route('client.dashboard');

        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false], 200)
            : redirect()->to($redirectUrl);
    }
}