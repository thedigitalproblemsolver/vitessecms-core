<?php declare(strict_types=1);

namespace VitesseCms\Core\Listeners\Services;

use Phalcon\Events\Event;
use VitesseCms\Core\Services\RouterService;

class RouterServiceListener
{
    private RouterService $routerService;

    public function __construct(RouterService $routerService)
    {
        $this->routerService = $routerService;
    }

    public function attach( Event $event): RouterService
    {
        return $this->routerService;
    }
}
