<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task\Listener;

use Hyperf\Coordinator\Constants;
use Hyperf\Coordinator\CoordinatorManager;
use Hyperf\Event\Contract\ListenerInterface;
use Override;
use Verdient\Task\Event\WorkerCreated;
use Verdient\Task\Event\WorkerRemoved;
use Verdient\Task\Event\WorkerStopped;

/**
 * 工作进程停止监听器
 *
 * @author Verdient。
 */
class WorkerStoppedListener implements ListenerInterface
{
    /**
     * @author Verdient。
     */
    #[Override]
    public function listen(): array
    {
        return [
            WorkerStopped::class
        ];
    }

    /**
     * @param WorkerCreated|WorkerRemoved $event 事件
     *
     * @author Verdient。
     */
    #[Override]
    public function process(object $event): void
    {
        CoordinatorManager::until(Constants::WORKER_EXIT)->resume();
    }
}
