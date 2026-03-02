<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task\Listener;

use Hyperf\Event\Contract\ListenerInterface;
use Override;
use Psr\Log\LoggerInterface;
use Verdient\Hyperf3\Task\Logger\DispatchLoggerInterface;
use Verdient\Hyperf3\Task\LoggerManager;
use Verdient\Task\Event\WorkerCreated;
use Verdient\Task\Event\WorkerRemoved;

/**
 * 工作进程调度监听器
 *
 * @author Verdient。
 */
class WorkersDispatchListener implements ListenerInterface
{
    /**
     * 缓存的日志记录器
     *
     * @author Verdient。
     */
    protected array $loggers = [];

    /**
     * @author Verdient。
     */
    #[Override]
    public function listen(): array
    {
        return [
            WorkerCreated::class,
            WorkerRemoved::class,
        ];
    }

    /**
     * 获取记录器
     *
     * @param WorkerCreated|WorkerRemoved $event 事件
     *
     * @author Verdient。
     */
    protected function getLogger(WorkerCreated|WorkerRemoved $event): LoggerInterface
    {
        $class = $event->task::class;

        if (!isset($this->loggers[$class])) {
            $this->loggers[$class] = LoggerManager::create($class, DispatchLoggerInterface::class);
        }

        return $this->loggers[$class];
    }

    /**
     * @param WorkerCreated|WorkerRemoved $event 事件
     *
     * @author Verdient。
     */
    #[Override]
    public function process(object $event): void
    {
        $logger = $this->getLogger($event);

        if ($event instanceof WorkerCreated) {
            $logger->info('创建工作进程 ' . $event->pid . '，当前进程总数: ' . $event->current . '; 空闲: ' . $event->idle . '; 容量: ' . $event->max);
        } else if ($event instanceof WorkerRemoved) {
            $logger->info('移除工作进程 ' . $event->pid . '，当前进程总数: ' . $event->current . '; 空闲: ' . $event->idle);
        }
    }
}
