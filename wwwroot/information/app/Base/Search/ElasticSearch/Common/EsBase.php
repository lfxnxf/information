<?php namespace App\Base\Search\ElasticSearch\Common;
use Config;
require_once Config::get('app.php_base') . 'util/elastica/es_autoload.php';

/**
 * @Desc    列表页检索对外接口基类
 * @author  jiangyuan1<jiangyuan1@ganji.com>:
 */
abstract class EsBase {

    //只提供2个API search 和 count
    const QUERY_API_SEARCH     = '_search'; 
    const QUERY_API_COUNT      = '_count';

    protected $client          = null;
    protected $queryBuilderObj = null;
    protected $result          = null;

    protected function wrapData( $response) {
        $response     = new EsResult($response);
        $this->result = $response;

    }
    
    /**
     *@brief: 请求返回原始数据
     */
    public function queryRaw($path, $query) {
        return $this->client->request($path, $query);
    }

    /**
     *@brief: 请求
     */
    public function query($path, $query) {
        $response = $this->client->request($path, $query);
        $this->wrapData( $response);
        return $this->result->getData();
    }

    /**
     *@brief: 检索对外接口
     */
    abstract public function searching();

    /**
    public function query($path, $query) {
        $path        .= self::QUERY_API_SEARCH;
        return $this->result->getData();
    }

    public function total($path, $query) {
        if($this->result) {
            return $this->result->getTotal();            
        }

        $path .= self::QUERY_API_COUNT; 
        return $this->client->request($path, $query);
    }
    */

}
