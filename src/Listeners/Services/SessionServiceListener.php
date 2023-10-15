<?php declare(strict_types=1);

namespace VitesseCms\Core\Listeners\Services;

use Phalcon\Events\Event;
use Phalcon\Session\Manager as Session;

class SessionServiceListener
{
    private Session $sessionService;

    public function __construct(Session $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function attach( Event $event): Session
    {
        return $this->sessionService;
    }
}
