<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'annotations' => [
                'scan' => [
                    'collectors' => [
                        TaskExecutorCollector::class
                    ]
                ]
            ],
        ];
    }
}
