<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PeminjamanBarang;
use Illuminate\Support\Facades\Auth;

class PeminjamanNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function markAsRead()
    {
        PeminjamanBarang::where('user_id', Auth::id())
            ->where('is_read', false)
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
