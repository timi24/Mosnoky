<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $client = Client::firstOrCreate(['user_id' => $request->user()->id]);

        $reports = $client->reports()
            ->with('order')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('reports.index', [
            'reports' => $reports,
        ]);
    }

    public function create(Request $request): View
    {
        $client = Client::firstOrCreate(['user_id' => $request->user()->id]);

        $orders = $client->orders()->orderByDesc('created_at')->get();

        return view('reports.create', [
            'orders' => $orders,
            'selectedOrderId' => $request->integer('order_id') ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $client = Client::firstOrCreate(['user_id' => $request->user()->id]);

        $validated = $request->validate([
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'report_type' => ['required', 'in:PROBLEME_LIVRAISON,PRODUIT_DEFECTUEUX,COMMENTAIRE_INAPPROPRIE,AUTRE'],
            'description' => ['required', 'string', 'max:1000'],
        ]);

        // On vérifie que la commande, si fournie, appartient bien au client
        if (! empty($validated['order_id'])) {
            $ownsOrder = $client->orders()->where('id', $validated['order_id'])->exists();
            abort_unless($ownsOrder, 403);
        }

        Report::create([
            'client_id' => $client->id,
            'order_id' => $validated['order_id'] ?? null,
            'report_type' => $validated['report_type'],
            'description' => $validated['description'],
            'status' => 'OUVERT',
        ]);

        return redirect()
            ->route('reports.index')
            ->with('status', 'Votre signalement a été envoyé. Notre équipe va l\'examiner.');
    }
}
