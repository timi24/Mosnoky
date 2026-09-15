<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = AppNotification::where('user_id', $request->user()->id)
            ->orderByDesc('sent_at')
            ->paginate(15);

        AppNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('admin.notifications.index', [
            'notifications' => $notifications,
        ]);
    }
}
