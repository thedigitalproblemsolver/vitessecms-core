<?php declare(strict_types=1);

namespace VitesseCms\Core\Forms;

use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Models\Attributes;
use VitesseCms\User\Utils\PermissionUtils;

class AdminToolbarForm extends AbstractForm
{
    public function initialize(): void
    {
        $cache = $this->session->get('cache', true);
        $editorMode = $this->session->get('editorMode', false);
        $layoutMode = $this->session->get('layoutMode', false);

        if (PermissionUtils::check($this->user, 'block', 'adminblockposition', 'edit')) :
            $this->addToggle('Layout', 'layoutMode',(new Attributes())->setChecked($layoutMode));
        endif;
        if (PermissionUtils::check($this->user, 'block', 'adminblock', 'edit')) :
            $this->addToggle('Editor', 'editorMode',(new Attributes())->setChecked($editorMode));
        endif;

        $this->addToggle('Cache', 'cache', (new Attributes())->setChecked($cache));
    }
}
