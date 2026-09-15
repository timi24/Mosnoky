<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $reviews = Review::with(['client.user', 'product'])
            ->when($request->filled('status'), fn ($q) => $q->where('moderation_status', $request->string('status')))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'currentStatus' => $request->string('status')->toString(),
        ]);
    }

    public function approve(Review $review): RedirectResponse
    {
        $review->update(['moderation_status' => 'APPROUVE']);

        return back()->with('status', 'Avis approuvé et visible sur la boutique.');
    }

    public function hide(Review $review): RedirectResponse
    {
        $review->update(['moderation_status' => 'MASQUE']);

        return back()->with('status', 'Avis masqué.');
    }
}
