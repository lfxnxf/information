<?php

namespace App\Http\Controllers;

use App\Exceptions\UserException;
use App\Http\Requests\Admin\LoginRequest;
use App\Jobs\InsertLoginLog;
use App\Services\Admin\AdminUserService;
use App\Utils\Code;
use App\Utils\Result;
use App\Utils\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminController
{

    protected $adminUserService = null;

    public function __construct(AdminUserService $adminUserService)
    {
        $this->adminUserService = $adminUserService;
    }

    public function index(Request $request)
    {
        $userId = $request->input('user_id');
    }

    /**
     * @param LoginRequest $request
     * @return mixed
     * @throws UserException
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function login(LoginRequest $request)
    {
        $username = $request->input('username', '');
        $password = $request->input('password', '');
        //判断用户用户名密码是否正确
        $ret = $this->adminUserService->getOne($username, $password);
        if (!empty($ret)) {
            UserException::throwUserNotExistsException();
        }
        //token添加到缓存中
        $token = Utils::adminToken($username, $password);
        $this->setToken($token, $ret);
        //发送消息增加日志/发送短信通知客户登录成功。
        InsertLoginLog::dispatch($ret);
        return Result::getRes(Code::SUCCESS, ['token' => $token]);
    }

    /**
     * @param $token
     * @param $ret
     * @throws \Exception
     */
    public function setToken($token, $ret)
    {
        try{
            Cache::hMSet($token, json_encode($ret));
        }catch (\Exception $exception){
            throw $exception;
        }
    }
}
