<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Message;
use App\Models\Payment;
use App\Models\User;
use App\Support\NotificationLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Données "en direct" : compteurs des menus et liste des notifications,
 * relues automatiquement par le navigateur (sans recharger la page).
 */
class LiveController extends Controller
{
    public function counts(Request $request): JsonResponse
    {
        $user = $request->user();
        $adminIds = User::query()->whereRaw('UPPER(TRIM(role)) = ?', ['ADMINISTRATEUR'])->pluck('id');
        $isAdmin = strtoupper(trim($user->role ?? '')) === 'ADMINISTRATEUR';

        $data = [
            'is_admin' => $isAdmin,
            'notifications' => AppNotification::where('user_id', $user->id)->where('is_read', false)->count(),
        ];

        if ($isAdmin) {
            $data['messages'] = Message::whereNotIn('sender_id', $adminIds)->whereNull('read_at')->count();
            $data['payments'] = Payment::where('status', 'EN_ATTENTE')->whereNotNull('submitted_at')->count();
        } else {
            $data['messages'] = Message::whereIn('sender_id', $adminIds)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->count();
        }

        // La liste n'est envoyée que si la page "Notifications" est ouverte (?items=1)
        if ($request->boolean('items')) {
            $data['items'] = AppNotification::where('user_id', $user->id)
                ->orderByDesc('id')
                ->limit(15)
                ->get()
                ->map(function ($n) use ($isAdmin) {
                    $when = $n->sent_at ?? $n->created_at ?? null;
                    try {
                        $whenText = $when ? Carbon::parse($when)->locale('fr')->diffForHumans() : '';
                    } catch (\Throwable $e) {
                        $whenText = '';
                    }

                    return [
                        'id' => $n->id,
                        'title' => (string) $n->title,
                        'content' => (string) $n->content,
                        'is_read' => (bool) $n->is_read,
                        'open' => NotificationLink::url($n, $isAdmin) ? route('live.notifications.open', $n->id) : null,
                        'when' => $whenText,
                    ];
                })->values();
        }

        return response()->json($data)->header('Cache-Control', 'no-store');
    }

    public function readAllNotifications(Request $request): JsonResponse
    {
        AppNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }

    /**
     * Clic sur une notification : on la marque comme lue puis on ouvre la page concernée
     * (produit, commande, paiement, message…).
     */
    public function openNotification(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        $notification = AppNotification::where('user_id', $user->id)->where('id', $id)->firstOrFail();

        if (! $notification->is_read) {
            $notification->forceFill(['is_read' => true])->save();
        }

        $isAdmin = strtoupper(trim($user->role ?? '')) === 'ADMINISTRATEUR';
        $url = NotificationLink::url($notification, $isAdmin);

        return $url ? redirect($url) : redirect()->back();
    }
}
