<?php declare(strict_types=1);

namespace VitesseCms\Core\Forms;

use VitesseCms\Content\Models\Item;
use VitesseCms\Admin\AbstractAdminlistFilterForm;
use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Interfaces\AbstractFormInterface;
use VitesseCms\Shop\Enum\OrderStateEnum;

class AdminlistShopOrderForm extends AbstractAdminlistFilterForm
{
    public static function getAdminlistForm(
        AbstractFormInterface $form,
        BaseObjectInterface $item
    ): void {
        $form->_(
            'number',
            '%SHOP_ORDERID%',
            'filter[orderId]'
        );

        $form->_(
            'select',
            'Order state',
            'filter[orderState.calling_name]',
            [
                'options' => ElementHelper::arrayToSelectOptions(OrderStateEnum::ORDER_STATES)
            ]
        );

        if($form->setting !== null && $form->setting->has('SHOP_DATAGROUP_AFFILIATE')) :
            Item::setFindValue('datagroup', $form->setting->has('SHOP_DATAGROUP_AFFILIATE'));
            $form->_(
                'select',
                'Affiliate property',
                'filter[affiliateId]',
                [
                    'options' => ElementHelper::arrayToSelectOptions(Item::findAll())
                ]
            );
        endif;
    }
}
