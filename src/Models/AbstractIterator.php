<?php declare(strict_types=1);

namespace VitesseCms\Core\Models;

use ArrayIterator;
use VitesseCms\Database\AbstractCollection;

abstract class AbstractIterator extends ArrayIterator implements IteratorInterface
{
    public function getCurrent(): AbstractCollection
    {
        return $this->current();
    }
}
