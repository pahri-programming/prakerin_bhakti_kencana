<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect ke halaman login Google
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cari user berdasarkan email
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // Jika user sudah ada, login
                Auth::login($user);
            } else {
                // Jika user belum ada, buat user baru
                $user = User::create([
                    'name'              => $googleUser->name,
                    'email'             => $googleUser->email,
                    'password'          => bcrypt(Str::random(16)), // Random password
                    'instansi'          => 'Belum diisi',           // Default instansi
                    'isAdmin'           => 0,
                    'role'              => 'user',
                    'email_verified_at' => now(), // Langsung terverifikasi
                ]);

                Auth::login($user);
            }

            // Redirect berdasarkan role
            if ($user->isAdmin == 1) {
                return redirect('/admin');
            }

            return redirect('/');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }
}
