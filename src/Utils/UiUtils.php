<?php declare(strict_types=1);

namespace VitesseCms\Core\Utils;

class UiUtils
{
    public static function getScreens(): array
    {
        return [
            'xs' => 'Mobile',
            'sm' => 'Mobile portrait / Tablet landscape',
            'md' => 'Tablet portrait / Desktop small',
            'lg' => 'Dekstop',
            'xl' => 'Desktop large',
        ];
    }
}
