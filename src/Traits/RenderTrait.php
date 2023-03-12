<?php declare(strict_types=1);

namespace VitesseCms\Core\Traits;

use VitesseCms\Core\Enum\TranslationEnum;

trait RenderTrait
{
    protected function checkAccess(): bool
    {
        if (!$this->aclService->hasAccess($this->routerService->getActionName())) {
            $this->logService->message('access denied for : ' . $this->routerService->getMatchedRoute()->getCompiledPattern());
            $this->flashService->setError(TranslationEnum::CORE_ACTION_NOT_ALLOWED);
            $this->redirect($this->urlService->getBaseUri(), 401, 'Unauthorized');

            return false;
        }

        return true;
    }
}