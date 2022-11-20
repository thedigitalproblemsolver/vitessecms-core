<?php declare(strict_types=1);

namespace VitesseCms\Core;

use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Core\Traits\DiInterfaceTrait;

abstract class AbstractInjectable implements InjectableInterface
{
    use DiInterfaceTrait;
}
