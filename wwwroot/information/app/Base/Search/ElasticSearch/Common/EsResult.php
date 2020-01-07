<?php namespace App\Base\Search\ElasticSearch\Common;
/**
 * Desc     检索结果 
 * @author  jiangyuan1<jiangyuan1@ganji.com>:
 */
//use App\Base\Api\ElasticaSimple\ResultSet;
use Elastica\ResultSet;

class EsResult {

    protected $elasticaResult; //elastica的ResultSet对象

    public function __construct($response) {
        $this->elasticaResult = ResultSet::create($response);
    }

    public function setResult($response) {
        $this->elasticaResult = ResultSet::create($response);
        return $this;
    }

    public function getData() {
        $results = $this->elasticaResult->getResults();
        $ret = [];
        foreach($results as $result) {
            $ret[] = $result->getSource();
        }
        return $ret;
    }

    public function getRawData() {
        return $this->elasticaResult->getResults();
    }

    public function getTotal() {
        return $this->elasticaResult->getTotalHits();
    }

}
