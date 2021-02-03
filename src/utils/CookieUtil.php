<?php

namespace VitesseCms\Core\Utils;

use Phalcon\Di;
use Phalcon\Http\Response\Cookies;
use DateTime;

/**
 * Class CookieUtil
 */
class CookieUtil
{
    /**
     * @var Cookies
     */
    protected static $cookies;

    /**
     * init
     */
    protected static function init(): void
    {
        if (!is_object(self::$cookies)) {
            self::$cookies = Di::getDefault()->get('cookies');
        }
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public static function has(string $name): bool
    {
        self::init();

        return self::$cookies->has(md5($name));
    }

    /**
     * @param $name
     *
     * @return string
     */
    public static function get($name): string
    {
        if(CookieUtil::has($name)) :
            return self::$cookies->get(md5($name))->getValue(null,'');
        endif;

        return '';
    }

    /**
     * @param string $name
     * @param $value
     * @param int $expire
     * @param bool $override
     *
     * @return Cookies
     */
    public static function set(string $name, $value, int $expire = 0, $override = false): Cookies
    {
        self::init();

        if (!CookieUtil::has($name) || $override === true) :
            return self::$cookies->set(
                md5($name),
                $value,
                $expire
            );
        endif;

        return self::$cookies;
    }

    /**
     * @param string $name
     */
    public static function delete(string $name): void
    {
        self::init();

        if(CookieUtil::has($name)) :
            self::set($name, null, -1, true);
        endif;
    }
}
