<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;


class AuthController extends ApiController
{
    public function register(RegisterRequest $request)
    {

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => 'kasir', 
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user'       => $user,
            'token'      => $token,
            'token_type' => 'Bearer',
        ], 'Registrasi berhasil.', 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->error('Email atau password salah.', 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

         return $this->success([
            'user'       => $user,
            'token'      => $token,
            'token_type' => 'Bearer',
        ], 'Login berhasil.');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logout berhasil.');
    }

    public function user(Request $request)
    {
        return $this->success($request->user(), 'Data user berhasil diambil.');
}
}