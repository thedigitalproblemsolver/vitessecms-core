<?php declare(strict_types=1);

namespace VitesseCms\Core\Forms;

use VitesseCms\Admin\AbstractAdminlistFilterForm;
use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Interfaces\AbstractFormInterface;
use VitesseCms\Setting\Services\SettingService;
use Phalcon\Di;

class AdminlistBlockBlockPositionForm extends AbstractAdminlistFilterForm
{
    public static function getAdminlistForm(
        AbstractFormInterface $form,
        BaseObjectInterface $item
    ): void {
        self::addNameField($form);
        self::addPublishedField($form);

        $form->_(
            'select',
            '%ADMIN_POSITION%',
            'filter[position]',
            [
                'options' => ElementHelper::arrayToSelectOptions(
                    (array) $form->config->get('template')->get('positions')
                )
            ]
        );
    }
}
