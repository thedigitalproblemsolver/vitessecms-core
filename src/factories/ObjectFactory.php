<?php declare(strict_types=1);

namespace VitesseCms\Core\Factories;

use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Core\Interfaces\FactoryInterface;
use VitesseCms\Core\Traits\BaseObjectTrait;

class ObjectFactory implements FactoryInterface
{
    public static function create(BaseObjectInterface $bindata = null): BaseObjectInterface
    {
        return new class() implements BaseObjectInterface {
            use BaseObjectTrait;
        };
    }
}
