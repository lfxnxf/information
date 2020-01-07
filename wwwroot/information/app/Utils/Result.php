<?php

namespace App\Utils;

class Result
{

    /**
     * 返回数据
     * @param $code
     * @param array $data
     * @param string $msg
     * @return mixed
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    static public function getRes($code, $data = [], $msg = '')
    {
        $defaultMsg = Code::showText($code);
        if (empty($msg)) {
            $msg = $defaultMsg;
        } else {
            $msg = "$defaultMsg $msg";
        }

        return apiResponse()->json(empty($data) ?
            ['error' => $code, 'err_msg' => $msg] :
            ['error' => $code, 'err_msg' => $msg, 'data' => $data]
        );
    }

    /**
     * @param \Exception $e
     * @return mixed
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    static public function getExceptionRes(\Exception $e)
    {
        $defaultMsg = Code::showText($e->getCode());
        $msg = $defaultMsg . ' ' . $e->getMessage();

        return apiResponse()->json(
            ['error' => $e->getCode(), 'err_msg' => $msg, 'data' => []]
        );
    }
}
