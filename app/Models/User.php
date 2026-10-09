<?php

namespace App\Models;

use App\Concerns\HasTeams;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string|null $verification_code
 * @property Carbon|null $verification_code_expires_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Team|null $currentTeam
 * @property string $role
 * @property-read Collection<int, Team> $ownedTeams
 * @property-read Collection<int, Membership> $teamMemberships
 * @property-read Collection<int, Team> $teams
 */
#[Fillable(['name', 'email', 'password', 'current_team_id','role'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token', 'verification_code'])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasTeams, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verification_code_expires_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /**
     * Envoie un email de vérification avec un code à 6 chiffres.
     *
     * Important : on n'utilise PAS le système Mail/SMTP de Laravel ici, car
     * Render (hébergeur gratuit) bloque les connexions SMTP sortantes. On
     * envoie donc l'email directement via l'API web de Brevo (HTTPS, jamais
     * bloqué) à la place.
     */
    public function sendEmailVerificationNotification(): void
    {
        $code = (string) random_int(100000, 999999);

        $this->forceFill([
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addMinutes(15),
        ])->save();

        $this->sendViaBrevo(
            'Votre code de vérification Mosnoky',
            '<div style="font-family: sans-serif; max-width: 480px; margin: 0 auto;">'
                .'<h2>Vérification de votre compte Mosnoky</h2>'
                .'<p>Voici votre code de vérification :</p>'
                .'<p style="font-size: 32px; font-weight: bold; letter-spacing: 8px;">'.$code.'</p>'
                .'<p>Ce code expire dans 15 minutes.</p>'
                .'</div>'
        );
    }

    /**
     * Envoie l'email "mot de passe oublié" avec le lien de réinitialisation.
     *
     * Même raison que ci-dessus : on bypass le Mailer SMTP de Laravel
     * (bloqué sur Render) et on passe directement par l'API de Brevo.
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ], false));

        $this->sendViaBrevo(
            'Réinitialisation de votre mot de passe Mosnoky',
            '<div style="font-family: sans-serif; max-width: 480px; margin: 0 auto;">'
                .'<h2>Réinitialisation de votre mot de passe</h2>'
                .'<p>Vous avez demandé à réinitialiser votre mot de passe sur Mosnoky. Cliquez sur le lien ci-dessous :</p>'
                .'<p><a href="'.$url.'" style="display: inline-block; padding: 12px 24px; background: #111; color: #fff; text-decoration: none; border-radius: 6px;">Réinitialiser mon mot de passe</a></p>'
                .'<p>Si vous n\'avez pas demandé ceci, ignorez simplement cet email.</p>'
                .'<p>Ce lien expire dans 60 minutes.</p>'
                .'</div>'
        );
    }

    /**
     * Envoie un email via l'API HTTP de Brevo (pas de SMTP, jamais bloqué).
     */
    private function sendViaBrevo(string $subject, string $htmlContent): void
    {
        try {
            $response = Http::withHeaders([
                'api-key' => env('BREVO_API_KEY'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender' => [
                    'name' => env('MAIL_FROM_NAME', 'Mosnoky'),
                    'email' => env('MAIL_FROM_ADDRESS'),
                ],
                'to' => [
                    ['email' => $this->email, 'name' => $this->name],
                ],
                'subject' => $subject,
                'htmlContent' => $htmlContent,
            ]);

            if (! $response->successful()) {
                Log::error('Brevo email failed', [
                    'subject' => $subject,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}