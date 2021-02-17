<?php

namespace VitesseCms\Core\Controllers;

use VitesseCms\Core\AbstractController;
use VitesseCms\Content\Models\Item;
use VitesseCms\Core\Utils\FileUtil;

class IndexController extends AbstractController
{

    /**
     * core action
     */
    public function indexAction(): void
    {
        Item::setFindValue('slug', $_SERVER['REQUEST_URI']);
        $item = Item::findFirst(['slug' => $_SERVER['REQUEST_URI']]);
        if ($item) :
            $this->view->setVar('title', $item->_('name'));
            $this->view->setVar('content', $item->_('bodytext') . FileUtil::getTag($item->_('image')));
        endif;

        $this->prepareView();
    }
}
