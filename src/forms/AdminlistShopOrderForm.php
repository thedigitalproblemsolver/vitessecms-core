<?php declare(strict_types=1);

namespace VitesseCms\Core\Forms;

use VitesseCms\Content\Models\Item;
use VitesseCms\Admin\AbstractAdminlistFilterForm;
use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Interfaces\AbstractFormInterface;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\Shop\Enum\OrderStateEnum;

class AdminlistShopOrderForm extends AbstractAdminlistFilterForm
{
    public static function getAdminlistForm(AbstractFormInterface $form, BaseObjectInterface $item): void
    {
        $form->addNumber('%SHOP_ORDERID%', 'filter[orderId]')
            ->addDropdown(
            'Order state',
            'filter[orderState.calling_name]',
                (new Attributes())->setOptions(ElementHelper::arrayToSelectOptions(OrderStateEnum::ORDER_STATES))
        );

        if($form->setting !== null && $form->setting->has('SHOP_DATAGROUP_AFFILIATE')) :
            Item::setFindValue('datagroup', $form->setting->get('SHOP_DATAGROUP_AFFILIATE'));
            $form->addDropdown(
                'Affiliate property',
                'filter[affiliateId]',
                (new Attributes())->setOptions(ElementHelper::arrayToSelectOptions(Item::findAll()))
            );
        endif;
    }
}
