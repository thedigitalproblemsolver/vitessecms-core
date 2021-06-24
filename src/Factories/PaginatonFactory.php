<?php declare(strict_types=1);

namespace VitesseCms\Core\Factories;

use Phalcon\Http\Request;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorModel;
use stdClass;
use VitesseCms\Core\Services\UrlService;

class PaginatonFactory
{
    public static function createFromArray(
        array $items,
        Request $request,
        UrlService $url,
        string $urlQueryKey = 'page',
        int $limit = 15
    ): stdClass
    {
        $paginator = new PaginatorModel(
            [
                'data' => $items,
                'limit' => $limit,
                'page' => $request->get($urlQueryKey),
            ]
        );

        $pagination = $paginator->getPaginate();
        $pagination->slug = $url->removeParamsFromQuery([$urlQueryKey], $request->getURI());
        $pagination->slugSeperator = '&';
        $pagination->urlQueryKey = $urlQueryKey;
        if (substr_count($pagination->slug, '?') === 0) :
            $pagination->slugSeperator = '?';
        endif;
        if ($pagination->before === 1) :
            unset($pagination->before);
        endif;

        return $pagination;
    }
}
