<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task\Annotation;

use Hyperf\Di\MetadataCollector;
use Verdient\Task\TaskInterface;

/**
 * 任务收集器
 *
 * @method static ?Task get(string $key, $default = null)
 * @method static array<class-string<TaskInterface>,Task> list()
 *
 * @author Verdient。
 */
class TaskCollector extends MetadataCollector
{
    /**
     * @inheritdoc
     *
     * @author Verdient。
     */
    protected static array $container = [];

    /**
     * 收集类
     *
     * @param class-string<TaskInterface> $className 类名
     * @param Task $annotation 注解
     *
     * @author Verdient。
     */
    public static function collectClass(string $className, Task $annotation): void
    {
        static::$container[$className] = $annotation;
    }
}
