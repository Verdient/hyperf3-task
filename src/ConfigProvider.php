<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Verdient\Hyperf3\Task\Annotation\TaskCollector;
use Verdient\Hyperf3\Task\Listener\BeforeMainServerStartListener;
use Verdient\Hyperf3\Task\Listener\WorkerStoppedListener;
use Verdient\Hyperf3\Task\Logger\ConsumeLoggerInterface;
use Verdient\Hyperf3\Task\Logger\DispatchLoggerInterface;
use Verdient\Hyperf3\Task\Logger\ProduceLoggerInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for task.',
                    'source' => dirname(__DIR__) . '/publish/task.php',
                    'destination' => constant('BASE_PATH') . '/config/autoload/task.php',
                ]
            ],
            'listeners' => [
                BeforeMainServerStartListener::class => 99,
                WorkerStoppedListener::class
            ],
            'annotations' => [
                'scan' => [
                    'collectors' => [
                        TaskCollector::class
                    ]
                ],
            ],
            'logger' => [
                DispatchLoggerInterface::class => fn($name) => LoggerManager::config($name, DispatchLoggerInterface::class),
                ProduceLoggerInterface::class => fn($name) => LoggerManager::config($name, ProduceLoggerInterface::class),
                ConsumeLoggerInterface::class => fn($name) => LoggerManager::config($name, ConsumeLoggerInterface::class),
                MasterProcess::class => function () {

                    $filename = BASE_PATH . '/runtime/logs/process/task/master/.log';

                    return [
                        'handler' => [
                            'class' => RotatingFileHandler::class,
                            'constructor' => [
                                'filename' => $filename,
                                'filenameFormat' => '{date}'
                            ],
                        ],
                        'formatter' => [
                            'class' => LineFormatter::class,
                            'constructor' => [
                                'format' => "%datetime% [%level_name%] %message%\n",
                                'dateFormat' => 'Y-m-d H:i:s',
                                'allowInlineLineBreaks' => true,
                            ],
                        ]
                    ];
                }
            ],
        ];
    }
}
