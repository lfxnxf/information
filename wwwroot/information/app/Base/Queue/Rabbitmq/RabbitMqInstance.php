<?php
/**
 * Created by PhpStorm.
 * User: ThinkPad
 * Date: 2019/7/29
 * Time: 14:56
 */

namespace App\Base\Queue\Rabbitmq;

/**
 * 单例获取rabbitMq对象
 * Class Instance
 * @package App\Base\Queue\Rabbitmq
 */
class RabbitMqInstance
{
    /**
     * @var []RabbitmqClient
     */
    private static $instance = null;

    /**
     * @param string $exchangeName
     * @param array $conf
     * @param bool $durable
     * @param string $exchangeType
     * @return RabbitmqClient
     */
    public static function getInstance($exchangeName = '', $conf = [], $durable = false, $exchangeType = 'fanout')
    {
        $index = $exchangeName;
        if (!isset(self::$instance[$index]) || empty(self::$instance[$index])) {
            self::$instance[$index] = new RabbitmqClient($exchangeName, $conf, $durable, $exchangeType);
        }
        return self::$instance[$index];
    }

    /**
     * @param $exchangeName
     * @param RabbitmqClient $instance
     */
    public static function setInstance($exchangeName, RabbitmqClient $instance)
    {
        self::$instance[$exchangeName] = $instance;
    }

}