<?php

namespace App\Repositories;

use App\Models\User;
use App\Traits\GlobalTrait;
use Exception;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    use GlobalTrait;

    public function login($request)
    {
        try {
            $credentials = $request->only(['username', 'password']);

            $user = User::where('username', $credentials['username'])->first();
            if (!$user) {
                $this->ApiException('Username tidak ditemukan');
            }

            if (!Hash::check($credentials['password'], $user->password)) {
                $this->ApiException('Password salah');
            }

            if (!$token = auth('api')->login($user)) {
                $this->ApiException('Login gagal');
            }

            $user['token'] = $token;
            return $user;
        } catch (\Exception $e) {
            throw $e;
            report($e);
            return $e;
        }
    }

    public function me()
    {
        try {
            $user = User::with('details')->find($this->getUserAuth()->id);
            return $user;
        } catch (\Exception $e) {
            throw $e;
            report($e);
            return $e;
        }
    }

    public function logout()
    {
        try {
            auth('api')->logout();
            return true;
        } catch (\Exception $e) {
            throw $e;
            report($e);
            return $e;
        }
    }
}
