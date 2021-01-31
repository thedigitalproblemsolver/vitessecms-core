<?php declare(strict_types=1);

namespace VitesseCms\Core\Forms;

use VitesseCms\Admin\AbstractAdminlistFilterForm;
use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Form\Interfaces\AbstractFormInterface;

class AdminlistSefRedirectForm extends AbstractAdminlistFilterForm
{
    public static function getAdminlistForm(
        AbstractFormInterface $form,
        BaseObjectInterface $item
    ): void {
        self::addPublishedField($form);

        $form->_(
            'text',
            '%ADMIN_FROM%',
            'filter[from]'
        );
    }
}
