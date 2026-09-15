<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReceiptController extends Controller
{
    public function download(Request $request, Order $order): Response
    {
        abort_unless($order->client->user_id === $request->user()->id, 403);

        $payment = $order->payment;

        abort_unless($payment && $payment->status === 'ACCEPTE', 404, 'Aucun paiement confirmé pour cette commande.');

        $order->load('items.product');

        $pdf = Pdf::loadView('receipts.order', [
            'order' => $order,
            'payment' => $payment,
        ])->setPaper('a4');

        return $pdf->download('recu-'.$order->order_number.'.pdf');
    }
}
