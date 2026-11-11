
<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AppNotification;
use App\Models\Message;
use App\Models\Payment;
use App\Support\NotificationLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LiveController extends Controller
{
    /**
     * Vérifie si l'utilisateur connecté est administrateur.
     */
    private function isAdmin($user): bool
    {
        return strtoupper(trim($user->role ?? '')) === 'ADMINISTRATEUR';
    }

    /**
     * Données en direct : compteurs et notifications.
     */
    public function counts(Request $request): JsonResponse
    {
        $user = $request->user();

        // Vérification du rôle depuis la table users
        $isAdmin = $this->isAdmin($user);

        // Liste des identifiants de tous les administrateurs
        $adminIds = User::whereRaw(
            "UPPER(TRIM(role)) = ?",
            ['ADMINISTRATEUR']
        )->pluck('id');

        $data = [
            'is_admin' => $isAdmin,
            'notifications' => AppNotification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count(),
        ];

        if ($isAdmin) {

            // Messages reçus par les administrateurs depuis les clients
            $data['messages'] = Message::whereNotIn('sender_id', $adminIds)
                ->whereNull('read_at')
                ->count();

            // Paiements en attente
            $data['payments'] = Payment::where('status', 'EN_ATTENTE')
                ->whereNotNull('submitted_at')
                ->count();

        } else {

            // Messages envoyés par les administrateurs au client
            $data['messages'] = Message::whereIn('sender_id', $adminIds)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->count();
        }

        // Liste des notifications si la page Notifications est ouverte
        if ($request->boolean('items')) {

            $data['items'] = AppNotification::where('user_id', $user->id)
                ->orderByDesc('id')
                ->limit(15)
                ->get()
                ->map(function ($n) use ($isAdmin) {

                    $when = $n->sent_at ?? $n->created_at ?? null;

                    try {
                        $whenText = $when
                            ? Carbon::parse($when)->diffForHumans()
                            : '';
                    } catch (\Throwable $e) {
                        $whenText = '';
                    }

                    return [
                        'id' => $n->id,
                        'title' => (string) $n->title,
                        'content' => (string) $n->content,
                        'is_read' => (bool) $n->is_read,
                        'open' => NotificationLink::url($n, $isAdmin)
                            ? route('live.notifications.open', $n->id)
                            : null,
                        'when' => $whenText,
                    ];
                })->values();
        }

        return response()->json($data)
            ->header('Cache-Control', 'no-store');
    }

    /**
     * Marquer toutes les notifications comme lues.
     */
    public function readAllNotifications(Request $request): JsonResponse
    {
        AppNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }

    /**
     * Ouvrir une notification.
     */
    public function openNotification(
        Request $request,
        int $id
    ): RedirectResponse {

        $user = $request->user();

        $notification = AppNotification::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        if (! $notification->is_read) {
            $notification->forceFill([
                'is_read' => true
            ])->save();
        }

        // Même vérification que dans AccountController
        $isAdmin = $this->isAdmin($user);

        $url = NotificationLink::url($notification, $isAdmin);

        return $url
            ? redirect($url)
            : redirect()->back();
    }
}
