<?php declare(strict_types=1);

namespace VitesseCms\Core\Repositories;

use VitesseCms\Core\Models\JobQueue;

class JobQueueRepository
{
    public function getFirstByJobId(int $jobId): ?JobQueue
    {
        JobQueue::setFindValue('jobId', $jobId);
        JobQueue::setFindPublished(false);
        /** @var JobQueue $jobQueue */
        $jobQueue = JobQueue::findFirst();
        if(is_object($jobQueue)):
            return $jobQueue;
        endif;

        return null;
    }
}
