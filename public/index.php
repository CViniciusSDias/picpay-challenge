<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

$_SERVER['APP_RUNTIME_OPTIONS'] = [
    'settings' => [
        'hook_flags' => SWOOLE_HOOK_ALL,
    ],
];
return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
