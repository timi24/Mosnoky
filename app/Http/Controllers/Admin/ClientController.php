<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $clients = Client::with('user')
            ->withCount('orders')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.clients.index', [
            'clients' => $clients,
            'search' => $request->string('search')->toString(),
        ]);
    }

    public function show(Client $client): View
    {
        $client->load('user');
        $orders = $client->orders()->orderByDesc('created_at')->paginate(10);

        return view('admin.clients.show', [
            'client' => $client,
            'orders' => $orders,
        ]);
    }
}
