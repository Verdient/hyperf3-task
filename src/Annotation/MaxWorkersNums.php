<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task\Annotation;

use Attribute;

/**
 * 最大进程数量
 *
 * @author Verdient。
 */
#[Attribute(Attribute::TARGET_CLASS)]
class MaxWorkersNums
{
    /**
     * @param int $value 最大进程数量
     *
     * @author Verdient。
     */
    public function __construct(public readonly int $value) {}
}
