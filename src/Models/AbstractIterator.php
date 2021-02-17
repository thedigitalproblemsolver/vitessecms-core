<?php declare(strict_types=1);

namespace VitesseCms\Core\Models;

use VitesseCms\Database\AbstractCollection;
use ArrayIterator;

abstract class AbstractIterator extends ArrayIterator implements IteratorInterface
{
    public function getCurrent(): AbstractCollection
    {
        return $this->current();
    }
}
