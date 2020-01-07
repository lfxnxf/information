<?php namespace App\Base\Search\ElasticSearch\Common;
/**
 * Description. 
 * @author  jiangyuan1<jiangyuan1@ganji.com>:
 * @version ID
 * @copyright Copyright &copy;  2012-2013 ganji.com
 */

class QueryBuilder {
    protected $queryBody = null;
    protected $params = null;

    static $rangeMap = [
        '>=' => 'gte',
        '>'  => 'gt',
        '<=' => 'lte',
        'lt' => 'lt',
    ];

    public function __construct() {

    }
    
    public function __get($attr) {
        return $this->$attr;
    }

    public function setQuery($query) {
        $this->queryBody = $query;
    }

    /**
     *@brief: 根据经纬度排序，获取要排序的查询串
     */
    public function getGeoSort($lat, $lng, $order = "asc", $unit = "km", $distanceType = "plane") {
        $sort = [   
            [      
                "_geo_distance" => [
                    "location" => [
                        "lat" => $lat,
                        "lon" => $lng
                        ],
                    "order" => $order,
                    "unit"  => $unit,
                    "distance_type" => $distanceType
                ]   
            ]  
        ];
        return $sort;
    }

    /**
     *@brief: 生成ids查询数组
     *@param: $ids | array
     */
    public function ids($ids) {
        if(is_array($ids)) {
            return false;
        }

        return [
            "ids" => [
                "values" => $ids
            ]
        ];
    }
    
    /**
     *@brief: 类似于mysql的where查询
     *@param: $where | array 例如 $where = [['listing_status' => 1], ['ad_types' => 2] ]   
     */
    public function getTerms($where) {
        $terms = [];
        foreach($where as $item) {
            $terms[]['term'] = $item;
        }
        return $terms;
    }

    /**
     *@brief: 范围查询
     *@param: $where | array 例如 $where = [['listing_status', '>', 1], ['ad_types', '<=',  2] ]   
     */
    public function getRange($where) {
        $range = [];
        foreach($where as $item) {
            if(empty($item[0]) || empty($item[1])) {
                continue;
            }

            if(!isset($item[2])) {
                continue;
            }
    
            if(empty(self::$rangeMap[$item[1]])) {
                continue;
            }

            $range[]['range'][$item[0]] = [ self::$rangeMap[$item[1]] => $item[2] ];
        }

        return $range;
    }

	public function getWhereIn($where) {
		$terms = [];

		foreach ($where as $item) {
			$terms[]['terms'] = $item;
		}

		return $terms;
	}

    /*
    public function getParent($type, $item) {
        $item = $this->getTerms($item);
        $parent = [
            "type"     => $type,
            "filter"   => [
                "and"  => $item
            ]
        ];
        return $parent;
    }
    
    public function getChild($type, $and) {
        $and = $this->getTerms($and); 
        $hasChild = [
            'type'   => $type,
            'query'  => [
                'filtered' => [
                    "query" =>  [ "match_all" =>  [] ] ,
                    "filter" => [
                        "and" => $and,
                    ]
                ],
            ],
        ];

        $filter = [
            'has_child' => $hasChild
        ];

        return $filter;
    }
    */

    /**
     *@brief: 设置排序
     */
    public function addSort($sort) {
        $this->queryBody['sort'] = $sort;
    }
   
    /**
     *@brief: 设置检索偏移量
     */ 
    public function addFrom($from) {
        $from = (int)$from;
        $this->queryBody['from'] = $from;
    }

    /**
     *@brief: 设置检索数量
     */ 
    public function addSize($size) {
        $size = (int)$size;
        $this->queryBody['size'] = $size;
    }

}
