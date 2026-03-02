<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task\Annotation;

use Attribute;

/**
 * 最大空闲时间（秒）
 *
 * @author Verdient。
 */
#[Attribute(Attribute::TARGET_CLASS)]
class MaxIdleSeconds
{
    /**
     * @param int $value 秒数
     *
     * @author Verdient。
     */
    public function __construct(public readonly int $value) {}
}
