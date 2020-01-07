<?php


namespace App\Base\Queue\Rabbitmq;


class FakeRabbitmqClient extends RabbitmqClient
{
    protected $queue;

    public function __construct($exchangeName, $conf = [], $durable = false, $exchangeType = 'fanout')
    {
        $this->queue = new \SplQueue();
        // parent::__construct($exchangeName, $conf, $durable, $exchangeType);
    }

    public function connection($durable = false)
    {
        return true;
    }

    public function getQueueNoConsumeCount($durable = true)
    {
        return parent::getQueueNoConsumeCount($durable); // TODO: Change the autogenerated stub
    }

    public function close()
    {
        parent::close(); // TODO: Change the autogenerated stub
    }

    public function addOne($arrContent, $durable = true)
    {
        $this->queue->enqueue($arrContent);
    }

    public function addOneToExchange($arrContent)
    {
        parent::addOneToExchange($arrContent); // TODO: Change the autogenerated stub
    }

    public function getOne($funcCallback, $queueName = '', $durable = true)
    {
        return $this->queue->dequeue();
    }

    public static function Ack($message)
    {
        parent::Ack($message); // TODO: Change the autogenerated stub
    }

    public static function Nack($message)
    {
        parent::Nack($message); // TODO: Change the autogenerated stub
    }

    public function getRandServer()
    {
        parent::getRandServer(); // TODO: Change the autogenerated stub
    }

    public function delayTime($delayTime = 0)
    {
        return parent::delayTime($delayTime); // TODO: Change the autogenerated stub
    }

    public function onRoute($routeKey = '')
    {
        return parent::onRoute($routeKey); // TODO: Change the autogenerated stub
    }

    public function onDelayExchange($delayExchange = 'delay_exchange')
    {
        return parent::onDelayExchange($delayExchange); // TODO: Change the autogenerated stub
    }

}