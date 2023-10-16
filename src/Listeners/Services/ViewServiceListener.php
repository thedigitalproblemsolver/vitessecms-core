<?php declare(strict_types=1);

namespace VitesseCms\Core\Listeners\Services;

use Phalcon\Events\Event;
use VitesseCms\Core\Services\ViewService;

class ViewServiceListener
{
    private ViewService $viewService;

    public function __construct(ViewService $viewService)
    {
        $this->viewService = $viewService;
    }

    public function attach( Event $event): ViewService
    {
        return $this->viewService;
    }
}
