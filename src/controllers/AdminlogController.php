<?php declare(strict_types=1);

namespace VitesseCms\Core\Controllers;

use VitesseCms\Admin\AbstractAdminController;
use VitesseCms\Core\Interfaces\RepositoriesInterface;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Core\Models\Log;
use VitesseCms\Form\AbstractForm;

class AdminlogController extends AbstractAdminController implements RepositoriesInterface
{

    public function onConstruct()
    {
        parent::onConstruct();

        $this->listOrder = 'createdAt';
        $this->listOrderDirection = -1;
        $this->class = Log::class;
    }

    public function editAction(
        string $itemId = null,
        string $template = 'editForm',
        string $templatePath = 'src/core/resources/views/admin/',
        AbstractForm $form = null
    ): void {
        parent::editAction(
            $itemId,
            'adminLogEdit'
        );
    }

    protected function getAdminlistName(AbstractCollection $item) : string
    {
        return $item->getCreateDate()->format('Y-m-d').' - '.$item->_('message');
    }
}
