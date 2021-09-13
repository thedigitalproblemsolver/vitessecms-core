<?php declare(strict_types=1);

namespace VitesseCms\Core\Listeners;

use VitesseCms\Core\Enum\CacheEnum;
use VitesseCms\Core\Interfaces\InitiateListenersInterface;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Core\Listeners\Services\CacheServiceListener;

class InitiateListeners implements InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $di): void
    {
        $di->eventsManager->attach(CacheEnum::ATTACH_SERVICE_LISTENER, new CacheServiceListener($di->cache));
    }
}
