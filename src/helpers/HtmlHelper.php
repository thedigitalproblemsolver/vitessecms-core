<?php declare(strict_types=1);

namespace VitesseCms\Core\Helpers;

class HtmlHelper
{
    public static function makeAttribute(array $input, string $type): string
    {
        if (count($input) > 0) :
            return ' '.$type.'="'.trim(implode(' ', $input)).'"';
        endif;

        return '';
    }
}
