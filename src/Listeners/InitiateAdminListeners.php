<?php declare(strict_types=1);

namespace VitesseCms\Core\Listeners;

use VitesseCms\Core\Enum\FlashEnum;
use VitesseCms\Core\Enum\RouterEnum;
use VitesseCms\Core\Enum\UrlEnum;
use VitesseCms\Core\Enum\ViewEnum;
use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Core\Listeners\Services\FlashServiceListener;
use VitesseCms\Core\Listeners\Services\RouterServiceListener;
use VitesseCms\Core\Listeners\Services\UrlServiceListener;
use VitesseCms\Core\Listeners\Services\ViewServiceListener;

class InitiateAdminListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $di): void
    {
        $di->eventsManager->attach(ViewEnum::SERVICE_LISTENER, new ViewServiceListener($di->view));
        $di->eventsManager->attach(FlashEnum::SERVICE_LISTENER, new FlashServiceListener($di->flash));
        $di->eventsManager->attach(ViewEnum::SERVICE_LISTENER, new ViewServiceListener($di->view));
        $di->eventsManager->attach(RouterEnum::SERVICE_LISTENER, new RouterServiceListener($di->router));
        $di->eventsManager->attach(UrlEnum::SERVICE_LISTENER, new UrlServiceListener($di->url));
    }
}
