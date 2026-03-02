<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task\Handler;

use Override;
use Verdient\Hyperf3\Logger\Utils;
use Verdient\Hyperf3\Task\Configuration;
use Verdient\Hyperf3\Task\Event\EventDispatcher;
use Verdient\Hyperf3\Task\Logger\ConsumeLoggerInterface;
use Verdient\Hyperf3\Task\Logger\ProduceLoggerInterface;
use Verdient\Task\Dispatcher\LocalDispatcher;
use Verdient\Task\Dispatcher\WorkersDispatcher;
use Verdient\Task\Process\ChildrenProcess;
use Verdient\Task\Process\HandlerInterface;
use Verdient\Task\ProcessDriver\ProcessDriverInterface;
use Verdient\Task\TaskInterface;

use function Hyperf\Config\config;

/**
 * 调度器处理程序
 *
 * @author Verdient。
 */
class DispatcherHandler implements HandlerInterface
{
    /**
     * 标识符
     *
     * @author Verdient。
     */
    protected string $identifier;

    /**
     * 进程名称
     *
     * @author Verdient。
     */
    public readonly string $processName;

    /**
     * 工作进程名称
     *
     * @author Verdient。
     */
    protected readonly string $workerProcessName;

    /**
     * @param TaskInterface $task 任务
     * @param Configuration $configuration 配置
     * @param int $masterPid 主进程编号
     *
     * @author Verdient。
     */
    public function __construct(
        protected TaskInterface $task,
        protected Configuration $configuration,
        protected int $masterPid,
        protected ProcessDriverInterface $processDriver,
        protected HandlerInterface $bootHandler
    ) {
        $this->identifier = $this->configuration->identifier ?: str_replace('\\', '-', $task::class);

        $prefix = config('app_name') . '.Task.' . $this->identifier;

        if ($configuration->maxWorkersNums === 1) {
            $this->processName = $prefix . '.Process';
        } else {
            $this->processName = $prefix  . '.Dispatcher';
        }

        $this->workerProcessName = $prefix . '.Worker';
    }

    /**
     * @author Verdient。
     */
    #[Override]
    public function handle(ChildrenProcess $process): void
    {
        if ($this->configuration->maxWorkersNums === 1) {
            $dispatcher = LocalDispatcher::create($this->task);
        } else {
            $dispatcher = WorkersDispatcher::create($this->task)
                ->setName($this->workerProcessName)
                ->setMaxWorkersNums($this->configuration->maxWorkersNums)
                ->setMaxWorkersNumsPerCPU($this->configuration->maxWorkersNumsPerCPU)
                ->setMaxIdleSeconds($this->configuration->maxIdleSeconds)
                ->setProcessDriver($this->processDriver)
                ->setBootHandler($this->bootHandler);
        }

        $taskClass = ($this->task)::class;

        $dispatcher->enableCoroutine();
        $dispatcher->setMasterPid($this->masterPid);
        $dispatcher->setIdentifier($this->identifier);
        $dispatcher->setProduceLogger($this->configuration->produceLogger ?: Utils::createLogger([$taskClass => ProduceLoggerInterface::class]));
        $dispatcher->setConsumeLogger($this->configuration->consumeLogger ?: Utils::createLogger([$taskClass => ConsumeLoggerInterface::class]));
        $dispatcher->setEventDispatcher(new EventDispatcher);

        $dispatcher->dispatch();
    }
}
