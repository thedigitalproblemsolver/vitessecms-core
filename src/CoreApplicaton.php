<?php declare(strict_types=1);

namespace VitesseCms\Core;

use Phalcon\Mvc\Application;
use VitesseCms\Admin\Utils\AdminUtil;
use VitesseCms\Content\Services\ContentService;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Core\Utils\SystemUtil;

/**
 * @property ContentService $content
 */
class CoreApplicaton extends Application implements InjectableInterface
{
    public function events(): CoreApplicaton
    {
        foreach (SystemUtil::getModules($this->configuration) as $path) :
            if (AdminUtil::isAdminPage()):
                $listenerPath = $path . '/Listeners/InitiateAdminListeners.php';
            else :
                $listenerPath = $path . '/Listeners/InitiateListeners.php';
            endif;

            if (is_file($listenerPath)) :
                SystemUtil::createNamespaceFromPath($listenerPath)::setListeners($this);
            endif;
        endforeach;

        return $this;
    }
}
