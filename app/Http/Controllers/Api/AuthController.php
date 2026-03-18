<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * REGISTER - Daftar user baru
     */
    public function register(Request $request)
    {
        // 🔍 DEBUG: Log semua data yang masuk
        Log::info('Register Request Data:', $request->all());

        // Validasi input - PALING SIMPLE DULU
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'instansi' => 'nullable|string|max:255',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            // 🔍 DEBUG: Log error validasi
            Log::error('Validation Failed:', $validator->errors()->toArray());

            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Buat user baru
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'instansi' => $request->instansi,
                'role'     => 'user', // Default role
                'isAdmin'  => false,
            ]);

            // 🔍 DEBUG: Log user yang berhasil dibuat
            Log::info('User Created:', ['id' => $user->id, 'email' => $user->email]);

            // Buat token untuk user
            $token = $user->createToken('auth_token')->plainTextToken;

            // Response sukses
            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data'    => [
                    'user'         => $user,
                    'access_token' => $token,
                    'token_type'   => 'Bearer',
                ],
            ], 201);

        } catch (\Exception $e) {
            // 🔍 DEBUG: Log error saat create user
            Log::error('Register Error:', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * LOGIN - Masuk ke sistem
     */
    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Cek email & password
        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        // Ambil data user
        $user = User::where('email', $request->email)->firstOrFail();

        // Buat token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Response sukses
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data'    => [
                'user'         => $user,
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ],
        ], 200);
    }

    /**
     * LOGOUT - Keluar dari sistem
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang dipakai
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ], 200);
    }
}
