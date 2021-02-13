<?php

namespace VitesseCms\Core\Utils;

use Phalcon\Http\Uri;

/**
 * Class UrlUtil
 */
class UrlUtil
{
    /**
     * @var string
     */
    public static $url;

    /**
     * @var array
     */
    public static $urlParsed;

    /**
     * set the file in a SplFileInfo object
     *
     * @param string|null $url
     * @deprecated should be moved to UrlService
     */
    public static function setUrl(string $url = null)
    {
        if ($url !== null) :
            self::$url = $url;
            self::$urlParsed = parse_url($url);
        endif;

        Uri::class;
    }

    /**
     * @param string|null $url
     *
     * @return bool
     * @deprecated should be moved to UrlService
     */
    public static function exists(string $url = null): bool
    {
        self::setUrl($url);

        return strpos(get_headers($url)[0], '200');
    }
}
