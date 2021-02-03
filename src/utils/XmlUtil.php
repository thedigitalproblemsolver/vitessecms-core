<?php

namespace VitesseCms\Core\Utils;

/**
 * Class XmlUtil
 */
class XmlUtil
{
    /**
     * @param $object
     * @param $attribute
     *
     * @return string
     */
    public static function getAttribute($object, $attribute): string
    {
        if(isset($object[$attribute])) :
            return (string) $object[$attribute];
        endif;

        return '';
    }
}
