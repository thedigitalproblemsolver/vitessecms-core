<?php declare(strict_types=1);

namespace VitesseCms\Core\Factories;

use VitesseCms\Datafield\Models\Datafield;
use VitesseCms\Datagroup\Models\Datagroup;
use VitesseCms\Database\Utils\MongoUtil;
use stdClass;

class DatagroupFactory
{
    public static function create(
        string $title,
        string $template,
        string $component,
        array $datafields = [],
        bool $published = false,
        bool $includeInSitemap = false,
        string $parentId = null,
        string $itemOrdering = ''
    ): Datagroup {
        $datagroup = new Datagroup();
        $datagroup->set('name', $title, true);
        $datagroup->set('template', $template);
        $datagroup->set('component', $component);
        $datagroup->set('datafields', $datafields);
        $datagroup->set('published', $published);
        $datagroup->set('sitemap', $includeInSitemap);
        $datagroup->set('parentId', $parentId);
        $datagroup->set('itemOrdering', $itemOrdering);

        if ($parentId !== null && MongoUtil::isObjectId($parentId)) :
            $parentDatagroup = Datagroup::findById($parentId);
            $parentDatagroup->set('hasChildren', true)->save();
        endif;

        return $datagroup;
    }

    public static function createDatafieldEntry(
        Datafield $datafield,
        bool $published = true,
        bool $required = false,
        bool $filterable = false
    ): array {
        return [
            'id'         => (string)$datafield->getId(),
            'published'  => $published,
            'required'   => $required,
            'filterable' => $filterable,
        ];
    }
}
