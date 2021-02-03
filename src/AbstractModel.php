<?php

namespace VitesseCms\Core;

use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Core\Traits\BaseObjectTrait;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\ModelInterface;

/**
 * Class AbstractModel
 */
abstract class AbstractModel
    extends Model
    implements ModelInterface, BaseObjectInterface
{
    use BaseObjectTrait;

    /**
     * @var string
     */
    protected static $mysqlConnection = 'db';

    /**
     * initialize
     */
    public function onConstruct()
    {
        $this->setConnectionService(self::$mysqlConnection);
    }

    /**
     * @param string $connectionName
     */
    public static function setDatabase(string $connectionName)
    {
        self::$mysqlConnection = $connectionName;
    }
}
