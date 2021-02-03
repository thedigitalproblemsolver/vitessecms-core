<?php declare(strict_types=1);

namespace VitesseCms\Core\Factories;

use VitesseCms\Core\Models\Log;
use MongoDB\BSON\ObjectId;
use Phalcon\Di;

class LogFactory
{
    /**
     * @param ObjectId $itemId
     * @param string $class
     * @param string $message
     * @param bool $published
     *
     * @return Log
     * @deprecated  should use log->write()
     */
    public static function create(
        ObjectId $itemId,
        string $class,
        string $message,
        bool $published = true
    ): Log {
        return (new Log())
            ->set('itemId', $itemId)
            ->set('class', $class)
            ->set('message', $message)
            ->set('userId', Di::getDefault()->get('user')->getId())
            ->set('published', $published)
            ->set('ipAddress', $_SERVER['REMOTE_ADDR'])
            ->set('property', $_SERVER['HTTP_HOST'])
            ->set('sourceUri', $_SERVER['REQUEST_URI'])
            ->set('post', serialize(Di::getDefault()->get('request')->getPost()));
    }
}
