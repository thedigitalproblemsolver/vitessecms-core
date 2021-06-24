<?php declare(strict_types=1);

namespace VitesseCms\Core\Utils;

class DebugUtil
{
    private const DEBUG_ENVIRONEMNTS = ['local'];

    public static function isDev(): bool
    {
        return in_array(getenv('ENVIRONMENT'), self::DEBUG_ENVIRONEMNTS);
    }
}
