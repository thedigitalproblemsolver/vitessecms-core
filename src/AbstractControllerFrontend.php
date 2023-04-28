<?php declare(strict_types=1);

namespace VitesseCms\Core;

use Phalcon\Mvc\Controller;
use VitesseCms\Core\Interfaces\ControllerInterface;
use VitesseCms\Core\Traits\ControllerTrait;

abstract class AbstractControllerFrontend extends Controller implements ControllerInterface
{
    use ControllerTrait;

    public function onConstruct()
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

        return $this->configService->getTemplatePositions();
    }

    protected function xmlResponse(string $data, bool $result = true): void
    {
        $this->response->setContentType('text/xml', 'UTF-8');
        echo $data;
        $this->viewService->disable();
        $this->response->send();

        die();
    }
}
