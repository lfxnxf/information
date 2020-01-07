<?php

namespace App\Services\Admin;

use App\Models\UserModel;

class AdminUserService
{

    protected $userModel = null;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function getOne($username, $password)
    {
        $user = $this->userModel
            ->where('username', $username)
            ->where('password', md5($password))
            ->first();
        if (empty($user)) {
            return [];
        }
        return $user->toArray();
    }
}