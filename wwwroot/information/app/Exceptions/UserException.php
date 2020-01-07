<?php

namespace App\Exceptions;

use App\Utils\Code;

class UserException extends \Exception
{
    /**
     * 分配/释放时修改客户失败
     * @throws UserException
     */
    public static function throwUserNotExistsException()
    {
        throw new self('', Code::USER_NOT_EXISTS);
    }
}