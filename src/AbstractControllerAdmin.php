<?php declare(strict_types=1);

namespace VitesseCms\Core;

use Phalcon\Mvc\Controller;
use VitesseCms\Admin\Forms\AdminlistForm;
use VitesseCms\Core\Interfaces\ControllerInterface;
use VitesseCms\Core\Traits\ControllerTrait;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Mustache\DTO\RenderTemplateDTO;
use VitesseCms\Mustache\Enum\ViewEnum;

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

    private function getTemplatePositions() : array
    {
        if($this->isEmbedded) {
            return  $this->configService->getTemplateEmbeddedPositions();
        }

        return $this->configService->getTemplateAdminPositions();
    }
}
