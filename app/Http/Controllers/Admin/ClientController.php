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
        $searchTerms = preg_split('/\s+/', trim($request->string('search')->toString()), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $clients = Client::with('user')
            ->withCount('orders')
            ->when($searchTerms !== [], function ($query) use ($searchTerms) {
                $query->whereHas('user', function ($userQuery) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $userQuery->where(function ($nameQuery) use ($term) {
                            $nameQuery->where('first_name', 'like', "%{$term}%")
                                ->orWhere('last_name', 'like', "%{$term}%")
                                ->orWhere('name', 'like', "%{$term}%")
                                ->orWhere('email', 'like', "%{$term}%");
                        });
                    }
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
