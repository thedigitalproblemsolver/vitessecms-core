<?php declare(strict_types=1);

namespace VitesseCms\Core\Listeners\Services;

use Phalcon\Events\Event;
use VitesseCms\Core\Services\FlashService;

class FlashServiceListener
{
    /**
     * @var FlashService
     */
    private $flash;

    public function __construct(FlashService $flash)
    {
        $this->flash = $flash;
    }

    public function attach( Event $event): FlashService
    {
        return $this->flash;
    }
}
