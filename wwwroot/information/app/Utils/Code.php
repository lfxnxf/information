<?php

namespace App\Utils;

class Code
{
    /**
     * 成功
     */

    const SUCCESS = 0;

    const SYSTEM_ERROR_CODE = 1001;
    const DB_FAILED = 1002;
    const URL_FAILED = 1003;
    const METHOD_NOT_ALLOWED = 1004;//没有http权限
    const ADMIN_NOT_LOGIN = 100001;//未登录
    const NOT_HAS_PERMISSION = 100002;//没有权限

    /**
     * 错误码
     */
    const VALIDATE_ERROR_CODE = 2001;//验证错误

    const USER_NOT_EXISTS = 3001; //用户不存在

    /**
     * 错误码描述内容
     * @var array
     */
    static $_Code = [
        /**
         * 成功
         */
        self::SUCCESS             => '',
        self::SYSTEM_ERROR_CODE   => '系统错误',
        self::METHOD_NOT_ALLOWED  => '路由没有没有http权限',
        self::DB_FAILED           => '数据库执行错误',
        self::URL_FAILED          => '接口URL错误',
        self::VALIDATE_ERROR_CODE => '参数错误',
        self::ADMIN_NOT_LOGIN     => '未登录,请重新登录',
        self::NOT_HAS_PERMISSION  => '没有权限！',

        /**
         * 用户错误码
         */
        self::USER_NOT_EXISTS     => '用户不存在',
    ];

    /**
     * @param $code
     * @return mixed
     */
    static function showText($code)
    {
        return static:: $_Code[$code] ?? static:: $_Code[self::SYSTEM_ERROR_CODE];
    }
}