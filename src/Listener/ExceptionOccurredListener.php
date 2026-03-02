<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task\Listener;

use Hyperf\Event\Contract\ListenerInterface;
use Override;
use Verdient\Hyperf3\Event\Event;
use Verdient\Hyperf3\Exception\ExceptionOccurredEvent;
use Verdient\Task\Event\FailedToConsume;
use Verdient\Task\Event\FailedToDispachEvent;
use Verdient\Task\Event\FailedToProduce;
use Verdient\Task\Event\FailedToStart;
use Verdient\Task\Event\FailedToStop;

/**
 * 异常发生监听器
 *
 * @author Verdient。
 */
class ExceptionOccurredListener implements ListenerInterface
{
    /**
     * @author Verdient。
     */
    #[Override]
    public function listen(): array
    {
        return [
            FailedToStop::class,
            FailedToStart::class,
            FailedToConsume::class,
            FailedToProduce::class,
            FailedToDispachEvent::class
        ];
    }

    /**
     * @param FailedToStop|FailedToStart|FailedToConsume|FailedToProduce|FailedToDispachEvent $event 事件
     *
     * @author Verdient。
     */
    #[Override]
    public function process(object $event): void
    {
        Event::dispatch(new ExceptionOccurredEvent($event->throwable));
    }
}
