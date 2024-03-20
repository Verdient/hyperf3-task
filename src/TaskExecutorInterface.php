<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task;

use Verdient\Hyperf3\Logger\HasLoggerInterface;

/**
 * 任务执行器接口
 * @author Verdient。
 */
interface TaskExecutorInterface extends HasLoggerInterface
{
    /**
     * 执行
     * @param array $params 参数
     * @author Verdient。
     */
    public function execute(array $params = []): Result;
}
