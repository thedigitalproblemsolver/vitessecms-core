<?php declare(strict_types=1);

namespace VitesseCms\Core;

use VitesseCms\Core\Interfaces\InjectableInterface;
use Phalcon\Mvc\User\Component;

abstract class AbstractInjectable extends Component implements InjectableInterface
{
}
