<?php

use function Hyperf\Support\env;

return [
    'enable' => (bool) env('TASK_ENABLE', false),
    'tasks' => []
];
