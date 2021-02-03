<?php declare(strict_types=1);

namespace VitesseCms\Core\Forms;

use VitesseCms\Admin\AbstractAdminlistFilterForm;
use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Form\Interfaces\AbstractFormInterface;

class AdminlistCoreLogForm extends AbstractAdminlistFilterForm
{
    public static function getAdminlistForm(
        AbstractFormInterface $form,
        BaseObjectInterface $item
    ): void {
        $form->_(
            'text',
            'itemId',
            'filter[itemId]'
        );
        $form->_(
            'text',
            'userId',
            'filter[userId]'
        );
        $form->_(
            'text',
            'ipAddress',
            'filter[ipAddress]'
        );
        $form->_(
            'text',
            'property',
            'filter[property]'
        );
        $form->_(
            'text',
            'sourceUri',
            'filter[sourceUri]'
        );
    }
}
