<?php declare(strict_types=1);

namespace VitesseCms\Core\Interfaces;

use VitesseCms\Form\Interfaces\AbstractFormInterface;
use VitesseCms\Setting\Services\SettingService;

interface AdminlistFormInterface
{
    public static function getAdminlistForm(
        AbstractFormInterface $form,
        BaseObjectInterface $item
    ): void;
}
