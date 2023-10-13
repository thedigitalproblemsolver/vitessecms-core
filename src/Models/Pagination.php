<?php declare(strict_types=1);

namespace VitesseCms\Core\Models;

use Phalcon\Paginator\Adapter\NativeArray;

class Pagination
{
    /**
     * @var NativeArray
     */
    protected $nativeArray;

    protected string $slug;
    protected string $slugSeperator;
    protected string $urlQueryKey;
    protected int $totalPages;

    public function __construct(NativeArray $nativeArray)
    {
        $this->nativeArray = $nativeArray;
    }

    public function getItems()
    {
        return $this->nativeArray->paginate()->getItems();
    }

    public function getNext(): int
    {
        return $this->nativeArray->paginate()->getNext();
    }

    public function getPrevious(): int
    {
        return $this->nativeArray->paginate()->getPrevious();
    }

    public function getLast(): int
    {
        return $this->nativeArray->paginate()->getLast();
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): Pagination
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlugSeperator(): string
    {
        return $this->slugSeperator;
    }

    public function setSlugSeperator(string $slugSeperator): Pagination
    {
        $this->slugSeperator = $slugSeperator;

        return $this;
    }

    public function getUrlQueryKey(): string
    {
        return $this->urlQueryKey;
    }

    public function setUrlQueryKey(string $urlQueryKey): Pagination
    {
        $this->urlQueryKey = $urlQueryKey;

        return $this;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function setTotalPages(int $totalPages): Pagination
    {
        $this->totalPages = $totalPages;

        return $this;
    }
}