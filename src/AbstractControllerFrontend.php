<?php declare(strict_types=1);

namespace VitesseCms\Core;

use Phalcon\Mvc\Controller;
use VitesseCms\Block\DTO\RenderPositionDTO;
use VitesseCms\Block\Enum\BlockPositionEnum;
use VitesseCms\Core\Enum\ViewEnum;
use VitesseCms\Core\Interfaces\ControllerInterface;
use VitesseCms\Core\Traits\ControllerTrait;
use VitesseCms\Mustache\DTO\RenderPartialDTO;
use VitesseCms\Mustache\Enum\FrontendHtmlEnum;

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

    private function renderPositions(array $positions): void
    {
        $dataGroups = ['all'];
        if ($this->viewService->hasCurrentItem()) :
            $dataGroups[] = 'page:' . $this->viewService->getCurrentItem()->getId();
            $dataGroups[] = $this->viewService->getCurrentItem()->getDatagroup();
        endif;

        foreach ($positions as $position => $tmp) {
            $html = trim($this->eventsManager->fire(
                BlockPositionEnum::RENDER_POSITION,
                new RenderPositionDTO($position, [null, '', $this->activeUser->getRole()], $dataGroups)
            ));

            if(!empty($html)) {
                $this->viewService->setVar(
                    $position,
                    $this->eventsManager->fire(
                        \VitesseCms\Mustache\Enum\ViewEnum::RENDER_PARTIAL_EVENT,
                        new RenderPartialDTO(
                            'template_position',
                            ['html' => $html, 'class' => 'container-' . $position]
                        ))
                );
            }
        }
    }

    private function loadAssets(): void
    {
        $this->eventsManager->fire('RenderListener:loadAssets', new \stdClass());
        $this->eventsManager->fire('RenderListener:buildJs', new \stdClass());

        $this->viewService->set('javascript', $this->assetsService->buildAssets('js'));
        $this->viewService->set('stylesheet', $this->assetsService->buildAssets('css'));
        $this->viewService->set('inlinejavascript', $this->assetsService->getInlineJs());
        $this->viewService->set('inlinestylesheet', $this->assetsService->getInlineCss());
    }

    private function setViewServiceVars():void
    {
        $this->viewService->setVar('flash', $this->flashService->output());
        $this->viewService->setVar('languageLocale', $this->configService->getLanguageShort());
        $this->eventsManager->fire(ViewEnum::SET_FRONTEND_VARS_SERVICE_LISTENER, new \stdClass());
        $this->eventsManager->fire(FrontendHtmlEnum::PARSE_HEADER_EVENT->value, new \stdClass());
        $this->viewService->setVar('htmlHead', $this->assetsService->getHeadCode());
    }
}
