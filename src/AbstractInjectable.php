<?php declare(strict_types=1);

namespace VitesseCms\Core;

use Phalcon\Mvc\User\Component;
use VitesseCms\Core\Interfaces\InjectableInterface;

abstract class AbstractInjectable extends Component implements InjectableInterface
{
}
