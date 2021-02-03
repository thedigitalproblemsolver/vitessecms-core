<?php declare(strict_types=1);

namespace VitesseCms\Core\Forms;

use VitesseCms\Admin\AbstractAdminlistFilterForm;
use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Form\Interfaces\AbstractFormInterface;

class AdminlistCoreLogForm extends AbstractAdminlistFilterForm
{
    public static function getAdminlistForm(AbstractFormInterface $form, BaseObjectInterface $item): void
    {
        $form->addText('itemId', 'filter[itemId]')
            ->addText('userId', 'filter[userId]')
            ->addText('ipAddress', 'filter[ipAddress]')
            ->addText('property', 'filter[property]')
            ->addText('sourceUri', 'filter[sourceUri]')
        ;
    }
}
