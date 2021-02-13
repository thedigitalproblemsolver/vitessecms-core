<?php

namespace VitesseCms\Core\Interfaces;

/**
 * Class FactoryInterface
 * @deprecated unnessecary
 */
interface FactoryInterface
{
    /**
     * @param BaseObjectInterface|null $bindData
     *
     * @return BaseObjectInterface
     */
    public static function create(BaseObjectInterface $bindData = null) : BaseObjectInterface;
}
