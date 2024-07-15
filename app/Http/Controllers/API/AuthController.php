<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Repositories\AuthRepository;

class AuthController extends Controller
{
    private $repository;

    public function __construct()
    {
        $this->repository = new AuthRepository();
    }

    public function login(LoginRequest $request)
    {
        try {
            $data = $this->repository->login($request);
            return response()->success($data, 'Selamat, Kamu berhasil login!');
        } catch (\Throwable $e) {
            return response()->error($e->getMessage(), 400);
        }
    }

    public function me()
    {
        try {
            $data = $this->repository->me();
            return response()->success($data, 'Data berhasil didapatkan!');
        } catch (\Throwable $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }

    public function logout()
    {
        try {
            $data = $this->repository->logout();
            return response()->success($data, 'Berhasil Logout!');
        } catch (\Throwable $e) {
            return response()->error($e->getMessage(), $e->getCode());
        }
    }
}
