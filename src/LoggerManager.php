<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Task;

use Hyperf\Logger\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Hyperf\Stringable\Str;
use Psr\Log\LoggerInterface;
use Verdient\Hyperf3\Task\Logger\ConsumeLoggerInterface;
use Verdient\Hyperf3\Task\Logger\DispatchLoggerInterface;
use Verdient\Hyperf3\Task\Logger\ProduceLoggerInterface;

use function Hyperf\Support\make;

/**
 * 记录器管理器
 *
 * @author Verdient。
 */
class LoggerManager
{
    /**
     * 规则集合
     *
     * @author Verdient。
     */
    protected static array $rules = [
        'App\Tasks\\' => ['Task'],
        'App\Task\\' => ['Task'],
    ];

    /**
     * 注册规则
     *
     * @param string $namespace 命名空间
     * @param string[] $suffixes 后缀集合
     *
     * @author Verdient。
     */
    public static function registerRule(string $namespace, array $suffixes): void
    {
        if (!str_ends_with($namespace, '\\')) {
            $namespace .= '\\';
        }

        if (isset(static::$rules[$namespace])) {
            $suffixes = array_values(array_unique(array_merge(static::$rules[$namespace], $suffixes)));
        }

        uasort($suffixes, function ($a, $b) {
            return strlen($b) <=> strlen($a);
        });

        static::$rules[$namespace] = $suffixes;

        uksort(static::$rules, function ($a, $b) {
            return strlen($b) <=> strlen($a);
        });
    }

    /**
     * 简化名称
     *
     * @param string $class 类名
     *
     * @author Verdient。
     */
    protected static function simplifyName(string $class): string
    {
        foreach (static::$rules as $namespace => $suffixes) {
            $length = strlen($namespace);

            if (strlen($class) === $length || !str_starts_with($class, $namespace)) {
                continue;
            }

            $class = substr($class, $length);

            foreach ($suffixes as $suffix) {
                $length = strlen($suffix);
                if (strlen($class) > $length && str_ends_with($class, $suffix)) {
                    $class = substr($class, 0, -$length);
                }
            }
        }

        return $class;
    }

    /**
     * 生成记录器配置
     *
     * @param string $name 名称
     * @param string $class 类名
     *
     * @author Verdient。
     */
    public static function config(string $name, string $class): array
    {
        $path = match ($class) {
            ProduceLoggerInterface::class => 'produce',
            ConsumeLoggerInterface::class => 'consume',
            DispatchLoggerInterface::class => 'dispatch',
        };

        $nameParts = array_map([Str::class, 'kebab'], explode('\\', static::simplifyName($name)));

        $filename = BASE_PATH . '/runtime/logs/task/' . implode('/', $nameParts) . '/' . $path . '/.log';

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

    /**
     * 创建记录器
     *
     * @param string $name 名称
     * @param string $class 类名
     *
     * @author Verdient。
     */
    public static function create(string $name, string $class): LoggerInterface
    {
        $config = static::config($name, $class);

        $handler = make($config['handler']['class'], $config['handler']['constructor'] ?? []);

        if (isset($config['formatter'])) {
            $formatter = make($config['formatter']['class'], $config['formatter']['constructor']);

            $handler->setFormatter($formatter);
        }

        return new Logger($name, [$handler]);
    }
}
