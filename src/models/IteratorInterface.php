<?php declare(strict_types=1);

namespace VitesseCms\Core\Models;

use VitesseCms\Database\AbstractCollection;

interface IteratorInterface
{
    public function getCurrent(): AbstractCollection;
}
