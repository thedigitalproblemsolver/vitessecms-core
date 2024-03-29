<?php declare(strict_types=1);

namespace VitesseCms\Core\Enum;

use VitesseCms\Core\AbstractEnum;

class ViewEnum extends AbstractEnum
{
    public const ATTACH_SERVICE_LISTENER = 'viewService:attach';
    public const SET_FRONTEND_VARS_SERVICE_LISTENER = 'viewService:setFrontendVars';
    public const SERVICE_LISTENER = 'viewService';
}
