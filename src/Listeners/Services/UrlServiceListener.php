<?php declare(strict_types=1);

namespace VitesseCms\Core\Listeners\Services;

use Phalcon\Events\Event;
use VitesseCms\Core\Services\UrlService;

class UrlServiceListener
{
    private UrlService $urlService;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;
    }

    public function attach( Event $event): UrlService
    {
        return $this->urlService;
    }
}
