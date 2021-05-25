<?php declare(strict_types=1);

namespace VitesseCms\Core;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\ModelInterface;
use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Core\Traits\BaseObjectTrait;

abstract class AbstractModel extends Model implements ModelInterface, BaseObjectInterface
{
    use BaseObjectTrait;
}
