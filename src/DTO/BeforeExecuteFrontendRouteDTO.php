<?php

declare(strict_types=1);

namespace VitesseCms\Core\DTO;

use VitesseCms\Content\Models\Item;
use VitesseCms\Datafield\Models\Datafield;

final class BeforeExecuteFrontendRouteDTO
{
    public ?Datafield $datafield;

    public function __construct(public readonly ?Item $currentItem)
    {
    }
}
