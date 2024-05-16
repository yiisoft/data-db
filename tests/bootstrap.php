<?php

declare(strict_types=1);

if (getenv('ENVIRONMENT', local_only: true) !== 'ci') {
    $dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
    $dotenv->load();
}

/**
 * @link https://github.com/krakjoe/uopz/issues/172
 */
if (function_exists('uopz_allow_exit')) {
    uopz_allow_exit(true);
}
