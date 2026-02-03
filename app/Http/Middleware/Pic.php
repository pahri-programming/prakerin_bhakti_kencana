<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Pic
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Roles yang diizinkan (pic, admin, dll)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user sudah login
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userRole = auth()->user()->role;

        // Jika tidak ada parameter roles, default izinkan role 'pic'
        if (empty($roles)) {
            $roles = ['pic'];
        }

        // Check if user has any of the allowed roles
        if (! in_array($userRole, $roles)) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki hak untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
