<?php declare(strict_types=1);

namespace VitesseCms\Core\Forms;

use VitesseCms\Admin\AbstractAdminlistFilterForm;
use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Form\Interfaces\AbstractFormInterface;

class AdminlistCommunicationEmailForm extends AbstractAdminlistFilterForm
{
    public static function getAdminlistForm(
        AbstractFormInterface $form,
        BaseObjectInterface $item
    ): void {
        $form->_(
            'text',
            'Subject',
            'filter[subject.'. $form->configuration->getLanguageShort() .']'
        );
        self::addPublishedField($form);
    }
}
