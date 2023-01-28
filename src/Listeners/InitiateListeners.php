<?php declare(strict_types=1);

namespace VitesseCms\Core\Listeners;

use VitesseCms\Core\Enum\CacheEnum;
use VitesseCms\Core\Enum\FlashEnum;
use VitesseCms\Core\Enum\SecurityEnum;
use VitesseCms\Core\Enum\SessionEnum;
use VitesseCms\Core\Enum\UrlEnum;
use VitesseCms\Core\Enum\ViewEnum;
use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Core\Listeners\Services\CacheServiceListener;
use VitesseCms\Core\Listeners\Services\FlashServiceListener;
use VitesseCms\Core\Listeners\Services\SecurityServiceListener;
use VitesseCms\Core\Listeners\Services\SessionServiceListener;
use VitesseCms\Core\Listeners\Services\UrlServiceListener;
use VitesseCms\Core\Listeners\Services\ViewServiceListener;

class InitiateListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $di): void
    {
        $di->eventsManager->attach(CacheEnum::SERVICE_LISTENER, new CacheServiceListener($di->cache));
        $di->eventsManager->attach(UrlEnum::SERVICE_LISTENER, new UrlServiceListener($di->url));
        $di->eventsManager->attach(ViewEnum::SERVICE_LISTENER, new ViewServiceListener($di->view));
        $di->eventsManager->attach(FlashEnum::SERVICE_LISTENER, new FlashServiceListener($di->flash));
        $di->eventsManager->attach(SecurityEnum::SERVICE_LISTENER, new SecurityServiceListener($di->security));
        $di->eventsManager->attach(SessionEnum::SERVICE_LISTENER, new SessionServiceListener($di->session));
    }
}
