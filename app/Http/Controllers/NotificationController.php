<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(Request $request)
    {
        $user = auth()->user();

        if ($user) {
            $user->unreadNotifications->markAsRead();
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'unauthenticated'], 401);
    }
}
