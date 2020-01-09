<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AdminUserException;
use App\Http\Requests\Admin\LoginRequest;
use App\Jobs\AdminLoginQueue;
use App\Services\Admin\AdminUserService;
use App\Utils\Code;
use App\Utils\Result;
use App\Utils\Utils;
use Illuminate\Http\Request;

class UserController
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
     * @throws AdminUserException
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function login(LoginRequest $request)
    {
        $username = $request->input('username', '');
        $password = $request->input('password', '');
        //判断用户用户名密码是否正确
        $ret = $this->adminUserService->getOne($username, $password);
        if (empty($ret)) {
            AdminUserException::throwUserNotExistsException();
        }
        //发送消息增加日志/添加缓存/发送短信通知客户登录成功。
        $token = Utils::adminToken($username, $password);
        $ret['login_ip'] = Utils::getRealClientIp();
        $ret['token'] = $token;
        AdminLoginQueue::dispatch($ret)->onQueue('admin.login');
        return Result::getRes(Code::SUCCESS, ['token' => $token]);
    }

}
