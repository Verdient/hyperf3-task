<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task\Handler\Boot;

use Hyperf\Context\Context;
use Hyperf\Di\ReflectionManager;

/**
 * 重置上下文
 *
 * @author Verdient。
 */
trait ResetContext
{
    /**
     * 重置上下文
     *
     * @author Verdient。
     */
    protected function resetContext(): void
    {
        $reflectionClass = ReflectionManager::reflectClass(Context::class);
        $reflectionClass->setStaticPropertyValue('nonCoContext', []);
    }
}
