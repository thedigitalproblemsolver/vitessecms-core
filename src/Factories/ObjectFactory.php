<?php declare(strict_types=1);

namespace VitesseCms\Core\Factories;

use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Core\Traits\BaseObjectTrait;

class ObjectFactory
{
    public static function create(): BaseObjectInterface
    {
        return new class() implements BaseObjectInterface {
            use BaseObjectTrait;
        };
    }
}
