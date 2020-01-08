<?php

namespace App\Jobs;

use App\Services\Admin\AdminUserService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class AdminLoginQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $adminUserModel = null;
    protected $loginLogModel = null;
    protected $msg = null;
    protected $logPre = 'InsertLoginLog';

    /**
     * Create a new job instance.
     * InsertLoginLog constructor.
     * @param $msg
     */
    public function __construct($msg)
    {
        $this->msg = $msg;
    }

    /**
     * @param AdminUserService $adminUserService
     */
    public function handle(AdminUserService $adminUserService)
    {
        try {
            //token添加到缓存中
            $adminUserService->setToken($this->msg['token'], $this->msg);

            //修改用户信息
            $adminUserService->updateUser($this->msg['id'], [
                'last_login_at' => time(),
                'token'         => $this->msg['token']
            ]);

            //新增登录日志
            $data = [
                'admin_user_id' => $this->msg['id'],
                'ip'            => ip2long($this->msg['login_ip']),
                'create_at'     => time()
            ];
            $adminUserService->insertLoginLog($data);
            echo 'SUCCESS!';
        } catch (\Exception $exception) {
            Log::error($this->logPre . ':{' . $exception->getMessage() . '}');
            echo 'FAILED!';
        }

    }
}
