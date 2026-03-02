<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task\Annotation;

use Attribute;

/**
 * 标识符
 *
 * @author Verdient。
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Identifier
{
    /**
     * @param string $value 标识符
     *
     * @author Verdient。
     */
    public function __construct(public readonly string $value) {}
}
