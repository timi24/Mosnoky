<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $client = Client::firstOrCreate(['user_id' => $request->user()->id]);

        $payments = Payment::whereHas('order', function ($query) use ($client) {
            $query->where('client_id', $client->id);
        })
            ->with('order')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('payments.index', [
            'payments' => $payments,
        ]);
    }
}
