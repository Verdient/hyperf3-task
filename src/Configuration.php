<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task;

use Psr\Log\LoggerInterface;

/**
 * 配置
 *
 * @author Verdient。
 */
class Configuration
{
    /**
     * 最大工作进程数
     *
     * @author Verdient。
     */
    public int $maxWorkersNums = 0;

    /**
     * 单个CPU最大可承载的进程数量
     *
     * @author Verdient。
     */
    public int $maxWorkersNumsPerCPU = 0;

    /**
     * 最大闲置时间
     *
     * @author Verdient。
     */
    public int $maxIdleSeconds = 15;

    /**
     * 标识符
     *
     * @author Verdient。
     */
    public ?string $identifier = null;

    /**
     * 生产日志记录器
     *
     * @author Verdient。
     */
    public ?LoggerInterface $produceLogger = null;

    /**
     * 消费日志记录器
     *
     * @author Verdient。
     */
    public ?LoggerInterface $consumeLogger = null;
}
