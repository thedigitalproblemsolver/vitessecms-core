<?php declare(strict_types=1);

namespace VitesseCms\Core\Listeners;

use VitesseCms\Core\Enum\FlashEnum;
use VitesseCms\Core\Enum\ViewEnum;
use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Core\Services\FlashService;

class InitiateAdminListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $di): void
    {
        $di->eventsManager->attach(ViewEnum::ATTACH_SERVICE_LISTENER, new ViewServiceListener($di->view));
        $di->eventsManager->attach(FlashEnum::ATTACH_SERVICE_LISTENER, new FlashServiceListener($di->flash));
    }
}
