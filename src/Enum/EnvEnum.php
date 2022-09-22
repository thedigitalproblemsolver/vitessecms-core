<?php declare(strict_types=1);

namespace VitesseCms\Core\Enum;

use VitesseCms\Core\AbstractEnum;

class EnvEnum extends AbstractEnum
{
    public const CACHE_LIFE_TIME = 'CACHE_LIFE_TIME';
    public const ENVIRONMENT = 'ENVIRONMENT';
    public const ENVIRONMENT_LOCAL = 'local';
    public const ENVIRONMENT_PRODUCTION = 'production';
}
