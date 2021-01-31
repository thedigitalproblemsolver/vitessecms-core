<?php declare(strict_types=1);

namespace VitesseCms\Core\Helpers;

use VitesseCms\Content\Models\Item;
use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Core\Models\Datagroup;
use VitesseCms\Datafield\AbstractField;
use VitesseCms\User\Models\User;
use VitesseCms\User\Utils\PermissionUtils;

class ItemHelper
{
    public static function getRowStateClass(bool $published): string
    {
        if (!$published) :
            return 'list-group-item-danger';
        endif;

        return 'list-group-item-success';
    }

    public static function getPublishIcon(bool $published, bool $button = false): string
    {
        $btn = '';
        if (!$published) :
            if ($button) :
                $btn = ' btn btn-danger';
            endif;

            return 'publish-toggle fa fa-circle red'.$btn;
        endif;
        if ($button) :
            $btn = ' btn btn-success';
        endif;

        return 'publish-toggle fa fa-circle'.$btn;
    }

    public static function getRequiredIcon(bool $required, bool $button = false): string
    {
        $btn = '';
        if (!$required) :
            if ($button) :
                $btn = ' btn btn-danger';
            endif;

            return 'fa fa-asterisk red'.$btn;
        endif;
        if ($button) :
            $btn = ' btn btn-success';
        endif;

        return 'fa fa-asterisk'.$btn;
    }

    public static function getIcon(bool $state, string $type, bool $button = false): string
    {
        $btn = '';
        if (!$state) :
            if ($button) :
                $btn = ' btn btn-danger';
            endif;

            return 'fa fa-'.$type.' red'.$btn;
        endif;
        if ($button) :
            $btn = ' btn btn-success';
        endif;

        return 'fa fa-'.$type.' '.$btn;
    }

    public static function getPublishText(bool $published): string
    {
        if (!$published) :
            return '%ADMIN_SET_TO_PUBLISH%';
        endif;

        return '%ADMIN_SET_TO_UNPUBLISH%';
    }

    public static function getRequiredText(bool $required): string
    {
        if (!$required) :
            return '%ADMIN_SET_TO_REQUIRED%';
        endif;

        return '%ADMIN_SET_TO_NOT_REQUIRED%';
    }

    public static function getPathFromRoot(Item $item): array
    {
        $path = [$item];
        if ($item->_('parentId') !== null) :
            $parent = Item::findById($item->_('parentId'));
            if ($parent) :
                $path = self::getParents($path, $parent);
            endif;
        endif;

        $path = array_reverse($path);

        return $path;
    }

    public static function getParents(array $path, Item $item): array
    {
        $path[] = $item;
        if ($item->_('parentId') != null) :
            $parent = Item::findById($item->_('parentId'));
            if ($parent) :
                $path = ItemHelper::getParents($path, $parent);
            endif;
        endif;

        return $path;
    }

    public static function buildItemTree(
        string $parentId = null,
        array $return = [],
        string $postfix = ''
    ): array {
        Item::setFindValue('parentId', $parentId);
        Item::addFindOrder('name');
        $items = Item::findAll();

        foreach ($items as $item) :
            $return[(string)$item->getId()] = $postfix.$item->_('name');
            if ($item->_('hasChildren') === true) :
                $return = self::buildItemTree((string)$item->getId(), $return, $postfix.$item->_('name').' > ');
            endif;
        endforeach;

        return $return;
    }

    /**
     * TODO efficenter door middel van DB-toggle?
     */
    public static function parseBeforeMainContent(Item $item)
    {
        /** @var Datagroup $datagroup */
        $datagroup = Datagroup::findById($item->getDatagroup('datagroup'));
        if($datagroup) :
            $datafields = $datagroup->getDatafields();
            foreach ($datafields as $datafieldObject) :
                /** @var Datafield $datafield */
                $datafield = Datafield::findById($datafieldObject['id']);
                if ($datafield !== null) :
                    $datafield->getClass()::beforeMaincontent($item, $datafield);
                endif;
            endforeach;
        endif;
    }

    public static function setEditLink(Item $item, User $user): void
    {
        $item->set('editLink', '');
        if (PermissionUtils::check(
            $user,
            'content',
            'adminitem',
            'edit'
        )) :
            $item->set('editLink', '/admin/content/adminitem/edit/'.$item->getId());
        endif;
    }

    public static function checkAccess(User $user, Item $item): bool
    {
        if ($user->getPermissionRole() === 'superadmin') :
            return true;
        endif;

        $roles = $item->_('roles');
        if (is_array($roles)) :
            array_push($roles, '');
            array_push($roles, null);
        else :
            $roles = ['', null];
            $item->set('roles', $roles);
        endif;

        if (
            $item->_('roles')[0] != ''
            && !in_array($user->_('role'), $roles)
        ) :
            return false;
        endif;

        return true;
    }

    public static function getRecursiveChildren(string $parentId, array $return = []): array
    {
        Item::setFindValue('parentId', $parentId);
        Item::setFindLimit(9999);
        $items = (array)Item::findAll();
        foreach ($items as $item) :
            $return[] = $item;
            if ($item->hasChildren()) :
                $return = self::getRecursiveChildren((string)$item->getId(), $return);
            endif;
        endforeach;

        return $return;
    }
}
