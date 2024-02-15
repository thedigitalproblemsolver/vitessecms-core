<?php
declare(strict_types=1);

namespace VitesseCms\Core\Listeners;

use VitesseCms\Core\Enum\CacheEnum;
use VitesseCms\Core\Enum\FlashEnum;
use VitesseCms\Core\Enum\RouterEnum;
use VitesseCms\Core\Enum\SecurityEnum;
use VitesseCms\Core\Enum\SessionEnum;
use VitesseCms\Core\Enum\UrlEnum;
use VitesseCms\Core\Enum\ViewEnum;
use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Core\Listeners\Services\CacheServiceListener;
use VitesseCms\Core\Listeners\Services\FlashServiceListener;
use VitesseCms\Core\Listeners\Services\RouterServiceListener;
use VitesseCms\Core\Listeners\Services\SecurityServiceListener;
use VitesseCms\Core\Listeners\Services\SessionServiceListener;
use VitesseCms\Core\Listeners\Services\UrlServiceListener;
use VitesseCms\Core\Listeners\Services\ViewServiceListener;

class InitiateListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $injectable): void
    {
        $injectable->eventsManager->attach(CacheEnum::SERVICE_LISTENER, new CacheServiceListener($injectable->cache));
        $injectable->eventsManager->attach(UrlEnum::SERVICE_LISTENER, new UrlServiceListener($injectable->url));
        $injectable->eventsManager->attach(ViewEnum::SERVICE_LISTENER, new ViewServiceListener($injectable->view));
        $injectable->eventsManager->attach(FlashEnum::SERVICE_LISTENER, new FlashServiceListener($injectable->flash));
        $injectable->eventsManager->attach(
            SecurityEnum::SERVICE_LISTENER,
            new SecurityServiceListener($injectable->security)
        );
        $injectable->eventsManager->attach(
            SessionEnum::SERVICE_LISTENER,
            new SessionServiceListener($injectable->session)
        );
        $injectable->eventsManager->attach(
            RouterEnum::SERVICE_LISTENER,
            new RouterServiceListener($injectable->router)
        );
    }
}
