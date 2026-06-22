<?php

/*
 * Silence the PHP 8.5 deprecation emitted by Testbench's bundled MySQL/MariaDB
 * configuration, which still references the deprecated `PDO::MYSQL_ATTR_SSL_CA`
 * constant:
 *
 *   Constant PDO::MYSQL_ATTR_SSL_CA is deprecated since 8.5, use Pdo\Mysql::ATTR_SSL_CA
 *
 * Cachet targets Laravel 11, so we're pinned to the Testbench 9 line which
 * mirrors Laravel 11's stock config; we can't bump to a fixed Testbench release
 * without moving the whole package to Laravel 13. This is loaded early via
 * Composer's dev autoloader (so it covers both the test suite and `testbench
 * serve`) and only swallows this one specific deprecation, deferring every
 * other error to the handler that was already in place.
 */

$previous = set_error_handler(static function (int $level, string $message, string $file = '', int $line = 0) use (&$previous): bool {
    if ($level === E_DEPRECATED && str_contains($message, 'PDO::MYSQL_ATTR_SSL_CA')) {
        return true;
    }

    return $previous !== null ? (bool) $previous($level, $message, $file, $line) : false;
});
