<?php declare(strict_types=1);

namespace VitesseCms\Core\Listeners\Services;

use Phalcon\Events\Event;
use VitesseCms\Core\Services\ViewService;

class ViewServiceListener
{
    /**
     * @var ViewService
     */
    private $view;

    public function __construct(ViewService $view)
    {
        $this->view = $view;
    }

    public function attach( Event $event): ViewService
    {
        return $this->view;
    }
}
