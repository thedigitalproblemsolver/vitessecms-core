<?php

declare(strict_types=1);

namespace VitesseCms\Core\Enum;

enum FrontendEnum: string
{
    case LISTENER = 'FrontendListener';
    case BEFORE_EXECUTE_ROUTE = 'FrontendListener:beforeExecuteRoute';
}