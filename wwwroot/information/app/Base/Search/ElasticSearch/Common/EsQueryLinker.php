<?php
/**
 * ElasticSearch 查询工具类
 * created by wuzhengzhong@doumi.com
 * User: wuzhengzhong
 * Date: 2016-10-09 10:26:43
 */

namespace App\Base\Search\ElasticSearch\Common;

use Config;
use Elastica\Client;
use Elastica\Query;
use Elastica\QueryBuilder;
use Elastica\Search;
use DebugBar;

require_once Config::get('app.php_base') . 'util/elastica/es_autoload.php';

/**
 * Class EsQuery
 * elasticSearch 查询工具类，使用方式类似laravel的sql query build
 * example：
 *
 *  $esQuery = new EsQuery('/jianzhi_vip.jz_task', 'jz_task');
 *  $esQuery->where('listing_status', 5);
 *  $esQuery->where('modify_at', '>=', 1465873997);
 *  $esQuery->where('id', '!=', 16480);
 *  $esQuery->where(function($esQuery){
 *       $esQuery->orWhere('user_id', 100002)
 *               ->orWhere('title', 'like', '注册');
 *  });
 *  $esQuery->whereBetween('company_id', 10572,11572);
 *  $esQuery->whereIn('id', [10041,10108,16480]);
 *  $esQuery->orderBy('id', 'asc');
 *  $esQuery->orderBy('modify_at', 'desc');
 *  $esQuery->search(0,10);
 *  $resultSet = $esQuery->getResults();
 *  $total = $esQuery->getTotal();
 *
 * @package App\Base\Search\ElasticSearch\Common
 */
class EsQueryLinker
{

