<?php declare(strict_types=1);

namespace VitesseCms\Core\Forms;

use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Models\Attributes;

class JobQueueForm extends AbstractForm
{

    public function initialize()
    {
        $this->addText('%CORE_NAME%', 'name', (new Attributes())->setReadonly())
            ->addTextarea('Parameters', 'params',(new Attributes())->setReadonly())
            ->addText('Job-id', 'jobId',(new Attributes())->setReadonly())
            ->addText('System message', 'message',(new Attributes())->setReadonly())
            ->addText('Parse date', 'parseDate',(new Attributes())->setReadonly())
            ->addText('Parsed', 'parsed',(new Attributes())->setReadonly()
        );
    }
}
