<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task\Handler\Boot;

use Hyperf\Framework\Event\BootApplication;
use Override;
use Verdient\Hyperf3\Event\Event;
use Verdient\Task\Process\ChildrenProcess;
use Verdient\Task\Process\HandlerInterface;

/**
 * Fork工作进程启动处理程序
 *
 * @author Verdient。
 */
class ForkHandler implements HandlerInterface
{
    use CloseAmqpConnections;
    use ResetContext;

    /**
     * @author Verdient。
     */
    #[Override]
    public function handle(ChildrenProcess $process): void
    {
        $process->clearFileDescriptors();

        require BASE_PATH . '/config/container.php';

        $this->closeAmqpConnections();
        $this->resetContext();

        Event::dispatch(new BootApplication);
    }
}
