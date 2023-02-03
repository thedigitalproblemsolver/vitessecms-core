<?php declare(strict_types=1);

namespace VitesseCms\Core;

use MongoDB\BSON\ObjectId;
use Phalcon\Mvc\Controller;
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
use VitesseCms\Setting\Models\Setting;
use VitesseCms\User\Enum\AclEnum;
use VitesseCms\User\Enum\UserEnum;
use VitesseCms\User\Models\User;
use VitesseCms\User\Services\AclService;
use VitesseCms\User\Utils\PermissionUtils;

abstract class AbstractControllerFrontend extends Controller
{
    protected ViewService $viewService;
    protected RouterService $routerService;
    protected FlashService $flashService;
    protected LogService $logService;
    private AclService $aclService;
    private AssetsService $assetsService;

    public function onConstruct()
    {
        $this->viewService = $this->eventsManager->fire(ViewEnum::ATTACH_SERVICE_LISTENER, new \stdClass());
        $this->routerService = $this->eventsManager->fire(RouterEnum::ATTACH_SERVICE_LISTENER, new \stdClass());
        $this->flashService = $this->eventsManager->fire(FlashEnum::ATTACH_SERVICE_LISTENER, new \stdClass());
        $this->logService = $this->eventsManager->fire(LogEnum::ATTACH_SERVICE_LISTENER, new \stdClass());
        $this->aclService = $this->eventsManager->fire(AclEnum::ATTACH_SERVICE_LISTENER, new \stdClass());
        $this->assetsService = $this->eventsManager->fire(AssetsEnum::ATTACH_SERVICE_LISTENER, new \stdClass());
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

    protected function redirect(string $url, int $status = 301, string $message = 'Moved Permanently'): void
    {
        $this->response->setStatusCode($status, $message);
        $this->response->redirect($url);
        $this->viewService->disable();
        $this->response->send();
    }

    protected function afterExecuteRoute(): void
    {
        $this->assetsService->loadSite();
        $this->eventsManager->fire('RenderListener:buildJs', new \stdClass());

        $this->viewService->set('javascript', $this->assetsService->buildAssets('js'));
        $this->viewService->set('stylesheet', $this->assetsService->buildAssets('css'));
        $this->viewService->set('inlinejavascript', $this->assetsService->getInlineJs());
        $this->viewService->set('inlinestylesheet', $this->assetsService->getInlineCss());
    }
}
