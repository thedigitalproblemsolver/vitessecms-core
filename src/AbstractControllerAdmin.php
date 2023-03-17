<?php declare(strict_types=1);

namespace VitesseCms\Core;

use Phalcon\Mvc\Controller;
use VitesseCms\Core\Interfaces\ControllerInterface;
use VitesseCms\Core\Traits\ControllerTrait;

abstract class AbstractControllerAdmin extends Controller implements ControllerInterface
{
    use ControllerTrait;

    public function OnConstruct()
    {
        $this->attachRenderTraitServices();
    }

    protected function beforeExecuteRoute(): bool
    {
        return $this->checkAccess();
    }
}
