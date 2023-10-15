<?php declare(strict_types=1);

namespace VitesseCms\Core\Utils;

class StringUtil
{
    public static function camelCaseToSeperator(string $str, string $separator = ' ')
    {
        return ucfirst(strtolower(preg_replace("/[A-Z]/", $separator . "$0", $str)));
    }
}