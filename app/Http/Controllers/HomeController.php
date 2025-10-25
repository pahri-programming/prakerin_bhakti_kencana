<?php
namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        // Kalau admin -> ke dashboard backend
        if ($user->isAdmin == 1) {
            return redirect()->route('backend.index');
        }

        // Kalau teknisi -> ke dashboard teknisi
        if ($user->role === 'teknisi') {
            return redirect()->route('teknisi.index');
        }

        // Selain itu (member/user biasa) -> tampilkan welcome page
      return redirect()->route('frontend.welcome');
    }
}
