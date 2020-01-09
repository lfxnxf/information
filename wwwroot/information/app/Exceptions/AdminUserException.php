<?php

namespace App\Exceptions;

use App\Utils\Code;

class AdminUserException extends \Exception
{
    /**
     * 分配/释放时修改客户失败
     * @throws AdminUserException
     */
    public static function throwUserNotExistsException()
    {
        throw new self('', Code::USER_NOT_EXISTS);
    }

    /**
     * @throws AdminUserException
     */
    public static function throwNotLogin()
    {
        throw new self('', Code::ADMIN_NOT_LOGIN);
    }

}