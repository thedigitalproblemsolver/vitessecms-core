<?php

namespace VitesseCms\Core\Factories;

use VitesseCms\Core\Models\JobQueue;

/**
 * Class JobQueueFactory
 */
class JobQueueFactory
{
    /**
     * @param string $name
     * @param string $params
     * @param int $jobId
     * @param string $message
     * @param bool $published
     * @param int|null $delay
     *
     * @return JobQueue
     */
    public static function create(
        string $name,
        string $params,
        int $jobId,
        string $message = '',
        bool $published = true,
        ?int $delay = null
    ): JobQueue {
        $datetime = new \DateTime();
        if($delay) :
            $datetime->modify('+'.$delay.' seconds');
        endif;

        return (new JobQueue())
            ->set('name', $name)
            ->set('params', $params)
            ->set('jobId', $jobId)
            ->set('message', $message)
            ->set('parseDate',$datetime->format('Y-m-d H:i:s'))
            ->set('published', $published);
    }
}
