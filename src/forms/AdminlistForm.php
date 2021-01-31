<?php declare(strict_types=1);

namespace VitesseCms\Core\Forms;

use VitesseCms\Database\AbstractCollection;
use VitesseCms\Core\Interfaces\AdminlistFormInterface;
use VitesseCms\Core\Utils\UiUtils;
use VitesseCms\Form\AbstractForm;

class AdminlistForm extends AbstractForm
{
    public function initialize(AbstractCollection $item): void
    {
        $this->setColumn(12, 'label', UiUtils::getScreens());
        $this->setColumn(12, 'input', UiUtils::getScreens());
        $this->setAjaxFunction('admin.fillAdminList');

        $nameParts = explode('\\', \get_class($item));
        /** @var AdminlistFormInterface $class */
        $class = 'VitesseCms\\'.$nameParts[1].'\\Forms\\Adminlist'.$nameParts[3].'Form';
        if (class_exists($class)) :
            $class::getAdminlistForm($this, $item);
        endif;

        /** @var AdminlistFormInterface $class */
        $class = 'VitesseCms\\Core\\Forms\\Adminlist'.$nameParts[1].$nameParts[3].'Form';
        if (class_exists($class)) :
            $class::getAdminlistForm($this, $item);
        endif;

        $this->addSubmitButton('%ADMIN_FILTER%')
            ->addEmptyButton('%FORM_EMPTY%')
        ;
    }
}

