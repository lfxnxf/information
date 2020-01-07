<?php
/**
 * created by wuzhengzhong@doumi.com
 * User: wuzhengzhong
 * Date: 2016-10-17 11:27:34
 */

namespace App\Base\Queue\Rabbitmq;

use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPIOException;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use PhpAmqpLib\Exception\AMQPProtocolConnectionException;
use PhpAmqpLib\Exception\AMQPProtocolException;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use Config;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitmqClient
{
    protected $handler;
    protected $exchange = '';
    protected $queueKey = "";
    protected $queueConf = [];
    protected $arrCurrentConf = [];
    protected $consumerTag = "";
    private $exchangeType = 'fanout';
    private $delayTime = 0;
    private $tale;
    private $delayExchange = 'delay_exchange';
    private $routeKey = '';
    private $tryNum = 0;
    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    public $channel;
    /**
     * @var \PhpAmqpLib\Connection\AMQPStreamConnection
     */
    public $connection;


    public function __construct($exchangeName, $conf = [], $durable = false, $exchangeType = 'fanout')
    {
        if (empty($conf)) {
            $conf = Config::get('basecrmqueueconfig.rabbitmq');
        }
        $this->queueKey = $conf['info'][$exchangeName]['queueKey'];
        $this->exchange = $conf['info'][$exchangeName]['exchange'];
        $this->queueConf = $conf['server'];
        $this->getRandServer();
        $this->exchangeType = $exchangeType;
        $this->connection($durable);
        $this->tale = new AMQPTable();
    }

    public function connection($durable = false)
    {
        try {
            $this->connection = new AMQPStreamConnection($this->arrCurrentConf['host'], $this->arrCurrentConf['port'],
                $this->arrCurrentConf['user'], $this->arrCurrentConf['pass'],
                $this->arrCurrentConf['vhost']);

            $this->channel = $this->connection->channel();
            $this->channel->exchange_declare($this->exchange, $this->exchangeType, false, $durable, false);
        } catch (\Exception $e) {
            $knownExceptions = [
                AMQPProtocolException::class,
                AMQPProtocolConnectionException::class,
                AMQPProtocolChannelException::class,
                AMQPTimeoutException::class,
                AMQPIOException::class,
                AMQPRuntimeException::class,
                \ErrorException::class, // fwrite on RST-ed closed connection
            ];
            $exceptionName = get_class($e);
            if (!in_array($exceptionName, $knownExceptions)) {
                throw $e;
            }

            $causeBy = $exceptionName . '::' . $e->getMessage();
            $this->tryNum++;
            if ($this->tryNum <= 10) {
                $oldConf = json_encode($this->arrCurrentConf);
                $tryTimes = $this->tryNum;

                $this->getRandServer();
                $newConf = json_encode($this->arrCurrentConf);

                \Log::error(__METHOD__ . " 连接RabbitMq {$oldConf}失败，Cause by: {$causeBy}|尝试第{$tryTimes}次重连，连接至{$newConf}");
                $this->connection($durable);
            } else {
                \Log::error(__METHOD__ . ' 连接RabbitMq ' . json_encode($this->arrCurrentConf) . ' 失败。Cause by: ' . $causeBy);
                throw $e;
            }
        }
    }

    /**
     * 查询MQ队列未消耗数量
     */
    public function getQueueNoConsumeCount($durable = true)
    {
        $res = $this->channel->queue_declare($this->queueKey, false, $durable, false, false);
        $this->close();

        return $res[1];
    }

    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }

    /**
     * 增加一条消息
     * @param $arrContent
     * @param bool $durable
     */
    public function addOne($arrContent, $durable = true)
    {
        if (!$this->routeKey) {
            $this->routeKey = $this->queueKey;
        }
        if (!is_string($arrContent)) {
            $messageBody = json_encode($arrContent);
        } else {
            $messageBody = $arrContent;
        }
        $this->channel->queue_declare($this->queueKey, false, $durable, false, false);
        $this->channel->queue_bind($this->queueKey, $this->exchange, $this->routeKey);

        $message = new AMQPMessage(
            $messageBody,
            array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
        );

        if (!$this->delayTime) {
            $this->channel->basic_publish($message, $this->exchange, $this->routeKey);
        } else {
            $this->setTale();
            $this->setDelayExchange();
            $delayQueue = $this->setDelayQueue();
            $this->channel->basic_publish($message, $this->delayExchange, $delayQueue);
        }
    }

    /**
     * 添加数据到rabbitMq交换机中
     * @param $arrContent
     */
    public function addOneToExchange($arrContent)
    {
        if (!is_string($arrContent)) {
            $messageBody = json_encode($arrContent);
        } else {
            $messageBody = $arrContent;
        }
        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $this->channel->basic_publish($message, $this->exchange, $this->routeKey);
    }

    public function getOne($funcCallback, $queueName = '', $durable = true)
    {
        $queueName = $queueName ? $queueName : $this->queueKey;
        $this->channel->queue_declare($queueName, false, $durable, false, false);
        $this->channel->queue_bind($queueName, $this->exchange);
        $this->channel->basic_consume($queueName, $this->consumerTag, false, false, false, false, $funcCallback);
    }

    public static function Ack($message)
    {
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
    }

    public static function Nack($message)
    {
        $message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag']);
    }

    /**
     * 随机获取服务器
     */
    public function getRandServer()
    {
        if (empty($this->queueConf)) {
            $this->queueConf = Config::get('basecrmqueueconfig.rabbitmq.server');
        }
        shuffle($this->queueConf);
        $this->arrCurrentConf = $this->queueConf[0];
    }

    /**
     * 消息过期时间
     * @param int $delayTime
     * @return $this
     */
    public function delayTime($delayTime = 0)
    {
        $this->delayTime = $delayTime;
        return $this;
    }

    /**
     * 设置绑定关系以及消息存活时间
     */
    private function setTale()
    {
        $this->tale->set('x-dead-letter-exchange', $this->exchange);//****很关键 表示过期后由哪个exchange处理
        $this->tale->set('x-dead-letter-routing-key', $this->routeKey);//****很关键 表示过期后由哪个exchange处理
        $this->tale->set('x-message-ttl', $this->delayTime);  //存活时长
    }

    /**
     * 设置延时消息交换机
     */
    private function setDelayExchange()
    {
        $this->channel->exchange_declare($this->delayExchange, 'direct', false, true, false);
    }

    /**
     * 设置队列以及绑定交换机路由
     */
    private function setDelayQueue()
    {
        $deadQueueName = 'enqueque.' . $this->exchange . '.' . $this->queueKey . '.' . $this->delayTime . '.x.delay';
        $this->channel->queue_declare($deadQueueName, false, true, false, false, false, $this->tale);
        $this->channel->queue_bind($deadQueueName, $this->delayExchange, $deadQueueName);
        return $deadQueueName;
    }

    /**
     * 路由
     * @param string $routeKey
     * @return $this
     */
    public function onRoute($routeKey = '')
    {
        $this->routeKey = $routeKey;
        return $this;
    }

    /**
     * 设置延时消息默认交换机
     * @param string $delayExchange
     * @return $this
     */
    public function onDelayExchange($delayExchange = 'delay_exchange')
    {
        $this->delayExchange = $delayExchange;
        return $this;
    }

}
