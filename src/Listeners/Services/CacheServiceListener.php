<?php declare(strict_types=1);

namespace VitesseCms\Core\Listeners\Services;

use VitesseCms\Core\Services\CacheService;

class CacheServiceListener
{
    /**
     * @var CacheService
     */
    private $cache;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }

    public function attach( Event $event): CacheService
    {
        return $this->cache;
    }
}
