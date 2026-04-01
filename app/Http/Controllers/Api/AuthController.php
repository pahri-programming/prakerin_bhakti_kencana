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
        Log::info('Register Request Data:', $request->all());

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'instansi' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::error('Validation Failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'instansi' => $request->instansi,
                'role'     => 'user',
                'isAdmin'  => false,
            ]);

            Log::info('User Created:', ['id' => $user->id, 'email' => $user->email]);

            $token = $user->createToken('auth_token')->plainTextToken;

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
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        $user  = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data'    => [
                'user'         => [
                    'id'       => $user->id,
                    'name'     => $user->name,
                    'email'    => $user->email,
                    'instansi' => $user->instansi,
                    'role'     => $user->role,
                ],
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
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ], 200);
    }

    /**
     * ME - Ambil data user yang sedang login
     * Dipakai Flutter untuk tampilkan profil & cek role
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'instansi' => $user->instansi,
                'role'     => $user->role,
            ],
        ], 200);
    }
}
