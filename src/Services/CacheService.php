<?php declare(strict_types=1);

namespace VitesseCms\Core\Services;

use Phalcon\Cache\AdapterFactory;
use Phalcon\Cache\Cache;
use Phalcon\Storage\SerializerFactory;
use VitesseCms\Core\Utils\DirectoryUtil;
use function is_string;

class CacheService
{
    /**
     * @var int
     */
    protected int $lifetime;

    /**
     * @var Cache
     */
    protected $cache;

    public function __construct(string $cacheDir, int $lifetime)
    {
        $this->lifetime = $lifetime;

        DirectoryUtil::exists($cacheDir, true);

        $adapterFactory = new AdapterFactory(new SerializerFactory());

        $adapter = $adapterFactory->newInstance('stream', [
            'lifetime' => $this->lifetime,
            'storageDir' => $cacheDir
        ]);

        $this->cache = new Cache($adapter);
    }

    public function get(string $cacheKey)
    {
        return $this->cache->get($cacheKey);
    }

    public function save(string $cacheKey, $content): bool
    {
        return $this->cache->set($cacheKey, $content);
    }

    public function setTimetoLife(int $time): void
    {
        $this->lifetime = $time;
    }

    public function getCacheKey($input): string
    {
        if (!is_string($input)) :
            $input = serialize($input);
        endif;

        return md5($input);
    }

    public function setNoCacheHeaders(): void
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
    }

    public function setCacheHeaders(
        int $expires = 0,
        int $lastModified = 0,
        int $expiresPeriod = 31536000
    ): void
    {
        header('Cache-Control: public');
        if ($expires > 0) :
            header('Expires: ' . gmdate('D, d M Y H:i:s', $expires + $expiresPeriod) . ' GMT');
        endif;
        if ($lastModified > 0) :
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
        endif;
    }

    public function flush(): bool
    {
        return $this->cache->clear();
    }

    public function delete(string $cacheKey): bool
    {
        return $this->cache->delete($cacheKey);
    }
}
