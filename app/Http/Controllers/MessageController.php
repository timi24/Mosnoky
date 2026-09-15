<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $adminIds = Administrator::pluck('user_id');

        $messages = Message::where(function ($q) use ($user, $adminIds) {
            $q->where('sender_id', $user->id)->whereIn('receiver_id', $adminIds);
        })->orWhere(function ($q) use ($user, $adminIds) {
            $q->whereIn('sender_id', $adminIds)->where('receiver_id', $user->id);
        })->orderBy('created_at')->get();

        // On marque comme lus les messages reçus de la boutique
        Message::whereIn('sender_id', $adminIds)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.index', [
            'messages' => $messages,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $user = $request->user();

        // On adresse le message au dernier admin ayant répondu, sinon au premier admin disponible
        $adminIds = Administrator::pluck('user_id');

        $lastAdminSender = Message::where('receiver_id', $user->id)
            ->whereIn('sender_id', $adminIds)
            ->latest('created_at')
            ->value('sender_id');

        $receiverId = $lastAdminSender ?? $adminIds->first();

        abort_if(is_null($receiverId), 500, 'Aucun administrateur disponible pour recevoir le message.');

        Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'content' => $validated['content'],
        ]);

        return redirect()->route('messages.index');
    }
}
