<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task\Handler\Boot;

use Hyperf\Context\ApplicationContext;
use ReflectionClass;

/**
 * 刷新AMQP连接
 *
 * @author Verdient。
 */
trait CloseAmqpConnections
{
    /**
     * 关闭AMQP连接
     *
     * @author Verdient。
     */
    protected function closeAmqpConnections(): void
    {
        if (!class_exists('Hyperf\Amqp\ConnectionFactory')) {
            return;
        }

        if (!ApplicationContext::hasContainer()) {
            return;
        }

        $factory = ApplicationContext::getContainer()->get('Hyperf\Amqp\ConnectionFactory');

        $reflectionClass = new ReflectionClass($factory);

        $reflectionProperty = $reflectionClass->getProperty('connections');

        $connections = $reflectionProperty->getValue($factory);

        $reflectionProperty->setValue($factory, []);

        foreach ($connections as $connections2) {
            foreach ($connections2 as $connection) {
                $reflectionClass = new ReflectionClass($connection);
                $reflectionProperty = $reflectionClass->getProperty('exited');
                $reflectionProperty->setValue($connection, true);
                $connection->close();
            }
        }
    }
}
