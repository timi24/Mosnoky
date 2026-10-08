<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailWithCode extends Notification
{
    use Queueable;

    public function __construct(private readonly string $code)
    {
        //
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre code de vérification Mosnoky')
            ->greeting('Bienvenue sur Mosnoky !')
            ->line('Voici votre code de vérification :')
            ->line(new \Illuminate\Support\HtmlString(
                '<div style="text-align:center; font-size:32px; font-weight:bold; letter-spacing:8px; margin:20px 0;">'.$this->code.'</div>'
            ))
            ->line('Entrez ce code sur la page de vérification pour activer votre compte.')
            ->line('Ce code expire dans 15 minutes.')
            ->line('Si vous n\'avez pas créé de compte sur Mosnoky, vous pouvez ignorer cet email.');
    }
}
