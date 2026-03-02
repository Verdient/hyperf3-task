<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task;

use Ds\Map;
use Hyperf\Di\ReflectionManager;
use Verdient\Hyperf3\Task\Annotation\Identifier;
use Verdient\Hyperf3\Task\Annotation\MaxIdleSeconds;
use Verdient\Hyperf3\Task\Annotation\MaxWorkersNums;
use Verdient\Hyperf3\Task\Annotation\MaxWorkersNumsPerCPU;
use Verdient\Hyperf3\Task\Annotation\TaskCollector;
use Verdient\Task\TaskInterface;

use function Hyperf\Support\make;

/**
 * 任务管理器
 *
 * @author Verdient。
 */
class TaskManager
{
    /**
     * 任务集合
     *
     * @author Verdient。
     */
    protected static ?Map $tasks = null;

    /**
     * 初始化任务
     *
     * @author Verdient。
     */
    protected static function initTasks(): void
    {
        if (static::$tasks === null) {
            static::$tasks = new Map;

            foreach (array_keys(TaskCollector::list()) as $class) {
                static::$tasks->offsetSet(make($class), static::parse($class));
            }
        }
    }

    /**
     * 解析任务
     *
     * @param string $className 类名
     *
     * @author Verdient。
     */
    public static function parse(string $className): Configuration
    {
        $reflectionClass = ReflectionManager::reflectClass($className);

        $result = new Configuration;

        foreach ($reflectionClass->getAttributes() as $attribute) {
            $attributeInstance = $attribute->newInstance();

            if ($attributeInstance instanceof MaxWorkersNums) {
                $result->maxWorkersNums = $attributeInstance->value;
            } else if ($attributeInstance instanceof MaxWorkersNumsPerCPU) {
                $result->maxWorkersNumsPerCPU = $attributeInstance->value;
            } else if ($attributeInstance instanceof MaxIdleSeconds) {
                $result->maxIdleSeconds = $attributeInstance->value;
            } else if ($attributeInstance instanceof Identifier) {
                $result->identifier = $attributeInstance->value;
            }
        }

        return $result;
    }

    /**
     * 添加任务
     *
     * @param TaskInterface $task 任务
     * @param Configuration $configuration 配置
     *
     * @author Verdient。
     */
    public static function add(
        TaskInterface $task,
        Configuration $configuration
    ): void {
        static::initTasks();

        static::$tasks->offsetSet($task, $configuration);
    }

    /**
     * 获取任务集合
     *
     * @return Map<TaskInterface,Configuration>
     * @author Verdient。
     */
    public static function all(): Map
    {
        static::initTasks();

        return static::$tasks;
    }
}
