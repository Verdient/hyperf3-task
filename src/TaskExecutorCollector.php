<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task;

use Hyperf\Di\MetadataCollector;

use function Hyperf\Support\make;

/**
 * 任务执行器收集器
 * @author Verdient。
 */
class TaskExecutorCollector extends MetadataCollector
{
    /**
     * 容器
     * @author Verdient。
     */
    protected static array $container = [];

    /**
     * 收集类
     * @param string $className 类的名称
     * @param TaskExecutor $annotation 注解
     * @author Verdient。
     */
    public static function collectClass($className, TaskExecutor $annotation): void
    {
        static::$container[$annotation->identifier] = $className;
    }

    /**
     * @inheritdoc
     * @author Verdient。
     */
    public static function clear(?string $key = null): void
    {
        if ($key) {
            foreach (static::$container as $identifier => $className) {
                if ($className === $key) {
                    unset(static::$container[$identifier]);
                    break;
                }
            }
        } else {
            static::$container = [];
        }
    }

    /**
     * 获取实例
     * @param string|int $type 类型
     * @return TaskExecutorInterface|false
     * @author Verdient。
     */
    public static function instance($type): TaskExecutorInterface|false
    {
        if (!$class = static::get((string) $type)) {
            return false;
        }
        return make($class);
    }
}
