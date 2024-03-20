<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task;

use Verdient\Hyperf3\Struct\HasReschedule;
use Verdient\Hyperf3\Struct\Result as StructResult;

/**
 * 结果
 * @author Verdient。
 */
class Result extends StructResult
{
    use HasReschedule;
}
