<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function markAsRead()
    {
        Booking::where('user_id', Auth::id())
            ->where('is_read', false)
            ->whereIn('status', ['Diterima', 'Ditolak'])
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
