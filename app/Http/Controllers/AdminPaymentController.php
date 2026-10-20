<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Espace admin : vérification des paiements Orange Money / Moov Money déclarés.
 */
class AdminPaymentController extends Controller
{
    public function index(): View
    {
        $pending = Payment::with('order.client.user')
            ->where('status', 'EN_ATTENTE')
            ->whereNotNull('submitted_at')
            ->orderBy('submitted_at')
            ->get();

        $recent = Payment::with('order.client.user')
            ->whereIn('status', ['ACCEPTE', 'REFUSE'])
            ->whereNotNull('submitted_at')
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get();

        return view('admin.payments.index', [
            'pending' => $pending,
            'recent' => $recent,
        ]);
    }

    /**
     * Interrogé toutes les quelques secondes par la page admin pour savoir
     * s'il y a un nouveau paiement à vérifier (déclenche l'alarme sonore).
     */
    public function alert(): JsonResponse
    {
        $pending = Payment::where('status', 'EN_ATTENTE')->whereNotNull('submitted_at');

        return response()
            ->json([
                'pending' => (clone $pending)->count(),
                'signature' => (clone $pending)->max('submitted_at'),
            ])
            ->header('Cache-Control', 'no-store');
    }

    public function confirm(Payment $payment): RedirectResponse
    {
        $done = DB::transaction(function () use ($payment) {
            $locked = Payment::where('id', $payment->id)->lockForUpdate()->first();

            // Déjà traité par quelqu'un d'autre (ou double clic) : on ne refait rien
            if (! $locked || $locked->status !== 'EN_ATTENTE' || $locked->submitted_at === null) {
                return false;
            }

            $locked->update(['status' => 'ACCEPTE', 'paid_at' => now()]);
            $locked->order->update(['status' => 'CONFIRMEE']);

            AppNotification::create([
                'user_id' => $locked->order->client->user_id,
                'title' => 'Paiement confirmé',
                'content' => "Votre paiement pour la commande {$locked->order->order_number} a été vérifié et confirmé. Merci !",
            ]);

            return true;
        });

        return back()->with('status', $done
            ? 'Paiement confirmé : la commande est validée et le client a été prévenu.'
            : 'Ce paiement a déjà été traité.');
    }

    public function reject(Payment $payment): RedirectResponse
    {
        $done = DB::transaction(function () use ($payment) {
            $locked = Payment::where('id', $payment->id)->lockForUpdate()->first();

            if (! $locked || $locked->status !== 'EN_ATTENTE' || $locked->submitted_at === null) {
                return false;
            }

            $locked->update(['status' => 'REFUSE']);

            AppNotification::create([
                'user_id' => $locked->order->client->user_id,
                'title' => 'Paiement non retrouvé',
                'content' => "Nous n'avons pas retrouvé votre paiement pour la commande {$locked->order->order_number}. Vérifiez la référence et le montant, puis refaites la déclaration depuis votre commande.",
            ]);

            return true;
        });

        return back()->with('status', $done
            ? 'Paiement rejeté : le client a été prévenu et peut refaire sa déclaration.'
            : 'Ce paiement a déjà été traité.');
    }
}
