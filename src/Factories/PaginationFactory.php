<?php declare(strict_types=1);

namespace VitesseCms\Core\Factories;

use Phalcon\Http\Request;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorModel;
use VitesseCms\Core\Models\Pagination;
use VitesseCms\Core\Services\UrlService;

class PaginationFactory
{
    public static function createFromArray(
        array      $items,
        Request    $request,
        UrlService $url,
        string     $urlQueryKey = 'page',
        int        $limit = 15
    ): Pagination
    {
        $pagination = new Pagination(new PaginatorModel(
            [
                'data' => $items,
                'limit' => $limit,
                'page' => $request->get($urlQueryKey)??1,
            ]
        ));

        $pagination->setSlug($url->removeParamsFromQuery([$urlQueryKey], $request->getURI()));
        $pagination->setSlugSeperator('&');
        $pagination->setUrlQueryKey($urlQueryKey);
        $pagination->setTotalPages(count($items));
        if (substr_count($pagination->getSlug(), '?') === 0) :
            $pagination->setSlugSeperator('?');
        endif;

        return $pagination;
    }
}
