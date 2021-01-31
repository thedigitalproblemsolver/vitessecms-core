<?php declare(strict_types=1);

namespace VitesseCms\Core\Helpers;

use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Core\Models\Datagroup;

/**
 * Class DatagroupHelper
 */
class DatagroupHelper
{
    /**
    * @param Datagroup $datagroup
    * @param array $fields
    *
    * @return array
    */
    public static function getPathToRoot(
        Datagroup $datagroup,
        array $fields = []
    ): array {
        $fields[] = $datagroup;
        if ($datagroup->_('parentId')) :
            $fields = self::getPathToRoot(
                Datagroup::findById($datagroup->_('parentId')),
                $fields
            );
        endif;

        return $fields;
    }

    public static function getPathFromRoot(Datagroup $datagroup ): array
    {
        return array_reverse(self::getPathToRoot($datagroup));
    }

    public static function getChildrenFromRoot(
        Datagroup $datagroup,
        array $datagroups = []
    ): array {
        $datagroups[] = $datagroup;
        Datagroup::setFindValue('parentId', (string)$datagroup->getId());
        $groups = Datagroup::findAll();
        if(\count($groups)) :
            /** @var Datagroup $group */
            foreach ($groups as $group) :
                $datagroups = self::getChildrenFromRoot($group, $datagroups);
            endforeach;
        endif;

        return $datagroups;
    }

    /**
     * @param Datagroup $datagroup
     *
     * @return bool
     */
    public static function hasFilterableFields(Datagroup $datagroup): bool
    {
        foreach ((array)$datagroup->_('datafields') as $field) :
            if (!empty($field['filterable'])) :
                $datafield = Datafield::findById($field['id']);
                /** @var Datafield $datafield */
                if (\is_object($datafield) && $datafield->_('published')) :
                    return true;
                endif;
            endif;
        endforeach;

        return false;
    }
}
