<?php

declare(strict_types=1);

namespace VitesseCms\Core;

use Phalcon\Mvc\Controller;
use stdClass;
use VitesseCms\Block\DTO\RenderPositionDTO;
use VitesseCms\Block\Enum\BlockPositionEnum;
use VitesseCms\Core\DTO\BeforeExecuteFrontendRouteDTO;
use VitesseCms\Core\Enum\FrontendEnum;
use VitesseCms\Core\Enum\ViewEnum;
use VitesseCms\Core\Interfaces\ControllerInterface;
use VitesseCms\Core\Traits\ControllerTrait;
use VitesseCms\Mustache\DTO\RenderPartialDTO;
use VitesseCms\Mustache\Enum\FrontendHtmlEnum;
use VitesseCms\Setting\Enum\CallingNameEnum;

abstract class AbstractControllerFrontend extends Controller implements ControllerInterface
{
    use ControllerTrait;

    public function onConstruct()
    {
        $this->attachRenderTraitServices();
    }

    protected function beforeExecuteRoute(): bool
    {
        if (!$this->checkAccess()) {
            return false;
        }

        $this->eventsManager->fire(
            FrontendEnum::BEFORE_EXECUTE_ROUTE->value,
            new BeforeExecuteFrontendRouteDTO(
                $this->viewService->getCurrentItem()
            )
        );

        return true;
    }

    protected function xmlResponse(string $data, bool $result = true): void
    {
        $this->response->setContentType('text/xml', 'UTF-8');
        echo $data;
        $this->viewService->disable();
        $this->response->send();

        die();
    }

    private function getTemplatePositions(): array
    {
        if ($this->isEmbedded) {
            return $this->configService->getTemplateEmbeddedPositions();
        }

        return $this->configService->getTemplatePositions();
    }

    private function renderPositions(array $positions): void
    {
        $dataGroups = ['all'];
        if ($this->viewService->hasCurrentItem()) :
            $dataGroups[] = 'page:' . $this->viewService->getCurrentItem()->getId();
            $dataGroups[] = $this->viewService->getCurrentItem()->getDatagroup();
        endif;

        foreach ($positions as $position => $tmp) {
            $html = trim(
                $this->eventsManager->fire(
                    BlockPositionEnum::RENDER_POSITION,
                    new RenderPositionDTO($position, [null, '', $this->activeUser->getRole()], $dataGroups)
                )
            );

            if (!empty($html)) {
                $this->viewService->setVar(
                    $position,
                    $this->eventsManager->fire(
                        \VitesseCms\Mustache\Enum\ViewEnum::RENDER_PARTIAL_EVENT,
                        new RenderPartialDTO(
                            'template_position',
                            ['html' => $html, 'class' => 'container-' . $position]
                        )
                    )
                );
            }
        }
    }

    private function setViewServiceVars(): void
    {
        $this->viewService->setVar('flash', $this->flashService->output());
        $this->viewService->setVar('languageLocale', $this->configService->getLanguageShort());
        $this->eventsManager->fire(ViewEnum::SET_FRONTEND_VARS_SERVICE_LISTENER, new stdClass());
        $this->eventsManager->fire(FrontendHtmlEnum::PARSE_HEADER_EVENT->value, new stdClass());
        $this->viewService->setVar('htmlHead', $this->assetsService->getHeadCode());
        $this->viewService->setVar('bodyClass', $this->getBodyClass());
        $this->viewService->setVar('hideAsideMenu', $this->configService->hideAsideMenu());
        $this->viewService->setVar(
            CallingNameEnum::FAVICON,
            $this->settingService->getString(CallingNameEnum::FAVICON)
        );
    }

    private function getBodyClass(): string
    {
        $return = [];
        if ($this->isEmbedded) {
            $return[] = 'embedded';
        }
        if ($this->viewService->hasCurrentItem() && $this->viewService->getCurrentItem()->isHomepage()) {
            $return[] = 'home';
        }

        return implode(' ', $return);
    }
}
