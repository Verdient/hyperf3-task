<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 任务执行器
 * @author Verdient。
 */
#[Attribute(Attribute::TARGET_CLASS)]
class TaskExecutor extends AbstractAnnotation
{
    /**
     * @inheritdoc
     * @author Verdient。
     */
    public function __construct(public int|string $identifier)
    {
    }

    /**
     * @inheritdoc
     * @author Verdient。
     */
    public function collectClass(string $className): void
    {
        TaskExecutorCollector::collectClass($className, $this);
    }
}
