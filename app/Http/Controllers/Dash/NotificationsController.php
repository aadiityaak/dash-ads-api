<?php

namespace App\Http\Controllers\Dash;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    //get notifications user login
    public function index()
    {
        $notifications = auth()->user()->unreadNotifications;

        return response()->json($notifications);
    }

    //mark notifications as read
    public function markAsRead(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        auth()->user()->unreadNotifications->when($request->id, function ($query) use ($request) {
            return $query->where('id', $request->id);
        })->markAsRead();

        return response()->json('success');
    }

    //mark all notifications as read
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json('success');
    }
}
