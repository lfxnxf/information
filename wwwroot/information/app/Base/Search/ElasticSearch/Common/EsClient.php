<?php namespace App\Base\Search\ElasticSearch\Common;
/**
 * Desc     客户端基类 
 * @author  jiangyuan1<jiangyuan1@ganji.com>:
 * @version ID
 */
//use App\Base\Api\ElasticaSimple\Client;
use Elastica\Client;
use App\Base\Search\ElasticSearch\Common\Connector;
use App\Base\Search\ElasticSearch\Common\EsResult;

class EsClient {

    //只提供2个API search 和 count
    const QUERY_API_SEARCH     = '_search';
    const QUERY_API_COUNT      = '_count';

    const ES_METHOD            = 'GET';

    protected $client;    
    protected $connect;
    protected $result;

    public function __get($attr) {
        return $this->$attr;
    }

    public function __construct() {
        $this->getClient();
    }

    private function getClient() {
        $connect = new Connector(); 
        //do {
            $server       = $connect->getServer();
            //修改es超时时间为3秒
            foreach ($server['connections'] as $k=>$v){
                $server['connections'][$k]['timeout'] = 3;
            }
            $this->client = new Client($server);
/*
            if( $this->client->getStatus()->getResponse()->isOk() ) {
                return;
            }

            if($connect->getRunout() == 1) {
                throw new \Exception('没有可用的服务器可以连接');
            }

            unset($this->client); 
        } while(1);
*/
    }
    
    public function request($path, $query) {
        return $this->client->request($path, self::ES_METHOD, $query);
    }
    
}
