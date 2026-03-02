<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;
use Override;
use TypeError;
use Verdient\Task\TaskInterface;

/**
 * 任务
 *
 * @author Verdient。
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Task extends AbstractAnnotation
{
    /**
     * @author Verdient。
     */
    #[Override]
    public function collectClass(string $className): void
    {
        if (!is_subclass_of($className, TaskInterface::class)) {
            throw new TypeError('The class ' . $className . ' with #[Task] must implement ' . TaskInterface::class . '.');
        }
        TaskCollector::collectClass($className, $this);
    }
}
