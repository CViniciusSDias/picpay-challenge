<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

if (getenv('APP_RUNTIME') === 'Runtime\\Swoole\\Runtime') {
    $_SERVER['APP_RUNTIME_OPTIONS'] = [
        'settings' => [
            'hook_flags' => SWOOLE_HOOK_ALL & ~SWOOLE_HOOK_FILE & ~SWOOLE_HOOK_STDIO,
        ],
    ];
}

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
