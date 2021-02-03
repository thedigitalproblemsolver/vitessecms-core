<?php declare(strict_types=1);

namespace VitesseCms\Core\Models;

use \ArrayIterator;

class DatagroupIterator extends ArrayIterator
{
    public function __construct(array $datafields = [])
    {
        parent::__construct($datafields);
    }

    public function current(): Datagroup
    {
        return parent::current();
    }

    public function add(Datagroup $value): void
    {
        $this->append($value);
    }
}
