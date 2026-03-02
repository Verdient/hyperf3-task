<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task\Annotation;

use Attribute;

/**
 * 每个CPU最多启动的进程数量
 *
 * @author Verdient。
 */
#[Attribute(Attribute::TARGET_CLASS)]
class MaxWorkersNumsPerCPU
{
    /**
     * @param int $value 最大进程数量
     *
     * @author Verdient。
     */
    public function __construct(public readonly int $value) {}
}
