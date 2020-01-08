<?php

namespace App\Services\Admin;

use App\Models\AdminUserModel;
use App\Models\LoginLogModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class AdminUserService
{

    protected $adminUserModel = null;

    protected $loginLogModel = null;

    public function __construct(AdminUserModel $adminUserModel, LoginLogModel $loginLogModel)
    {
        $this->adminUserModel = $adminUserModel;
        $this->loginLogModel = $loginLogModel;
    }

    public function getOne($username, $password)
    {
        $user = $this->adminUserModel
            ->where('username', $username)
            ->where('password', md5($password))
            ->first();
        if (empty($user)) {
            return [];
        }
        return $user->toArray();
    }


    /**
     * @param $token
     * @param $ret
     * @throws \Exception
     */
    public function setToken($token, $ret)
    {
        try {
            Redis::hMSet($token, $ret);
        } catch (\Exception $exception) {
            Log::error($this->logPre . ':{' . $exception->getMessage() . '}');
            echo 'FAILED!';
            throw $exception;
        }
    }

    /**
     * @param $id
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function updateUser($id, $data)
    {
        //修改最后登录时间
        try {
            $this->adminUserModel->where('id', $id)->update($data);
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function insertLoginLog($data)
    {
        try {
            $this->loginLogModel->create($data);
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}