<?php namespace App\Base\Search\ElasticSearch\Common;

/**
 * @Desc    获取服务器连接配置
 * @author  jiangyuan1<jiangyuan1@ganji.com>:
 * @version ID
 */
use Config;

class Connector {

    const DEFAULT_SERVER_KEY = 1;

    protected $config   = null;
    protected $used     = [];
    protected $isRunout = null; //表示是否所有的服务器都遍历完

    public function __construct() {
        $this->config = Config::get('esconfig.jianzhi');
    }

    public function getServer() {
	return $this->config;
	/*
        $keys = array_keys($this->config);
        $key  = $this->roulette($keys);

        if($key === '') {
            $key = self::DEFAULT_SERVER_KEY;
        }

        $ret = $this->formatConfig( $this->config[$key] );
        unset($this->config[$key]);
        if( empty($this->config) ) {
            $this->isRunout = 1;
        }

        return $ret;
	*/
    }

    public function getRunout() {
        return $this->isRunout;
    }

    private function formatConfig($config) {
        $ret = [];
        $ret['host']   = $config['host'];
        $ret['port']   = $config['port'];

        return $ret;
    }

    private function roulette($keys) {
        $sum    = 0;

        foreach($keys as $key) {
            $sum += $this->config[$key]['weight'];
        }

        $random = $this->randomFloat();
        $m      = 0;
        foreach($keys as $key) {
            $m += $this->config[$key]['weight']/$sum;
            if($m >= $random  ) {
                return $key;
            }
        }
    }

    private function randomFloat($min = 0, $max = 1) {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

}