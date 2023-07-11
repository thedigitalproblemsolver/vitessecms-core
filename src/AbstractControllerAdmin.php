<?php declare(strict_types=1);

namespace VitesseCms\Core;

use Phalcon\Mvc\Controller;
use VitesseCms\Admin\Forms\AdminlistForm;
use VitesseCms\Admin\Utils\AdminUtil;
use VitesseCms\Core\Interfaces\ControllerInterface;
use VitesseCms\Core\Traits\ControllerTrait;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Mustache\DTO\RenderTemplateDTO;
use VitesseCms\Mustache\Enum\ViewEnum;

abstract class AbstractControllerAdmin extends Controller implements ControllerInterface
{
    use ControllerTrait;

    private bool $isAdminPage;

    public function OnConstruct()
    {
        $this->attachRenderTraitServices();
        $this->isAdminPage = AdminUtil::isAdminPage();
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

    private function setViewServiceVars():void
    {
        $this->viewService->setVar('flash', $this->flashService->output());
        $this->viewService->setVar('languageLocale', $this->configService->getLanguageShort());
        $this->viewService->setVar('bodyClass', $this->getBodyClass());
    }

    private function getBodyClass():string
    {
        $return = [];
        if($this->isEmbedded) {
            $return[] = 'embedded';
        }
        if($this->isAdminPage) {
            $return[] = 'admin';
        }

        return implode(' ', $return);
    }
}
