<?php declare(strict_types=1);

namespace VitesseCms\Core\Services;

use VitesseCms\Core\Utils\DirectoryUtil;
use Phalcon\Cache\Backend\File as BackFile;
use Phalcon\Cache\Frontend\Data as FrontData;

class CacheService extends AbstractInjectableService
{
    /**
     * @var int
     */
    protected $lifetime;

    /**
     * @var BackFile
     */
    protected $cache;

    public function __construct(string $cacheDir, int $lifetime)
    {
        $this->lifetime = $lifetime;

        DirectoryUtil::exists($cacheDir, true);

        $this->cache = new BackFile(
            new FrontData(['lifetime' => $this->lifetime]),
            [
                'cacheDir' => $cacheDir,
            ]
        );
    }

    public function get(string $cacheKey)
    {
        if ($this->session->get('cache') === false):
            return null;
        endif;

        return $this->cache->get($cacheKey);
    }

    public function save(string $cacheKey, $content): bool
    {
        return $this->cache->save($cacheKey, $content);
    }

    public function delete(string $cacheKey): bool
    {
        return $this->cache->delete($cacheKey);
    }

    public function setTimetoLife(int $time): void
    {
        $this->lifetime = $time;
    }

    public function getCacheKey($input): string
    {
        if (!\is_string($input)) :
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
    ): void {
        header('Cache-Control: public');
        if ($expires > 0) :
            header('Expires: '.gmdate('D, d M Y H:i:s', $expires + $expiresPeriod).' GMT');
        endif;
        if ($lastModified > 0) :
            header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastModified).' GMT');
        endif;
    }

    public function flush(): bool
    {
        return $this->cache->flush();
    }
}
