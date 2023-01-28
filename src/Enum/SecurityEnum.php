<?php declare(strict_types=1);

namespace VitesseCms\Core\Enum;

use VitesseCms\Core\AbstractEnum;

class SecurityEnum extends AbstractEnum
{
    public const ATTACH_SERVICE_LISTENER = 'securityService:attach';
    public const SERVICE_LISTENER = 'securityService';
}
