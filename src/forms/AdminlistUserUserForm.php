<?php declare(strict_types=1);

namespace VitesseCms\Core\Forms;

use VitesseCms\Admin\AbstractAdminlistFilterForm;
use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Form\Interfaces\AbstractFormInterface;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\User\Models\PermissionRole;

class AdminlistUserUserForm extends AbstractAdminlistFilterForm
{
    public static function getAdminlistForm(AbstractFormInterface $form, BaseObjectInterface $item): void
    {
        self::addNameField($form);
        self::addPublishedField($form);

        $form->addText('%CORE_EMAIL%', 'filter[email]')
            ->addDropdown(
            'User role',
            'filter[role]',
                (new Attributes())->setOptions(PermissionRole::findAll())
        );
    }
}
