<?php

namespace VitesseCms\Core\Utils;

/**
 * Class ArrayUtil
 */
class ArrayUtil
{
    /**
     * Sort an array based on a array with keys
     *
     * @param array $array
     * @param array $orderArray
     * @param string $strip
     *
     * @return array
     */
    public static function sortArrayByArray(
        array $array,
        array $orderArray,
        string $strip = ''
    ): array {
        foreach ($array as $key => $value) :
            if(\is_int($key) && isset($value['id']) && MongoUtil::isObjectId($value['id'])) :
                $array[$value['id']] = $value;
                unset($array[$key]);
            endif;
        endforeach;

        $ordered = [];
        foreach ($orderArray as $key) {
            if (!empty($strip)) :
                $key = str_replace($strip, '', $key);
            endif;
            if (array_key_exists($key, $array) || isset($array[$key])) {
                $ordered[$key] = $array[$key];
                unset($array[$key]);
            }
        }

        return $ordered + $array;
    }
}
