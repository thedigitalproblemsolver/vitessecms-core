<?php declare(strict_types=1);

namespace VitesseCms\Core\Listeners\Services;

use Phalcon\Events\Event;
use VitesseCms\Core\Services\CacheService;

class CacheServiceListener
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function attach( Event $event): CacheService
    {
        return $this->cacheService;
    }
}
