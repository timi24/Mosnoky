<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\AppNotification;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    private const API_URL = 'https://api-checkout.cinetpay.com/v2/payment';

    private const CHECK_URL = 'https://api-checkout.cinetpay.com/v2/payment/check';

    /**
     * Démarre un paiement CinetPay pour une commande et redirige le client
     * vers le comptoir de paiement (Orange Money, Moov Money, carte bancaire...).
     */
    public function initiate(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->client->user_id === $request->user()->id, 403);

        if ($order->status !== 'EN_ATTENTE') {
            return back()->with('status', 'Cette commande ne peut plus être payée en ligne.');
        }

        $transactionId = 'MOSNOKY-'.$order->id.'-'.time();

        $user = $request->user();

        $payload = [
            'apikey' => config('services.cinetpay.api_key'),
            'site_id' => config('services.cinetpay.site_id'),
            'transaction_id' => $transactionId,
            'amount' => (int) round($order->total / 5) * 5, // CinetPay exige un multiple de 5
            'currency' => 'XOF',
            'description' => 'Commande '.$order->order_number,
            'notify_url' => route('payments.cinetpay.notify'),
            'return_url' => route('payments.cinetpay.return', $order),
            'channels' => 'ALL',
            'customer_name' => $user->first_name ?? $user->name ?? 'Client',
            'customer_surname' => $user->last_name ?? '.',
            'customer_email' => $user->email,
            'customer_phone_number' => $user->phone ?? '00000000',
            'customer_address' => $order->shipping_address_snapshot['street'] ?? 'N/A',
            'customer_city' => $order->shipping_address_snapshot['city'] ?? 'Ouagadougou',
            'customer_country' => 'BF',
            'customer_state' => 'BF',
            'customer_zip_code' => '00000',
        ];

        $response = Http::acceptJson()->post(self::API_URL, $payload);
        $result = $response->json();

        if (($result['code'] ?? null) !== '201') {
            Log::error('CinetPay initialization failed', ['response' => $result]);

            return back()->with('status', 'Le paiement n\'a pas pu être initié. Veuillez réessayer.');
        }

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'reference' => $transactionId,
                'amount' => $order->total,
                'payment_method' => 'ORANGE_MONEY', // valeur par défaut, mise à jour après vérification
                'status' => 'EN_ATTENTE',
            ]
        );

        return redirect()->away($result['data']['payment_url']);
    }

    /**
     * Webhook appelé par CinetPay (serveur à serveur) pour signaler qu'une transaction
     * a évolué. CinetPay n'envoie jamais le statut réel ici pour des raisons de sécurité :
     * il faut systématiquement rappeler l'API de vérification.
     */
    public function notify(Request $request): Response
    {
        $transactionId = $request->input('cpm_trans_id');

        if (! $transactionId) {
            return response('Missing transaction id', 400);
        }

        $this->verifyAndUpdate($transactionId);

        return response('OK', 200);
    }

    /**
     * Page sur laquelle le client revient après avoir payé (ou annulé) sur le comptoir CinetPay.
     */
    public function returnPage(Request $request, Order $order): RedirectResponse
    {
        $payment = $order->payment;

        if ($payment && $payment->reference) {
            $this->verifyAndUpdate($payment->reference);
        }

        $order->refresh();

        $message = match ($order->payment?->status) {
            'ACCEPTE' => 'Paiement reçu avec succès ! Merci pour votre commande.',
            'REFUSE' => 'Le paiement a échoué ou a été annulé. Vous pouvez réessayer.',
            default => 'Paiement en cours de vérification. Actualisez la page dans quelques instants.',
        };

        return redirect()->route('orders.show', $order)->with('status', $message);
    }

    /**
     * Interroge l'API CinetPay pour connaître le vrai statut d'une transaction,
     * et met à jour le paiement + la commande en conséquence.
     */
    private function verifyAndUpdate(string $transactionId): void
    {
        $payment = Payment::where('reference', $transactionId)->first();

        if (! $payment || $payment->status !== 'EN_ATTENTE') {
            return; // déjà traité, ou paiement inconnu
        }

        $response = Http::acceptJson()->post(self::CHECK_URL, [
            'apikey' => config('services.cinetpay.api_key'),
            'site_id' => config('services.cinetpay.site_id'),
            'transaction_id' => $transactionId,
        ]);

        $result = $response->json();
        $status = $result['data']['status'] ?? null;

        if ($status === 'ACCEPTED') {
            $payment->update([
                'status' => 'ACCEPTE',
                'payment_method' => $this->mapPaymentMethod($result['data']['payment_method'] ?? null),
                'paid_at' => now(),
            ]);

            $payment->order->update(['status' => 'CONFIRMEE']);

            AppNotification::create([
                'user_id' => $payment->order->client->user_id,
                'title' => 'Paiement confirmé',
                'content' => "Votre paiement pour la commande {$payment->order->order_number} a été reçu avec succès.",
            ]);

            // On signale aussi à tous les administrateurs qu'un paiement sécurisé a bien été réceptionné
            $adminRows = Administrator::pluck('user_id')->map(fn ($userId) => [
                'user_id' => $userId,
                'title' => 'Paiement reçu en toute sécurité 💰',
                'content' => "Le paiement de la commande {$payment->order->order_number} ({$payment->order->client->user->first_name}) a été confirmé et l'argent réceptionné via CinetPay. Montant : ".number_format($payment->amount, 0, ',', ' ').' FCFA.',
                'is_read' => false,
                'sent_at' => now(),
            ])->all();

            if (! empty($adminRows)) {
                AppNotification::insert($adminRows);
            }
        } elseif (in_array($status, ['REFUSED', 'CANCELLED'], true)) {
            $payment->update(['status' => 'REFUSE']);
        }
        // Sinon (WAITING_FOR_CUSTOMER, PENDING...) : on ne change rien, on attend le prochain appel.
    }

    private function mapPaymentMethod(?string $cinetpayMethod): string
    {
        return match (true) {
            in_array($cinetpayMethod, ['OM'], true) => 'ORANGE_MONEY',
            in_array($cinetpayMethod, ['MOOV', 'MOOV_BF'], true) => 'MOOV_MONEY',
            in_array($cinetpayMethod, ['VISA', 'MASTERCARD', 'CREDIT_CARD'], true) => 'CARTE_BANCAIRE',
            default => 'ORANGE_MONEY',
        };
    }
}
