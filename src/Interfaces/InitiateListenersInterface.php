<?php
declare(strict_types=1);

namespace VitesseCms\Core\Interfaces;

interface InitiateListenersInterface
{
    public static function setListeners(InjectableInterface $injectable): void;
}
