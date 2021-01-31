<?php declare(strict_types=1);

namespace VitesseCms\Core\Forms;

use VitesseCms\Content\Models\Item;
use VitesseCms\Admin\AbstractAdminlistFilterForm;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Core\Helpers\DatagroupHelper;
use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Core\Models\Datagroup;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Form\Interfaces\AbstractFormInterface;
use Phalcon\Http\Request;

class AdminlistContentItemForm extends AbstractAdminlistFilterForm
{
    public static function getAdminlistForm(
        AbstractFormInterface $form,
        BaseObjectInterface $item
    ): void {
        $form->_(
            'hidden',
            null,
            'filter[datagroup]'
        );

        $request = new Request();
        if (isset($request->get('filter')['datagroup'])) :
            $mainDatagroup = Datagroup::findById($request->get('filter')['datagroup']);
            $datagroups = DatagroupHelper::getChildrenFromRoot($mainDatagroup);
            /** @var Datagroup $datagroup */
            foreach ($datagroups as $datagroup) :
                foreach ($datagroup->getDatafields() as $datafield) :
                    if ($datafield['published']) :
                        $datafield = Datafield::findById($datafield['id']);
                        if ($datafield && $datafield->isPublished()) :
                            $datafield->renderAdminlistFilter($form);
                        endif;
                    endif;
                endforeach;
            endforeach;

            $form->_(
                'select',
                'Has as parent',
                'filter[parentId]',
                [
                    'options' => ElementHelper::arrayToSelectOptions(self::getParentOptionsFromDatagroup($mainDatagroup)),
                ]
            );

        endif;
        self::addPublishedField($form);
    }

    protected static function getParentOptionsFromDatagroup(Datagroup $datagroup, array $parentOptions = []): array
    {
        Item::setFindValue('datagroup', (string)$datagroup->getId());
        $items = Item::findAll();
        foreach ($items as $item) :
            if ($item->hasChildren()):
                $parentOptions[(string)$item->getId()] = $item->_('name');
                self::getParentOptionsFromItem($item, $parentOptions);
            endif;
        endforeach;

        return $parentOptions;
    }

    protected static function getParentOptionsFromItem(
        AbstractCollection $parent,
        array &$parentOptions = [],
        array $prefix = []
    ): void {
        Item::setFindValue('parentId', (string)$parent->getId());
        $prefix[] = $parent->_('name');
        $items = Item::findAll();
        foreach ($items as $item) :
            if ($item->hasChildren()):
                $parentOptions[(string)$item->getId()] = implode(' > ', $prefix).' > '.$item->_('name');
                self::getParentOptionsFromItem($item, $parentOptions, $prefix);
            endif;
        endforeach;
    }
}
