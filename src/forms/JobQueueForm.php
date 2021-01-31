<?php

namespace VitesseCms\Core\Forms;

use VitesseCms\Form\AbstractForm;

/**
 * Class JobQueueForm
 */
class JobQueueForm extends AbstractForm
{

    public function initialize()
    {
        $this->_(
            'text',
            '%CORE_NAME%',
            'name',
            [
                'required' => 'required',
                'readonly' => true
            ]
        )->_(
            'textarea',
            'Parameters',
            'params',
            ['readonly' => true]
        )->_(
            'text',
            'Job-id',
            'jobId',
            [
                'required' => 'required',
                'readonly' => true
            ]
        )->_(
            'text',
            'System message',
            'message',
            ['readonly' => true]
        )->_(
            'text',
            'Parse date',
            'parseDate',
            ['readonly' => true]
        )->_(
            'text',
            'Parsed',
            'parsed',
            ['readonly' => true]
        );
    }
}
