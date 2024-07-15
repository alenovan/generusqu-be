<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\GroupsRepository;
use Illuminate\Http\Request;
use App\Repositories\OrdersRepository;
class GroupsController extends Controller
{
    private $repository;

    public function __construct()
    {
        $this->repository = new GroupsRepository();
    }

    public function get()
    {
        try {
            $data = $this->repository->get();
            return response()->success($data, 'Data Berhasil di ambil');
        } catch (\Throwable $e) {
            return response()->error($e->getMessage(), 400);
        }
    }



}
