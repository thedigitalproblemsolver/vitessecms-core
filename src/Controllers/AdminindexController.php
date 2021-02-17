<?php declare(strict_types=1);

namespace VitesseCms\Core\Controllers;

use VitesseCms\Core\AbstractController;

class AdminindexController extends AbstractController
{

    public function toggleParametersAction(): void
    {
        foreach (['layoutMode', 'editorMode', 'cache'] as $parameter) :
            $this->session->set($parameter, (bool)$this->request->get($parameter));
        endforeach;
        $this->disableView();
    }
}
