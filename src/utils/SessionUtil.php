<?php

namespace VitesseCms\Core\Utils;

use Phalcon\Di;
use Phalcon\Session\Adapter\Files as Session;

/**
 * Class SessionUtil
 * @deprecated use sesseionService from di
 */
class SessionUtil
{
    /**
     * @var Session
     */
    protected static $session;

    /**
     * init
     */
    protected static function init(): void
    {
        if (!is_object(self::$session)) {
            self::$session = Di::getDefault()->get('session');
        }
    }

    /**
     * @param string $name
     *
     * @return null|string
     */
    public static function get(string $name): ?string
    {
        self::init();

        return self::$session->get($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public static function set(string $name, $value): void
    {
        self::init();

        self::$session->set($name, $value);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public static function has(string $name): bool
    {
        self::init();

        return self::$session->has($name);
    }

    /**
     * @param string $name
     */
    public static function remove(string $name)
    {
        self::init();

        self::$session->remove($name);
    }
}
