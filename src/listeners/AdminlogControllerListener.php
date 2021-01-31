<?php declare(strict_types=1);

namespace VitesseCms\Core\Listeners;

use VitesseCms\Core\Controllers\AdminlogController;
use VitesseCms\Core\Models\Log;
use Phalcon\Events\Event;

class AdminlogControllerListener
{
    public function beforeEdit(Event $event, AdminlogController $controller, Log $log ): void
    {
        $class = $log->_('class');
        $controller->addRenderParam('item', $class::findById($log->_('itemId')));

        if($log->_('userId')) :
            $controller->addRenderParam(
                'user',
                $controller->repositories->user->getById(User::findById($log->_('userId')))
            );
        endif;
    }
}
