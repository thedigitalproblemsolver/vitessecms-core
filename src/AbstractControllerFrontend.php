<?php declare(strict_types=1);

namespace VitesseCms\Core;

use Phalcon\Mvc\Controller;
use \stdClass;
use VitesseCms\Block\DTO\RenderPositionDTO;
use VitesseCms\Block\Enum\BlockPositionEnum;
use VitesseCms\Configuration\Enums\ConfigurationEnum;
use VitesseCms\Configuration\Services\ConfigService;
use VitesseCms\Core\Enum\TranslationEnum;
use VitesseCms\Core\Enum\ViewEnum;
use VitesseCms\Core\Interfaces\ControllerInterface;
use VitesseCms\Core\Traits\ControllerTrait;
use VitesseCms\Media\Enums\AssetsEnum;
use VitesseCms\Media\Services\AssetsService;
use VitesseCms\Mustache\DTO\RenderPartialDTO;
use VitesseCms\User\Enum\UserEnum;
use VitesseCms\User\Models\User;

abstract class AbstractControllerFrontend extends Controller implements ControllerInterface
{
    use ControllerTrait;

    private AssetsService $assetsService;
    protected ConfigService $configService;
    protected User $activeUser;
    private bool $isEmbedded;

    public function onConstruct()
    {
        $this->attachRenderTraitServices();

        $this->assetsService = $this->eventsManager->fire(AssetsEnum::ATTACH_SERVICE_LISTENER->value, new stdClass());
        $this->configService = $this->eventsManager->fire(ConfigurationEnum::ATTACH_SERVICE_LISTENER->value, new stdClass());
        $this->activeUser = $this->eventsManager->fire(UserEnum::GET_ACTIVE_USER_LISTENER->value, new stdClass());
    }

    protected function beforeExecuteRoute(): bool
    {
        return $this->checkAccess();
    }

    protected function afterExecuteRoute(): void
    {
        $this->setViewServiceVars();

        $positions = $this->configService->getTemplatePositions();
        if($this->isEmbedded) {
            $positions = $this->configService->getTemplateEmbeddedPositions();
        }

        $this->renderPositions($positions);
        $this->loadAssets();
    }

    private function setViewServiceVars():void
    {
        $this->viewService->setVar('flash', $this->flashService->output());
        $this->viewService->setVar('languageLocale', $this->configService->getLanguageShort());
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
            $html = $this->eventsManager->fire(
                BlockPositionEnum::RENDER_POSITION,
                new RenderPositionDTO($position, [null, '', $this->activeUser->getRole()], $dataGroups)
            );

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

    private function loadAssets(): void
    {
        $this->eventsManager->fire(AssetsEnum::RENDER_LISTENER_LOAD_ASSETS->value, new stdClass());
        $this->eventsManager->fire(AssetsEnum::RENDER_LISTENER_BUILD_JAVASCRIPT->value, new stdClass());

        $this->viewService->set('javascript', $this->assetsService->buildAssets('js'));
        $this->viewService->set('stylesheet', $this->assetsService->buildAssets('css'));
        $this->viewService->set('inlinejavascript', $this->assetsService->getInlineJs());
        $this->viewService->set('inlinestylesheet', $this->assetsService->getInlineCss());
    }
}
