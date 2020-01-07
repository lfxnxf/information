<?php

namespace App\Utils;

class Utils
{

    private static $servicePath;


    /**
     * @return string
     */
    private static function getServicePath()
    {
        return self::$servicePath = app_path('Service') . '/';
    }

    /**
     * @return string
     */
    private static function getModelsPath()
    {
        return self::$servicePath = app_path('Models') . '/';
    }

    /**
     * 获取随机字符串
     * @param int $length
     * @return string
     */
    public static function getRandomString($length = 32)
    {
        $chars = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 获取随机数
     * @param $length
     * @return string
     */
    public static function random($length)
    {
        $hash = '';
        $chars = '0123456789';
        $max = strlen($chars) - 1;
        mt_srand((double)microtime() * 1000000);
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }

    /**
     * @param $service
     * @return bool
     */
    public static function getService($service)
    {
        $serviceFile = self::getServicePath() . $service . '.php';
        if (!file_exists($serviceFile)) {
            return false;
        }
        $serviceClass = "\\App\\Service\\" . $service;
        return new $serviceClass();
    }

    /**
     * 调用远程接口方法
     * @param $url
     * @param bool|true $https
     * @param string $method
     * @param null $data
     * @param array $header
     * @param string $sslCertPath
     * @param string $sslKeyPath
     * @return mixed|string
     */
    public static function httpCurl($url, $https = true, $method = 'GET', $data = null, $header = array(), $sslCertPath = "", $sslKeyPath = "")
    {
        $ch = curl_init();//初始化
        /**
         * put参数设置
         */
        if ($method == 'PUT') {
            $header[] = 'Content-Type: application/json';
            if (is_array($data)) {
                $data = json_encode($data);
            }
        }
        if (is_array($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//不做服务器认证
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//不做客户端认证
        }
        switch ($method) {
            case 'GET' :
                if (strpos($url, '?') === false) {
                    $url .= '?';
                } else {
                    $url .= '&';
                }
                $url .= http_build_query($data);
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);//设置请求是POST方式
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//设置POST请求的数据
                break;
            case 'PUT' :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //设置请求体，提交数据包
                break;
            case 'DELETE' :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        curl_setopt($ch, CURLOPT_URL, $url);//设置访问的URL
        curl_setopt($ch, CURLOPT_HEADER, false);//设置不需要头信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//只获取页面内容，但不输出
        if (!empty($sslCertPath)) {
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            //默认格式为PEM，可以注释
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $sslCertPath);
            //默认格式为PEM，可以注释
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $sslKeyPath);
        }
        $str = curl_exec($ch);//执行访问，返回结果
        if (curl_errno($ch) == 28) {//超时返回结果空
            return '';
        }
        if (curl_errno($ch)) {
            return 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);//关闭curl，释放资源
        return $str;
    }

    /**
     * 获取模型对象并给模型赋值
     * @param $modelName
     * @param $request
     * @return bool
     */
    public static function getModel($modelName = '', $request = [])
    {
        $modelFile = self::getModelsPath() . $modelName . '.php';
        if (!file_exists($modelFile)) {
            return false;
        }
        $modelClass = "\\App\\Models\\" . $modelName;
        $model = new $modelClass();
        if (empty($request)) {
            return $model;
        }
        return self::setRequest($model, $request);
    }

    /**
     * 给模型赋值
     * @param $model
     * @param $request
     * @return mixed
     */
    public static function setRequest($model, $request)
    {
        foreach ($request as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }

    /**
     * 获取分页字符串
     * @param int $page
     * @param int $limit
     * @return string
     */
    public static function setOffset($page, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        return $offset;
    }

    /**
     * 获取orderBy
     * @param array $orderBy
     * @return string
     */
    public static function getOrderBy($orderBy = [])
    {
        $orderStr = '';
        if (!empty($orderBy) && is_array($orderBy)) {
            foreach ($orderBy as $key => $value) {
                $orderStr .= empty($orderStr) ? $key . ' ' . $value : ',' . $key . ' ' . $value;
            }
        }
        return $orderStr;
    }

    /**
     * @param $username
     * @param $password
     * @return string
     */
    public static function adminToken($username, $password)
    {
        return md5($username . $password . self::random(16));
    }
}