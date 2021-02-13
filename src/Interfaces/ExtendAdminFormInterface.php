<?php

namespace VitesseCms\Core\Interfaces;

use VitesseCms\Form\AbstractForm;

/**
 * Class ExtendAdminFormInterface
 */
interface ExtendAdminFormInterface
{
    /**
     * @param AbstractForm $form
     */
    public function buildAdminForm(AbstractForm $form): void;
}
