<?php declare(strict_types=1);

namespace VitesseCms\Core;

use Phalcon\Mvc\Controller;
use VitesseCms\Core\Interfaces\RenderInterface;
use VitesseCms\Core\Traits\RenderTrait;

abstract class AbstractControllerAdmin extends Controller implements RenderInterface
{
    use RenderTrait;

    protected function beforeExecuteRoute(): bool
    {
        return $this->checkAccess();
    }
}
