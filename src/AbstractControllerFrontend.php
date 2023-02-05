<?php declare(strict_types=1);

namespace VitesseCms\Core;

use MongoDB\BSON\ObjectId;
use Phalcon\Mvc\Controller;
use VitesseCms\Block\DTO\RenderPositionDTO;
use VitesseCms\Block\Enum\BlockPositionEnum;
use VitesseCms\Configuration\Enums\ConfigurationEnum;
use VitesseCms\Configuration\Services\ConfigService;
use VitesseCms\Core\Enum\FlashEnum;
use VitesseCms\Core\Enum\RouterEnum;
use VitesseCms\Core\Enum\ViewEnum;
use VitesseCms\Core\Services\FlashService;
use VitesseCms\Core\Services\RouterService;
use VitesseCms\Core\Services\ViewService;
use VitesseCms\Log\Enums\LogEnum;
use VitesseCms\Log\Services\LogService;
use VitesseCms\Media\Enums\AssetsEnum;
use VitesseCms\Media\Enums\MediaEnum;
use VitesseCms\Media\Services\AssetsService;
use VitesseCms\Mustache\DTO\RenderPartialDTO;
use VitesseCms\Setting\Models\Setting;
use VitesseCms\User\Enum\AclEnum;
use VitesseCms\User\Enum\UserEnum;
use VitesseCms\User\Models\User;
use VitesseCms\User\Services\AclService;
use VitesseCms\User\Utils\PermissionUtils;
use stdClass;

abstract class AbstractControllerFrontend extends Controller
{
    protected ViewService $viewService;
    protected RouterService $routerService;
    protected FlashService $flashService;
    protected LogService $logService;
    private AclService $aclService;
    private AssetsService $assetsService;
    private ConfigService $configService;
    protected User $activeUser;

    public function onConstruct()
    {
        $this->viewService = $this->eventsManager->fire(ViewEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->routerService = $this->eventsManager->fire(RouterEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->flashService = $this->eventsManager->fire(FlashEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->logService = $this->eventsManager->fire(LogEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->aclService = $this->eventsManager->fire(AclEnum::ATTACH_SERVICE_LISTENER->value, new stdClass());
        $this->assetsService = $this->eventsManager->fire(AssetsEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->configService = $this->eventsManager->fire(ConfigurationEnum::ATTACH_SERVICE_LISTENER, new stdClass());
        $this->activeUser = $this->eventsManager->fire(UserEnum::GET_ACTIVE_USER_LISTENER->value, new stdClass());
    }

    protected function beforeExecuteRoute(): bool
    {
        if (!$this->aclService->hasAccess($this->routerService->getActionName())) {
            $this->logService->message('access denied for : ' . $this->routerService->getMatchedRoute()->getCompiledPattern());
            $this->flashService->setError('USER_NO_ACCESS');
            $this->redirect('/', 401, 'Unauthorized');

            return false;
        }

        return true;
    }

    protected function redirect(string $url, int $status = 301, string $message = 'Moved Permanently'): never
    {
        $this->response->setStatusCode($status, $message);
        $this->response->redirect($url);
        $this->viewService->disable();
        $this->response->send();
    }

    protected function afterExecuteRoute(): void
    {
        $this->renderPositions();
        $this->loadAssets();
    }

    private function renderPositions(): void
    {
        $dataGroups = ['all'];
        if ($this->viewService->hasCurrentItem()) :
            $dataGroups[] = 'page:' . $this->viewService->getCurrentItem()->getId();
            $dataGroups[] = $this->viewService->getCurrentItem()->getDatagroup();
        endif;

        foreach ($this->configService->getTemplatePositions() as $position => $tmp) {
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
                        ['html' => $html, 'class' => 'container-'.$position]
                ))
            );
        }
    }

    private function loadAssets(): void
    {
        $this->assetsService->loadSite();
        $this->eventsManager->fire('RenderListener:buildJs', new stdClass());

        $this->viewService->set('javascript', $this->assetsService->buildAssets('js'));
        $this->viewService->set('stylesheet', $this->assetsService->buildAssets('css'));
        $this->viewService->set('inlinejavascript', $this->assetsService->getInlineJs());
        $this->viewService->set('inlinestylesheet', $this->assetsService->getInlineCss());
    }
}
