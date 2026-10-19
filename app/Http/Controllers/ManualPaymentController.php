<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\AppNotification;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Paiement Orange Money / Moov Money par transfert manuel :
 * le client envoie l'argent sur le numéro de la boutique, puis déclare la
 * référence de sa transaction. L'équipe vérifie ensuite le SMS reçu et confirme.
 */
class ManualPaymentController extends Controller
{
    /**
     * Le bouton "Payer" existant de la page commande envoie ici (même route
     * qu'avant) : on redirige simplement vers la page de paiement.
     */
    public function start(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOwner($request, $order);

        return redirect()->route('payments.manual.show', $order);
    }

    public function show(Request $request, Order $order): View|RedirectResponse
    {
        $this->authorizeOwner($request, $order);

        if ($order->status !== 'EN_ATTENTE') {
            return redirect()->route('orders.show', $order)
                ->with('status', 'Cette commande ne peut plus être payée.');
        }

        $payment = Payment::where('order_id', $order->id)->first();

        // Pas de paiement encore, ou le précédent a été refusé : on repart à zéro
        // (le montant vient TOUJOURS de la commande en base, jamais du navigateur).
        if (! $payment || $payment->status === 'REFUSE') {
            $payment = Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'reference' => 'MOSNOKY-'.$order->id.'-'.time(),
                    'amount' => $order->total,
                    'payment_method' => 'ORANGE_MONEY',
                    'status' => 'EN_ATTENTE',
                    'paid_at' => null,
                    'customer_phone' => null,
                    'customer_reference' => null,
                    'submitted_at' => null,
                ]
            );
        }

        return view('payments.manual', [
            'order' => $order,
            'payment' => $payment,
            'accountName' => config('manual_payment.account_name'),
            'orangeNumber' => config('manual_payment.orange_number'),
            'moovNumber' => config('manual_payment.moov_number'),
        ]);
    }

    public function submit(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOwner($request, $order);

        // On nettoie la référence (espaces retirés, majuscules) avant de la contrôler
        $request->merge([
            'customer_reference' => strtoupper(preg_replace('/\s+/', '', (string) $request->input('customer_reference'))),
        ]);

        $data = $request->validate([
            'payment_method' => ['required', 'in:ORANGE_MONEY,MOOV_MONEY'],
            'customer_phone' => ['required', 'regex:/^\+?[0-9 ]{8,15}$/'],
            'customer_reference' => ['required', 'regex:/^[A-Z0-9._-]{6,40}$/', 'unique:payments,customer_reference'],
        ], [
            'payment_method.required' => 'Choisissez Orange Money ou Moov Money.',
            'payment_method.in' => 'Moyen de paiement invalide.',
            'customer_phone.required' => 'Indiquez le numéro depuis lequel vous avez payé.',
            'customer_phone.regex' => 'Numéro de téléphone invalide.',
            'customer_reference.required' => 'Indiquez la référence de la transaction reçue par SMS.',
            'customer_reference.regex' => 'Référence invalide (6 à 40 caractères : lettres, chiffres, . _ -).',
            'customer_reference.unique' => 'Cette référence a déjà été utilisée.',
        ]);

        $result = DB::transaction(function () use ($order, $data) {
            // Verrou : empêche deux envois simultanés pour la même commande
            $payment = Payment::where('order_id', $order->id)->lockForUpdate()->first();

            if (! $payment || $payment->status !== 'EN_ATTENTE' || $payment->submitted_at !== null) {
                return 'deja';
            }

            $payment->update([
                'payment_method' => $data['payment_method'],
                'customer_phone' => trim($data['customer_phone']),
                'customer_reference' => $data['customer_reference'],
                'submitted_at' => now(),
            ]);

            // On prévient tous les administrateurs qu'un paiement attend vérification
            $adminRows = Administrator::pluck('user_id')->map(fn ($userId) => [
                'user_id' => $userId,
                'title' => 'Paiement à vérifier 💰',
                'content' => "Commande {$order->order_number} : le client déclare avoir payé ".number_format($payment->amount, 0, ',', ' ').' FCFA ('
                    .str_replace('_', ' ', $data['payment_method']).", référence {$data['customer_reference']}). Vérifiez le SMS reçu puis confirmez dans Paiements.",
                'is_read' => false,
                'sent_at' => now(),
            ])->all();

            if (! empty($adminRows)) {
                AppNotification::insert($adminRows);
            }

            return 'ok';
        });

        if ($result === 'deja') {
            return redirect()->route('orders.show', $order)
                ->with('status', 'Votre paiement est déjà en cours de vérification.');
        }

        return redirect()->route('orders.show', $order)
            ->with('status', 'Merci ! Votre paiement est en cours de vérification. Vous serez prévenu dès sa confirmation.');
    }

    private function authorizeOwner(Request $request, Order $order): void
    {
        abort_unless($order->client->user_id === $request->user()->id, 403);
    }
}
