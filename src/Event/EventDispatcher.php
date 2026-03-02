<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task\Event;

use Hyperf\Context\ApplicationContext;
use Override;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * 事件调度器
 *
 * @author Verdient。
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @author Verdient。
     */
    #[Override]
    public function dispatch(object $event)
    {
        return ApplicationContext::getContainer()
            ->get(EventDispatcherInterface::class)
            ->dispatch($event);
    }
}
