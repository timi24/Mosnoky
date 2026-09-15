<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Administrator;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $adminIds = Administrator::pluck('user_id');

        // On récupère l'ensemble des clients ayant échangé avec la boutique,
        // avec leur dernier message et le nombre de messages non lus.
        $clientIds = Message::where(function ($q) use ($adminIds) {
            $q->whereIn('sender_id', $adminIds);
        })->orWhere(function ($q) use ($adminIds) {
            $q->whereIn('receiver_id', $adminIds);
        })
            ->get(['sender_id', 'receiver_id'])
            ->flatMap(fn ($m) => [$m->sender_id, $m->receiver_id])
            ->unique()
            ->reject(fn ($id) => $adminIds->contains($id))
            ->values();

        $conversations = User::whereIn('id', $clientIds)
            ->get()
            ->map(function ($user) use ($adminIds) {
                $lastMessage = Message::where(function ($q) use ($user, $adminIds) {
                    $q->where('sender_id', $user->id)->whereIn('receiver_id', $adminIds);
                })->orWhere(function ($q) use ($user, $adminIds) {
                    $q->whereIn('sender_id', $adminIds)->where('receiver_id', $user->id);
                })->latest('created_at')->first();

                $unreadCount = Message::where('sender_id', $user->id)
                    ->whereIn('receiver_id', $adminIds)
                    ->whereNull('read_at')
                    ->count();

                return [
                    'user' => $user,
                    'lastMessage' => $lastMessage,
                    'unreadCount' => $unreadCount,
                ];
            })
            ->sortByDesc(fn ($c) => $c['lastMessage']?->created_at)
            ->values();

        return view('admin.messages.index', [
            'conversations' => $conversations,
        ]);
    }

    public function show(Request $request, User $client): View
    {
        $adminIds = Administrator::pluck('user_id');

        $messages = Message::where(function ($q) use ($client, $adminIds) {
            $q->where('sender_id', $client->id)->whereIn('receiver_id', $adminIds);
        })->orWhere(function ($q) use ($client, $adminIds) {
            $q->whereIn('sender_id', $adminIds)->where('receiver_id', $client->id);
        })->orderBy('created_at')->get();

        Message::where('sender_id', $client->id)
            ->whereIn('receiver_id', $adminIds)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('admin.messages.show', [
            'client' => $client,
            'messages' => $messages,
        ]);
    }

    public function store(Request $request, User $client): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $client->id,
            'content' => $validated['content'],
        ]);

        return redirect()->route('admin.messages.show', $client);
    }
}
