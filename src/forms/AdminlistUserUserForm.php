<?php declare(strict_types=1);

namespace VitesseCms\Core\Forms;

use VitesseCms\Admin\AbstractAdminlistFilterForm;
use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Form\Interfaces\AbstractFormInterface;
use VitesseCms\User\Models\PermissionRole;

class AdminlistUserUserForm extends AbstractAdminlistFilterForm
{
    public static function getAdminlistForm(
        AbstractFormInterface $form,
        BaseObjectInterface $item
    ): void {
        self::addNameField($form);
        self::addPublishedField($form);

        $form->_(
            'text',
            '%CORE_EMAIL%',
            'filter[email]'
        );

        $form->_(
            'select',
            'User role',
            'filter[role]',
            [
                'options' => PermissionRole::class
            ]
        );
    }
}
