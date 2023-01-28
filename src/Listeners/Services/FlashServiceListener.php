<?php declare(strict_types=1);

namespace VitesseCms\Core\Listeners\Services;

use Phalcon\Events\Event;
use VitesseCms\Core\Services\FlashService;

class FlashServiceListener
{
    private FlashService $flashService;

    public function __construct(FlashService $flashService)
    {
        $this->flashService = $flashService;
    }

    public function attach( Event $event): FlashService
    {
        return $this->flashService;
    }
}
