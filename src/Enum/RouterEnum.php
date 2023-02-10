<?php declare(strict_types=1);

namespace VitesseCms\Core\Enum;

use VitesseCms\Core\AbstractEnum;

class RouterEnum extends AbstractEnum
{
    public const ATTACH_SERVICE_LISTENER = 'routerService:attach';
    public const SERVICE_LISTENER = 'routerService';
}
