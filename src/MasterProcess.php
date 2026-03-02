<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task;

use Hyperf\Contract\ProcessInterface;
use Hyperf\Process\AbstractProcess;
use Override;
use Swoole\Process as SwooleProcess;
use Verdient\Hyperf3\Logger\HasLogger;
use Verdient\Hyperf3\Task\Handler\Boot\ForkHandler;
use Verdient\Hyperf3\Task\Handler\Boot\ProcOpenHandler;
use Verdient\Hyperf3\Task\Handler\Boot\SwooleHandler;
use Verdient\Hyperf3\Task\Handler\DispatcherHandler;
use Verdient\Task\Process\CompositeHandler;
use Verdient\Task\Process\HandlerInterface;
use Verdient\Task\Process\Process;
use Verdient\Task\ProcessDriver\ForkDriver;
use Verdient\Task\ProcessDriver\ProcessDriverInterface;
use Verdient\Task\ProcessDriver\ProcOpenDriver;
use Verdient\Task\ProcessDriver\SwooleDriver;

use function Hyperf\Support\make;

/**
 * 主进程
 *
 * @author Verdient。
 */
class MasterProcess extends AbstractProcess
{
    use HasLogger;

    /**
     * @author Verdient。
     */
    public string $name = 'Task-Master';

    /**
     * @author Verdient。
     */
    public bool $enableCoroutine = false;

    /**
     * @author Verdient。
     */
    #[Override]
    public function handle(): void
    {
        $masterPid = getmypid();

        $processDriver = make(SwooleDriver::class);

        // $processDriver = make(\Verdient\Task\ProcessDriver\ProcOpenDriver::class);

        $bootHandler = $this->getBootHandler($processDriver);

        $processes = [];

        foreach (TaskManager::all() as $task => $configuration) {

            $dispatcherHandler = make(DispatcherHandler::class, [
                $task,
                $configuration,
                $masterPid,
                $processDriver,
                $bootHandler
            ]);

            $process = (new Process(
                new CompositeHandler([
                    $bootHandler,
                    $dispatcherHandler
                ]),
                $processDriver
            ))
                ->name($dispatcherHandler->processName)
                ->daemonize();

            $processes[$dispatcherHandler->processName] = $process;
        }

        pcntl_signal(SIGINT, fn($signo) => $this->handleSingal($signo, $processes));

        pcntl_signal(SIGTERM, fn($signo) => $this->handleSingal($signo, $processes));

        pcntl_signal(SIGCHLD, function () {
            SwooleProcess::wait();
        });

        $isBooted = false;

        while (true) {
            foreach ($processes as $processName => $process) {
                if (!$process->isRunning()) {
                    if ($isBooted) {
                        $this->logger()->warning($processName . ' exited abnormally, try to restart.');
                    }
                    $process->start();
                    $this->logger()->info($processName . ' started.');
                }
            }

            $isBooted = true;

            sleep(1);
        }
    }

    /**
     * 处理信号
     *
     * @param int $signal 信号
     * @param Process[] $processes 进程集合
     *
     * @author Verdient。
     */
    protected function handleSingal(int $signo, array $processes): void
    {
        foreach ($processes as $processName => $process) {
            $process->kill();
            $this->logger()->info($processName . ' stopped.');
        }

        pcntl_signal($signo, SIG_DFL);

        posix_kill(getmypid(), $signo);
    }

    /**
     * 获取进程启动处理程序
     *
     * @param ProcessDriverInterface $processDriver 进程驱动
     *
     * @author Verdient。
     */
    protected function getBootHandler(ProcessDriverInterface $processDriver): ?HandlerInterface
    {
        if ($processDriver instanceof SwooleDriver) {
            return make(SwooleHandler::class);
        }

        if ($processDriver instanceof ForkDriver) {
            return make(ForkHandler::class);
        }

        if ($processDriver instanceof ProcOpenDriver) {
            return make(ProcOpenHandler::class, [BASE_PATH]);
        }

        return null;
    }

    /**
     * 创建默认的记录器的组名集合
     *
     * @return array<int|string,string>
     * @author Verdient。
     */
    protected function groupsForCreateDefaultLogger(): array
    {
        return [static::class, static::class => ProcessInterface::class];
    }
}
