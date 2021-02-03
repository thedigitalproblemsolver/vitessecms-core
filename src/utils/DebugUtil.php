<?php declare(strict_types=1);

namespace VitesseCms\Core\Utils;

class DebugUtil
{
    public static function dump($input = null): void
    {
        if (self::isDev()) :
            echo '<pre>';
            var_dump($input);
            die();
        endif;
    }

    public static function isDev(): bool
    {
        return
            $_SERVER['REMOTE_ADDR'] === '145.53.211.29'
            || $_SERVER['REMOTE_ADDR'] === '77.169.11.226'
            || self::isDocker($_SERVER['SERVER_ADDR'])
        ;
    }

    public static function isDocker(string $ipAddress): bool
    {
        return $ipAddress === '172.17.0.2'
            || $ipAddress === '192.167.0.33'
        ;
    }
}