    /**
     * All of the available clause operators.
     *
     * @var array
     */
    protected $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=', 'like',
    ];

    protected $index;
    protected $type;
    protected $client;
    protected $query;
    protected $search;
    protected $resultSet;
    protected $conds = [];
    protected $sorts;

    /**
     * EsQuery constructor.
     * @param $index string 索引
     * @param string $type 类型
     * @param string $config 配置文件标识
     */
    public function __construct($index, $type = '', $config = null)
    {
        $this->index = $index;
        $this->type = $type;
        $connect = new EsConnector();
        //根据不同的类型获取不同配置 @lf
        if ($config) {
            $server = $connect->getServer($config);
        } else {
            $server = $connect->getServer($this->type);
        }
        $this->client = new Client($server);
        $this->search = new Search($this->client);
        $this->search->addIndex($index);
        if ($type) {
            $this->search->addType($type);
        }
        $qb = new QueryBuilder();
//        $this->boolQuery = $qb->query()->bool();
        $this->query = new Query();
    }

    /**
     * @param $path
     * @param $query
     * @param string $method
     * @return \Elastica\Response
     */
    protected function request($path, $query, $method = 'GET')
    {
        return $this->client->request($path, $method, $query);
    }

    /**
     * Add a basic where clause to the query.
     * example:
     *
     *  $esQuery->where('id', 16480);
     *  $esQuery->where('id', '>', 16480);
     *  $esQuery->where('id', '>=', 16480);
     *  $esQuery->where('id', '!=', 16480);
     *  $esQuery->where('title', 'like', '注册');
     *
     * @param $column
     * @param $operator
     * @param null $value
     * @param string $boolean
     * @throws \Exception
     * 增加是否需要把类型作为字段的前缀 $defaultPrefix
     */
    public function where($column, $operator, $value = null, $boolean = 'and', $defaultPrefix = true)
    {

        // Here we will make some assumptions about the operator. If only 2 values are
        // passed to the method, we will assume that the operator is an equals sign
        // and keep going. Otherwise, we'll require the operator to be passed in.
        if (func_num_args() == 2) {

            list($value, $operator) = [$operator, '='];
        } elseif ($this->invalidOperatorAndValue($operator, $value)) {
            throw new \Exception('Illegal operator and value combination.');
        }
        // If the columns is actually a Closure instance, we will assume the developer
        // wants to begin a nested where statement which is wrapped in parenthesis.
        // We'll add that Closure to the query then return back out immediately.

        if ($column instanceof \Closure) {
            return $this->whereNested($column, $boolean);
        }

        $qb = new QueryBuilder();
        $subQuery = null;
        $completeColumn = null;
        if ($defaultPrefix) {
            $completeColumn = $this->type . '-' . $column;
        } else {
            $completeColumn = $column;
        }

        if ($operator == '=') {
            $subQuery = $qb->query()->term([$completeColumn => $value]);
        } else if ($operator == '<') {
            $subQuery = $qb->query()->range($completeColumn, ['lt' => $value]);
        } else if ($operator == '>') {
            $subQuery = $qb->query()->range($completeColumn, ['gt' => $value]);
        } else if ($operator == '<=') {
            $subQuery = $qb->query()->range($completeColumn, ['lte' => $value]);
        } else if ($operator == '>=') {
            $subQuery = $qb->query()->range($completeColumn, ['gte' => $value]);
        } else if ($operator == '<>' || $operator == '!=') {
            $subQuery = $qb->query()->term([$completeColumn => $value]);
        } else if ($operator == 'like') {
            $subQuery = $qb->query()->match($completeColumn, ["query" => $value, "type" => "phrase_prefix"]);
        }

        if ($boolean == 'and') {
            if ($operator == '<>' || $operator == '!=') {
                $this->conds[] = [
                    'type' => 'mustNot',
                    'query' => $subQuery
                ];
            } else {
                $this->conds[] = [
                    'type' => 'must',
                    'query' => $subQuery
                ];
            }
        } else if ($boolean == 'or') {
            if ($operator == '<>' || $operator == '!=') {
                $boolQuery = $qb->query()->bool();
                $this->conds[] = [
                    'type' => 'should',
                    'query' => $boolQuery->addMustNot($subQuery)
                ];
            } else {
                $this->conds[] = [
                    'type' => 'should',
                    'query' => $subQuery
                ];
            }
        }
        return $this;
    }

    /**
     * Add a nested where statement to the query.
     *
     * @param  \Closure $callback
     * @param  string $boolean
     * @return \Illuminate\Database\Query\Builder|static
     */
    protected function whereNested(\Closure $callback, $boolean = 'and')
    {
        // To handle nested queries we'll actually create a brand new query instance
        // and pass it off to the Closure that we have. The Closure can simply do
        // do whatever it wants to a query then we will store it for compiling.
        $query = new static($this->index, $this->type);

        call_user_func($callback, $query);
        if ($boolean == 'and') {
            $this->conds[] = [
                'type' => 'mustNested',
                'query' => $query->conds
            ];
        } else if ($boolean == 'or') {
            $this->conds[] = [
                'type' => 'orNested',
                'query' => $query->conds
            ];
        }

        return $this;
    }

    /*
     * 查询嵌套文档@lf
     * [$path]=>array($column=>$value)
     *
     * */
    public function wherePath($column, $value, $path = null, $defaultPrefix = true)
    {
        $completeColumn = null;
        if ($defaultPrefix) {
            $completeColumn = $path . '.' . $column;
        } else {
            $completeColumn = $column;
        }
        $qb = new QueryBuilder();
        $nested = $qb->query()->nested();
        $nested->setPath($path);
        $nested->setQuery($qb->query()->term([$completeColumn => $value]));
        $this->conds[] = [
            'type' => 'must',
            'query' => $nested

        ];
        return $this;
    }

    /**
     * 添加and查询条件，相当于 where $column in ($values)
     * es检索的whereIn条件最多支持1024个值，当$values数组长度大于1000时，拆分成多个whereIn条件，然后用should连接
     * @param $column string 字段名
     * @param $values array 多个值组成的数组
     * @param $defaultPrefix bool
     * @throws
     * @return $this
     */
    public function whereIn($column, $values, $defaultPrefix = true)
    {
        $completeColumn = null;
        if ($defaultPrefix) {
            $completeColumn = $this->type . '-' . $column;
        } else {
            $completeColumn = $column;
        }
        $qb = new QueryBuilder();
        $count = count($values);
        if ($count > 1000) {
            if ($count > 20000) {
                throw new \Exception('Too many term, max term count is 20000, ' . $count . ' is given');
            }
            $boolQuery = $qb->query()->bool();
            while ($values) {
                $tmpArr = array_splice($values, 0, 1000);
                $boolQuery->addShould($qb->query()->terms($completeColumn, $tmpArr));
            }
            $this->conds[] = [
                'type' => 'must',
                'query' => $boolQuery
            ];
        } else {
            $this->conds[] = [
                'type' => 'must',
                'query' => $qb->query()->terms($completeColumn, $values)
            ];
        }
        return $this;
    }

    /**
     * 添加and查询条件，相当于 where $column not in ($values)
     * es检索的whereNotIn条件最多支持1024个值，当$values数组长度大于1000时，拆分成多个whereIn条件，然后用should连接
     * @param $column 字段名
     * @param $values 多个值组成的数组
     */
    public function whereNotIn($column, $values, $defaultPrefix = true)
    {
        $completeColumn = null;
        if ($defaultPrefix) {
            $completeColumn = $this->type . '-' . $column;
        } else {
            $completeColumn = $column;
        }
        $qb = new QueryBuilder();
        $count = count($values);
        if ($count > 1000) {
            if ($count > 20000) {
                throw new \Exception('Too many term, max term count is 20000, ' . $count . ' is given');
            }
            $boolQuery = $qb->query()->bool();
            while ($values) {
                $tmpArr = array_splice($values, 0, 1000);
                $boolQuery->addShould($qb->query()->terms($completeColumn, $tmpArr));
            }
            $this->conds[] = [
                'type' => 'mustNot',
                'query' => $boolQuery
            ];
        } else {
            $this->conds[] = [
                'type' => 'mustNot',
                'query' => $qb->query()->terms($completeColumn, $values)
            ];
        }
        return $this;
    }

    /**
     * 添加and查询条件，相当于 and $column between $from and $to
     * @param $column
     * @param $from
     * @param $to
     */
    public function whereBetween($column, $from, $to, $defaultPrefix = true)
    {
        $completeColumn = null;
        if ($defaultPrefix) {
            $completeColumn = $this->type . '-' . $column;
        } else {
            $completeColumn = $column;
        }
        $qb = new QueryBuilder();
        $this->conds[] = [
            'type' => 'must',
            'query' => $qb->query()->range($completeColumn, ['gte' => $from, 'lte' => $to])
        ];
        return $this;
    }


    /**
     * 添加连接查询条件，相当于 inner join $type on $column = $value
     * @param $type 同一个索引(index)下的其他类型(type)
     * @param $column
     * @param $value
     */
    public function join($type, $column, $value)
    {
        $completeColumn = null;
        $completeColumn = $type . '-' . $column;

        $qb = new QueryBuilder();
        $onQuery = $qb->query()->term([$completeColumn => $value]);
        $this->conds[] = [
            'type' => 'must',
            'query' => $qb->query()->has_child($onQuery, $type)
        ];
        return $this;
    }

    /**
     * 添加"or"查询条件
     * @param $column
     * @param $operator
     * @param null $value
     * @param bool $defaultPrefix
     * @return $this
     * @throws \Exception
     */
    public function orWhere($column, $operator, $value = null, $defaultPrefix = true)
    {
        if (func_num_args() == 2) {
            list($value, $operator) = [$operator, '='];
        } elseif ($this->invalidOperatorAndValue($operator, $value)) {
            throw new \Exception('Illegal operator and value combination.');
        }
        $this->where($column, $operator, $value, 'or', $defaultPrefix);
        return $this;
    }

    /**
     * 添加or查询条件，相当于 or $column between $from and $to
     * @param $column
     * @param $from
     * @param $to
     * @param bool $defaultPrefix
     * @return $this
     */
    public function orWhereBetween($column, $from, $to, $defaultPrefix = true)
    {
        $completeColumn = null;
        if ($defaultPrefix) {
            $completeColumn = $this->type . '-' . $column;
        } else {
            $completeColumn = $column;
        }
        $qb = new QueryBuilder();
        $this->conds[] = [
            'type' => 'should',
            'query' => $qb->query()->range($completeColumn, ['gte' => $from, 'lte' => $to])
        ];
        return $this;
    }

    /**
     * * 添加or查询条件，相当于 or $column in ($values)
     * es检索的whereIn条件最多支持1024个值，当$values数组长度大于1000时，拆分成多个whereIn条件，然后用should连接
     * @param $column
     * @param $values
     * @param bool $defaultPrefix
     * @return $this
     */
    public function orWhereIn($column, $values, $defaultPrefix = true)
    {
        $completeColumn = null;
        if ($defaultPrefix) {
            $completeColumn = $this->type . '-' . $column;
        } else {
            $completeColumn = $column;
        }
        $qb = new QueryBuilder();
        if (count($values) > 1000) {
            $boolQuery = $qb->query()->bool();
            while ($values) {
                $tmpArr = array_splice($values, 0, 1000);
                $boolQuery->addShould($qb->query()->terms($completeColumn, $tmpArr));
            }
            $this->conds[] = [
                'type' => 'should',
                'query' => $boolQuery
            ];
        } else {
            $this->conds[] = [
                'type' => 'should',
                'query' => $qb->query()->terms($completeColumn, $values)
            ];
        }
        return $this;
    }

    /**
     * 排序方式，相当于order by $column $sort
     * @param $column
     * @param string $sort
     * @param bool $defaultPrefix
     * @return $this
     */
    public function orderBy($column, $sort = 'asc', $defaultPrefix = true)
    {
        $completeColumn = null;
        if ($defaultPrefix) {
            $completeColumn = $this->type . '-' . $column;
        } else {
            $completeColumn = $column;
        }
        $this->sorts[$completeColumn] = $sort;
        return $this;
    }

    /**
     * 执行查询
     * @param int $start 分页起始位置
     * @param int $pageSize 每页记录数
     */
    public function search($start = 0, $pageSize = 10)
    {

        $boolQuery = $this->assembleQuery($this->conds);
        $this->query->setQuery($boolQuery);
        $this->search->setQuery($this->query);

        //es深分页性能问题优化
        if ($start + $pageSize > 10000) {
            $start = 10000 - $pageSize;
        }
        $this->query->setFrom($start);
        $this->query->setSize($pageSize);

        if ($this->sorts) {
            $this->query->setSort($this->sorts);
        }
        //echo json_encode($this->search->getQuery()->toArray());
        //dump($this->search->search()->getResults());exit;
        $this->resultSet = $this->search->search();
    }

    /**
     * 获取查询结果集
     * @return array
     */
    public function getResults()
    {
        $results = $this->resultSet->getResults();
        $ret = [];
        foreach ($results as $result) {
            //检索升级，结果集去除类型前缀 @lf
            $source = $result->getSource();
            $data = [];
            foreach ($source as $key => $item) {
                $k = str_replace($this->type . '-', '', $key);
                $data[$k] = $item;
            }
            $ret[] = $data;
        }

        return $ret;
    }

    /**
     * 获取记录总数
     * @return mixed
     */
    public function getTotal()
    {
        return $this->resultSet->getTotalHits();
    }

    /**
     * 判断操作符和值是否有效
     *
     * @param  string $operator
     * @param  mixed $value
     * @return bool
     */
    protected function invalidOperatorAndValue($operator, $value)
    {
        $isOperator = in_array($operator, $this->operators);

        return $isOperator && $operator != '=' && is_null($value);
    }

    /**
     * 组装条件
     */
    protected function assembleQuery($conds)
    {

        $qb = new QueryBuilder();
        $boolQuery = $qb->query()->bool();
        foreach ($conds as $cond) {
            if ($cond['type'] == 'must') {
                $boolQuery->addMust($cond['query']);
            } else if ($cond['type'] == 'mustNot') {
                $boolQuery->addMustNot($cond['query']);
            } else if ($cond['type'] == 'should') {
                $boolQuery->addShould($cond['query']);
            } else if ($cond['type'] == 'mustNested') {
                $nestedQuery = $this->assembleQuery($cond['query']);
                $boolQuery->addMust($nestedQuery);
            } else if ($cond['type'] == 'shouldNested') {
                $nestedQuery = $this->assembleQuery($cond['query']);
                $boolQuery->addShould($nestedQuery);
            }
        }
        return $boolQuery;
    }

    /**
     * 判断字段存在
     * @param $column
     * @param string $value
     * @param null $path
     * @param bool $defaultPrefix
     * @return $this
     */
    public function whereExists($column, $value = '', $path = null, $defaultPrefix = true)
    {
        $completeColumn = null;
        if ($defaultPrefix) {
            $completeColumn = $path . '.' . $column;
        } else {
            $completeColumn = $column;
        }
        $qb = new QueryBuilder();
        $nested = $qb->query()->nested();
        $nested->setPath($path);
        $nested->setExistsQuery($qb->filter()->exists($completeColumn));
        $this->conds[] = [
            'type' => 'must',
            'query' => $nested

        ];
        return $this;
    }

    /**
     * 判断字段不存在
     * @param $column
     * @param string $value
     * @param null $path
     * @param bool $defaultPrefix
     * @return $this
     */

    public function whereExistsMustNot($column, $value = '', $path = null, $defaultPrefix = true)
    {
        $completeColumn = null;
        if ($defaultPrefix) {
            $completeColumn = $path . '.' . $column;
        } else {
            $completeColumn = $column;
        }
        $qb = new QueryBuilder();
        $nested = $qb->query()->nested();
        $nested->setPath($path);
        $nested->setQuery($qb->filter()->exists($completeColumn));
        $this->conds[] = [
            'type' => 'mustNot',
            'query' => $nested

        ];
        return $this;
    }

    /**
     * 获取多个id数据
     * @param $column
     * @param $values
     * @param null $path
     * @param bool $defaultPrefix
     * @return $this
     * @throws \Exception
     */
    public function wherePathIn($column, $values, $path = null, $defaultPrefix = true)
    {
        $completeColumn = null;
        if ($defaultPrefix) {
            $completeColumn = $path . '.' . $column;
        } else {
            $completeColumn = $column;
        }
        $qb = new QueryBuilder();
        $count = count($values);
        $nested = $qb->query()->nested();
        $nested->setPath($path);
        if ($count > 1000) {
            if ($count > 20000) {
                throw new \Exception('Too many term, max term count is 20000, ' . $count . ' is given');
            }
            $boolQuery = $nested->setQuery($qb->query()->bool());
            while ($values) {
                $tmpArr = array_splice($values, 0, 1000);
                $boolQuery->addShould($qb->query()->terms($completeColumn, $tmpArr));
            }
            $this->conds[] = [
                'type' => 'must',
                'query' => $boolQuery
            ];
        } else {
            $this->conds[] = [
                'type' => 'must',
                'query' => $nested->setQuery($qb->query()->terms($completeColumn, $values))
            ];
        }
        return $this;
    }

}
