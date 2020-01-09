<?php

namespace App\Http\Middleware;

use App\Exceptions\AdminUserException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CheckPermissionMiddleware
{

    protected $request = null;

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws AdminUserException
     */
    public function handle(Request $request, Closure $next)
    {
        $this->checkIsLogin($request);
        $this->checkPermission($request);
        return $next($request);
    }

    /**
     * @param $request
     * @return mixed
     * @throws AdminUserException
     */
    protected function checkIsLogin(Request &$request)
    {
        $token = $request->header('token');
        $ret = Redis::hGetAll($token);
        if (empty($ret)) {
            AdminUserException::throwNotLogin();
        }
        $request->merge(['user_id' => $ret['id']]);
    }

    protected function checkPermission(Request $request)
    {
        $ret = $request->getRequestUri();

    }
}
