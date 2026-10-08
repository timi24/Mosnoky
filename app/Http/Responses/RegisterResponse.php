<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request): Response
    {
        // Important : on NE déconnecte PLUS l'utilisateur après l'inscription.
        // Fortify le connecte automatiquement à la création du compte ; on le
        // garde connecté et on l'envoie directement vers la page du code de
        // vérification, au lieu de le renvoyer se reconnecter d'abord.
        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false], 201)
            : redirect()->route('verification.notice')
                ->with('status', __('Votre compte a été créé. Entrez le code reçu par email pour l\'activer.'));
    }
}