<?php declare(strict_types=1);

namespace VitesseCms\Core\Forms;

use VitesseCms\Communication\Models\Newsletter;
use VitesseCms\Admin\AbstractAdminlistFilterForm;
use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Form\Interfaces\AbstractFormInterface;

class AdminlistCommunicationNewsletterQueueForm extends AbstractAdminlistFilterForm
{
    public static function getAdminlistForm(
        AbstractFormInterface $form,
        BaseObjectInterface $item
    ): void {
        $form->_(
            'email',
            '%CORE_EMAIL%',
            'filter[email]'
        )->_(
            'select',
            'Newsletter',
            'filter[newsletterId]',
            ['options' => Newsletter::class]
        );
    }
}
