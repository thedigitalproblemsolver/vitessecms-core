<?php declare(strict_types=1);

namespace VitesseCms\Core;

use Phalcon\Mvc\Application;
use VitesseCms\Admin\Utils\AdminUtil;
use VitesseCms\Core\Interfaces\InjectableInterface;
use VitesseCms\Core\Traits\DiInterfaceTrait;
use VitesseCms\Core\Utils\SystemUtil;

class CoreApplicaton extends Application implements InjectableInterface
{
    use DiInterfaceTrait;
    
    public function attachListeners(): CoreApplicaton
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
