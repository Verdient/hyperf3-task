<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task\Handler\Boot;

use Hyperf\Contract\ApplicationInterface;
use Hyperf\Di\ClassLoader;
use Hyperf\Engine\DefaultOption;
use Hyperf\Framework\Event\BeforeMainServerStart;
use Hyperf\Framework\Event\BootApplication;
use Override;
use Swoole\Server;
use Verdient\Hyperf3\Event\Event;
use Verdient\Task\Process\ChildrenProcess;
use Verdient\Task\Process\HandlerInterface;

/**
 * ProcOpen工作进程启动处理程序
 *
 * @author Verdient。
 */
class ProcOpenHandler implements HandlerInterface
{
    /**
     * @param string $basePath 基础路径
     *
     * @author Verdient。
     */
    public function __construct(protected readonly string $basePath) {}

    /**
     * @author Verdient。
     */
    #[Override]
    public function handle(ChildrenProcess $process): void
    {
        $process->clearFileDescriptors();

        ini_set('display_errors', 'on');
        ini_set('display_startup_errors', 'on');
        ini_set('memory_limit', '1G');

        error_reporting(E_ALL);

        date_default_timezone_set('Asia/Shanghai');

        !defined('BASE_PATH') && define('BASE_PATH', $this->basePath);

        !defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', DefaultOption::hookFlags());

        $_SERVER['argv'] = [
            $_SERVER['argv'][0],
            'start'
        ];

        (function () {
            ClassLoader::init();
            $container = require BASE_PATH . '/config/container.php';
            $container->get(ApplicationInterface::class);
            Event::dispatch(new BootApplication);
            Event::dispatch(new BeforeMainServerStart(new Server('127.0.0.1'), []));
        })();
    }
}
