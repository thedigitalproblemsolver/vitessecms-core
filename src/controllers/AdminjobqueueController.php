<?php

namespace VitesseCms\Core\Controllers;

use VitesseCms\Admin\AbstractAdminController;
use VitesseCms\Core\Forms\JobQueueForm;
use VitesseCms\Core\Models\JobQueue;

/**
 * Class AdminlanguageController
 */
class AdminjobqueueController extends AbstractAdminController
{
    /**
     * onConstruct
     * @throws \Phalcon\Mvc\Collection\Exception
     */
    public function onConstruct()
    {
        parent::onConstruct();

        $this->class = JobQueue::class;
        $this->classForm = JobQueueForm::class;
        $this->listOrder = 'createdAt';
        $this->listOrderDirection = -1;
    }
}
