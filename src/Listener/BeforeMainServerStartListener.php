<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task\Listener;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BeforeMainServerStart;
use Hyperf\Process\ProcessManager;
use Hyperf\Server\Event\MainCoroutineServerStart;
use Override;
use Psr\Container\ContainerInterface;
use Verdient\Hyperf3\Task\MasterProcess;
use Verdient\Hyperf3\Task\TaskManager;

/**
 * 主进程启动前监听器
 *
 * @author Verdient。
 */
class BeforeMainServerStartListener implements ListenerInterface
{
    public function __construct(protected ContainerInterface $container) {}

    /**
     * @author Verdient。
     */
    #[Override]
    public function listen(): array
    {
        return [
            BeforeMainServerStart::class,
            MainCoroutineServerStart::class,
        ];
    }

    /**
     * @param BeforeMainServerStart|MainCoroutineServerStart $event 事件
     *
     * @author Verdient。
     */
    #[Override]
    public function process(object $event): void
    {
        /** @var ConfigInterface */
        $config = $this
            ->container
            ->get(ConfigInterface::class);

        if (!$config->get('task.enable', false)) {
            return;
        }

        if (TaskManager::all()->isEmpty()) {
            return;
        }

        ProcessManager::register(new MasterProcess($this->container));
    }
}
