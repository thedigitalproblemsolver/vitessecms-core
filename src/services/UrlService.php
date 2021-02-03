<?php declare(strict_types=1);

namespace VitesseCms\Core\Services;

use Phalcon\Http\Request;
use Phalcon\Mvc\Url;

class UrlService extends Url
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var mixed
     */
    protected $urlParsed;

    /**
     * @var string
     */
    protected $protocol;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->protocol = 'https';
        if ($this->request->getPort() === 80) :
            $this->protocol = 'http';
        endif;
        $this->setBaseUri($this->protocol.'://'.$_SERVER['HTTP_HOST'].'/');
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
        $this->urlParsed = parse_url($url);
    }

    public function getParsed(string $key): string
    {
        return $this->urlParsed[$key] ?? '';
    }

    public function exists(string $url = null): bool
    {
        if(filter_var($url, FILTER_VALIDATE_URL)) :
            return (bool)strpos(get_headers($url)[0], '200');
        endif;

        return false;
    }

    public function checkProtocol(bool $useHttps): void
    {
        if ($useHttps && $_SERVER['SERVER_PORT'] === '80') :
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
            die();
        endif;

        if (!$useHttps && $_SERVER['SERVER_PORT'] !== '80') :
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
            die();
        endif;
    }

    public function getProtocol(): string
    {
        return $this->protocol;
    }

    public function removeParamsFromQuery(array $params, string $url): string
    {
        if (strpos($url, '?') === 0) :
            return $url;
        endif;

        $urlParsed = parse_url($url);
        if (empty($urlParsed['query'])) :
            return $url;
        endif;

        parse_str($urlParsed['query'], $queryParts);
        foreach ($params as $param) :
            if (isset($queryParts[$param])) :
                unset($queryParts[$param]);
            endif;
        endforeach;

        $query = [];
        foreach ($queryParts as $key => $part) :
            if (is_array($part)) :
                $keys = array_keys($part);
                $query[$key.'['.$keys[0].']'] = $part[$keys[0]];
            else :
                $query[$key] = $part;
            endif;
        endforeach;

        return $urlParsed['path'].'?'.http_build_query($query);
    }

    public function addParamsToQuery(string $key, string $value, string $url): string
    {
        $urlParsed = parse_url($url);

        if (isset($urlParsed['query'])) :
            parse_str($urlParsed['query'], $queryParts);
            $queryParts[$key] = $value;
        else :
            $queryParts[$key] = $value;
        endif;

        return $urlParsed['path'].'?'.http_build_query($queryParts);
    }
}
