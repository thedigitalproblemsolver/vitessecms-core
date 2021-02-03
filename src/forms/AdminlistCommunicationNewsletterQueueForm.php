<?php declare(strict_types=1);

namespace VitesseCms\Core\Forms;

use VitesseCms\Communication\Models\Newsletter;
use VitesseCms\Admin\AbstractAdminlistFilterForm;
use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Interfaces\AbstractFormInterface;
use VitesseCms\Form\Models\Attributes;

class AdminlistCommunicationNewsletterQueueForm extends AbstractAdminlistFilterForm
{
    public static function getAdminlistForm(AbstractFormInterface $form, BaseObjectInterface $item): void {
        $form->addEmail('%CORE_EMAIL%', 'filter[email]')
            ->addDropdown(
            'Newsletter',
            'filter[newsletterId]',
                (new Attributes())->setOptions(ElementHelper::arrayToSelectOptions(Newsletter::findAll())
            )
        );
    }
}
