<?php declare(strict_types=1);

namespace VitesseCms\Core\Repositories;

use VitesseCms\Core\Models\Datagroup;
use VitesseCms\Core\Models\DatagroupIterator;
use VitesseCms\Database\Models\FindValueIterator;

class DatagroupRepository
{

    public function getById(string $id, bool $hideUnpublished = true): ?Datagroup
    {
        Datagroup::setFindPublished($hideUnpublished);

        /** @var Datagroup $datagroup */
        $datagroup = Datagroup::findById($id);
        if (is_object($datagroup)):
            return $datagroup;
        endif;

        return null;
    }

    public function findAllByParentId(string $parentId, bool $hideUnpublished = true): DatagroupIterator
    {
        Datagroup::setFindValue('parentId', $parentId);
        Datagroup::setFindPublished($hideUnpublished);
        Datagroup::addFindOrder('name');

        return new DatagroupIterator(Datagroup::findAll());
    }

    public function findAll(?FindValueIterator $findValues = null, bool $hideUnpublished = true): DatagroupIterator
    {
        Datagroup::setFindPublished($hideUnpublished);
        Datagroup::addFindOrder('name');
        $this->parsefindValues($findValues);

        return new DatagroupIterator(Datagroup::findAll());
    }

    public function findFirst(
        ?FindValueIterator $findValues = null,
        bool $hideUnpublished = true
    ): ?Datagroup {
        Datagroup::setFindPublished($hideUnpublished);
        $this->parsefindValues($findValues);

        /** @var Datagroup $datagroup */
        $datagroup = Datagroup::findFirst();
        if (is_object($datagroup)):
            return $datagroup;
        endif;

        return null;
    }

    public function getPathToRoot(
        Datagroup $datagroup,
        ?DatagroupIterator $datagroupIterator = null
    ): DatagroupIterator {
        if($datagroupIterator === null) {
            $datagroupIterator = new DatagroupIterator();
        }

        $datagroupIterator->add($datagroup);
        if (!empty($datagroup->getParentId())) :
            $this->getPathToRoot(
                $this->getById($datagroup->getParentId()),
                $datagroupIterator
            );
        endif;

        return $datagroupIterator;
    }

    public function getPathFromRoot(Datagroup $datagroup ): DatagroupIterator
    {
        return new DatagroupIterator(array_reverse($this->getPathToRoot($datagroup)->getArrayCopy()));
    }

    protected function parsefindValues(?FindValueIterator $findValues = null): void
    {
        if ($findValues !== null) :
            while ($findValues->valid()) :
                $findValue = $findValues->current();
                Datagroup::setFindValue(
                    $findValue->getKey(),
                    $findValue->getValue(),
                    $findValue->getType()
                );
                $findValues->next();
            endwhile;
        endif;
    }
}
