<?php declare(strict_types=1);

namespace VitesseCms\Core\Utils;

use VitesseCms\Core\Enum\EnvEnum;

class DebugUtil
{
    private const DEBUG_ENVIRONEMNTS = [EnvEnum::ENVIRONMENT_LOCAL];

    public static function isDev(): bool
    {
        return in_array(getenv(EnvEnum::ENVIRONMENT), self::DEBUG_ENVIRONEMNTS);
    }
}
