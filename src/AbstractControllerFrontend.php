<?php declare(strict_types=1);

namespace VitesseCms\Core;

use Phalcon\Mvc\Controller;
use stdClass;
use VitesseCms\Block\DTO\RenderPositionDTO;
use VitesseCms\Block\Enum\BlockPositionEnum;
use VitesseCms\Configuration\Enums\ConfigurationEnum;
use VitesseCms\Configuration\Services\ConfigService;
use VitesseCms\Core\Enum\FlashEnum;
use VitesseCms\Core\Enum\RouterEnum;
use VitesseCms\Core\Enum\TranslationEnum;
use VitesseCms\Core\Enum\UrlEnum;
use VitesseCms\Core\Enum\ViewEnum;
use VitesseCms\Core\Services\FlashService;
use VitesseCms\Core\Services\RouterService;
use VitesseCms\Core\Services\UrlService;
use VitesseCms\Core\Services\ViewService;
use VitesseCms\Log\Enums\LogEnum;
use VitesseCms\Log\Services\LogService;
use VitesseCms\Media\Enums\AssetsEnum;
use VitesseCms\Media\Services\AssetsService;
use VitesseCms\Mustache\DTO\RenderPartialDTO;
use VitesseCms\User\Enum\AclEnum;
use VitesseCms\User\Enum\UserEnum;
use VitesseCms\User\Models\User;
use VitesseCms\User\Services\AclService;

abstract class AbstractControllerFrontend extends Controller
{
    protected ViewService $viewService;
    protected RouterService $routerService;
    protected FlashService $flashService;
    protected LogService $logService;
    private AclService $aclService;
    private AssetsService $assetsService;
    protected ConfigService $configService;
    protected User $activeUser;
    protected UrlService $urlService;
    private bool $isEmbedded;

    public function onConstruct()
    {
        $this->viewService = $this->eventsManager->fire(ViewEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->routerService = $this->eventsManager->fire(RouterEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->flashService = $this->eventsManager->fire(FlashEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->logService = $this->eventsManager->fire(LogEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->aclService = $this->eventsManager->fire(AclEnum::ATTACH_SERVICE_LISTENER->value, new stdClass());
        $this->assetsService = $this->eventsManager->fire(AssetsEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->configService = $this->eventsManager->fire(ConfigurationEnum::ATTACH_SERVICE_LISTENER->value, new stdClass());
        $this->activeUser = $this->eventsManager->fire(UserEnum::GET_ACTIVE_USER_LISTENER->value, new stdClass());
        $this->urlService = $this->eventsManager->fire(UrlEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->isEmbedded = $this->request->get('embedded', 'bool', false);
    }

    protected function beforeExecuteRoute(): bool
    {
        if (!$this->aclService->hasAccess($this->routerService->getActionName())) {
            $this->logService->message('access denied for : ' . $this->routerService->getMatchedRoute()->getCompiledPattern());
            $this->flashService->setError(TranslationEnum::CORE_ACTION_NOT_ALLOWED);
            $this->redirect($this->urlService->getBaseUri(), 401, 'Unauthorized');

            return false;
        }

        return true;
    }

    protected function redirect(string $url, int $status = 301, string $message = 'Moved Permanently'): void
    {
        if($this->isEmbedded) {
            $url = $this->urlService->addParamsToQuery('embedded', '1', $url);
        }

        if ($this->request->isAjax()) {
            $this->response->setContentType('application/json', 'UTF-8');
            $this->response->setContent(json_encode([
                'result' => true,
                'successFunction' => 'redirect(\'' . $url . '\')'
            ]));
            $this->viewService->disable();
            $this->response->send();
        } else {
            $this->response->setStatusCode($status, $message);
            $this->response->redirect($url);
            $this->viewService->disable();
            $this->response->send();
        }

        die();
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
        $this->eventsManager->fire(ViewEnum::SET_FRONTEND_VARS_SERVICE_LISTENER, new stdClass());
    }

    protected function jsonResponse(array $data, bool $result = true): void
    {
        $this->response->setContentType('application/json', 'UTF-8');
        echo json_encode(array_merge(['result' => $result], $data));
        $this->viewService->disable();
        $this->response->send();

        die();
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
        $this->eventsManager->fire('RenderListener:loadAssets', new stdClass());
        $this->eventsManager->fire('RenderListener:buildJs', new stdClass());

        $this->viewService->set('javascript', $this->assetsService->buildAssets('js'));
        $this->viewService->set('stylesheet', $this->assetsService->buildAssets('css'));
        $this->viewService->set('inlinejavascript', $this->assetsService->getInlineJs());
        $this->viewService->set('inlinestylesheet', $this->assetsService->getInlineCss());
    }
}
