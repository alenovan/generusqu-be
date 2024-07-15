<?php

namespace App\Repositories;

use App\Models\Groups;
use App\Traits\GlobalTrait;
use Exception;
use Illuminate\Support\Facades\Hash;

class GroupsRepository
{
    use GlobalTrait;

    
    public function get()
    {
        try {
            $user = Groups::get();
            return $user;
        } catch (\Exception $e) {
            throw $e;
            report($e);
            return $e;
        }
    }

  
}
