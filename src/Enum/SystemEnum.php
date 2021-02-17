<?php declare(strict_types=1);

namespace VitesseCms\Core\Enum;

use VitesseCms\Core\AbstractEnum;

class SystemEnum extends AbstractEnum
{
    public const COMPONENT_CONTENT = 'content';
    public const COMPONENT_FORM = 'form';
    public const COMPONENT_FORM_OPTIONS = 'formOptions';
    public const COMPONENT_USER = 'user';
    public const COMPONENT_WEBSHOP_CONTENT = 'webshopContent';
    public const COMPONENT_WEBSHOP_PRODUCT = 'webshopProduct';

    public const COMPONENTS = [
        SystemEnum::COMPONENT_CONTENT => '%ADMIN_CMS_COMPONENT_CONTENT%',
        SystemEnum::COMPONENT_FORM => '%ADMIN_CMS_COMPONENT_FORM%',
        SystemEnum::COMPONENT_FORM_OPTIONS => '%ADMIN_CMS_COMPONENT_FORM_OPTIONS%',
        SystemEnum::COMPONENT_USER => '%ADMIN_CMS_COMPONENT_USER%',
        SystemEnum::COMPONENT_WEBSHOP_CONTENT => '%ADMIN_CMS_COMPONENT_SHOP_CONTENT%',
        SystemEnum::COMPONENT_WEBSHOP_PRODUCT => '%ADMIN_CMS_COMPONENT_SHOP_PRODUCT%',
    ];
}
