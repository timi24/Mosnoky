<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request): Response
    {
        Auth::logout();

        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false], 201)
            : redirect()->route('login')->with('status', __('Your account was created. Please sign in.'));
    }
}
