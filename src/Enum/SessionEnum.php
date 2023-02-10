<?php declare(strict_types=1);

namespace VitesseCms\Core\Enum;

use VitesseCms\Core\AbstractEnum;

class SessionEnum extends AbstractEnum
{
    public const ATTACH_SERVICE_LISTENER = 'sessionService:attach';
    public const SERVICE_LISTENER = 'sessionService';
}
