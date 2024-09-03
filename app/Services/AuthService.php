<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(array $credentials)
    {
        if (!$token = Auth::attempt($credentials)) {
            return ['error' => 'Unauthorized', 'status' => 401];
        }
        return $this->createNewToken($token);
    }

    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        return $user;
    }

    public function createNewToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ];
    }

    public function logout()
    {
        Auth::logout();
    }

    public function refresh()
    {
        return $this->createNewToken(Auth::refresh());
    }
}
