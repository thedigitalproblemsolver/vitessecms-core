<?php

namespace VitesseCms\Core;

use VitesseCms\Core\Interfaces\AbstractFactoryInterface;
use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Database\AbstractCollection;
use MongoDB\BSON\ObjectID;

/**
 * Class AbstractFactory
 * @deprecated unnessecary
 */
abstract class AbstractFactory
{
    /**
     * @param string $modelClass
     * @param BaseObjectInterface|null $bindData
     *
     * @return AbstractCollection
     */
    protected static function createCollection(
        string $modelClass,
        BaseObjectInterface $bindData = null
    ): AbstractCollection
    {
        /** @var AbstractCollection $item */
        $item = new $modelClass();
        //$item->setId((new ObjectID()));

        /*if( is_object($bindData)) :
            Datagroup::setFindPublished(false);
            $datagroup = Datagroup::findById($datagroupId);
            foreach ($datagroup->_('datafields') as $datafieldId => $groupDatafield ) :
                Datafield::setFindPublished(false);
                $datafield = Datafield::findById($datafieldId);

                var_dump('CountryFactory '.$datafield->_('calling_name'));
                die();
            endforeach;
        endif;*/

        return $item;
    }
}
