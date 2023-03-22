<?php declare(strict_types=1);

namespace VitesseCms\Core;

use Phalcon\Mvc\Controller;
use VitesseCms\Admin\Forms\AdminlistForm;
use VitesseCms\Configuration\Enums\ConfigurationEnum;
use VitesseCms\Configuration\Services\ConfigService;
use VitesseCms\Core\Interfaces\ControllerInterface;
use VitesseCms\Core\Traits\ControllerTrait;
use VitesseCms\Mustache\DTO\RenderTemplateDTO;
use VitesseCms\Mustache\Enum\ViewEnum;

abstract class AbstractControllerAdmin extends Controller implements ControllerInterface
{
    use ControllerTrait;

    private ConfigService $configService;

    public function OnConstruct()
    {
        $this->attachRenderTraitServices();
        $this->configService = $this->eventsManager->fire(ConfigurationEnum::ATTACH_SERVICE_LISTENER->value, new \stdClass());

        $this->link = $this->url->getBaseUri() . 'admin/' . $this->router->getModuleName() . '/' . $this->router->getControllerName();
        $this->listTemplate = 'adminList';
        $this->listTemplatePath = $this->configService->getVendorNameDir() . 'admin/src/Resources/views/';
    }

    protected function beforeExecuteRoute(): bool
    {
        return $this->checkAccess();
    }

    public function adminListAction(): void
    {
        $adminListButtons = $this->eventsManager->fire(ViewEnum::RENDER_TEMPLATE_EVENT, new RenderTemplateDTO(
            str_replace('admin', '', $this->routerService->getControllerName()) . 'Buttons',
            $this->routerService->getModuleName() . '/src/Resources/views/admin/list/'
        ));

        $this->viewService->set(
            'content',
            $this->viewService->renderTemplate(
                $this->listTemplate,
                $this->listTemplatePath,
                [
                    'list' => $this->recursiveAdminList($this->getAdminListPagination()),
                    'editBaseUri' => $this->link,
                    'isAjax' => $this->request->isAjax(),
                    'filter' => $this->eventsManager->fire(
                        get_class($this) . ':adminListFilter',
                        $this,
                        new AdminlistForm()
                    ),
                    'adminListButtons' => $adminListButtons,
                    'displayEditButton' => $this->displayEditButton
                ]
            )
        );
    }
}
