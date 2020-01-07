<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/16
 * Time: 11:57
 */

namespace App\Custom;

use Illuminate\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

class ApiResponse extends ResponseFactory
{
    protected $disableCamelize = false;

    /**
     * @return $this
     */
    public function disableCamelize()
    {
        $this->disableCamelize = true;
        return $this;
    }

    public function json($data = [], $status = 200, array $headers = [], $options = 0)
    {
        if (isset($data['data']) && $this->disableCamelize === false) {
            $data['data'] = $this->change($data['data']);
        }

        $options |= JSON_UNESCAPED_UNICODE;

        return new JsonResponse($data, $status, $headers, $options);
    }

    /**
     * 递归修改字段，_修改为驼峰
     * @param $data
     * @return array
     */
    public function change($data)
    {
        if (!is_array($data)) {
            return [];
        }
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->change($value);
            }
            if (strpos($key, '_') !== false) {
                $data[camel_case($key)] = $value;
                unset($data[$key]);
            } else {
                $data[$key] = $value;
            }
        }
        return $data;
    }
}