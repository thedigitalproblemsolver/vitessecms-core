<?php declare(strict_types=1);

namespace VitesseCms\Core\Repositories;

use VitesseCms\Core\Interfaces\RepositoryInterface;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\User\Repositories\UserRepository;

class RepositoryCollection implements RepositoryInterface
{
    /**
     * @var JobQueueRepository
     */
    public $jobQueue;

    /**
     * @var UserRepository
     */
    public $user;

    /**
     * @var DatagroupRepository
     */
    public $datagroup;

    /**
     * @var DatafieldRepository
     */
    public $datafield;

    public function __construct(
        JobQueueRepository $jobQueueRepository,
        UserRepository $userRepository,
        DatagroupRepository $datagroupRepository,
        DatafieldRepository $datafieldRepository
    ) {
        $this->jobQueue = $jobQueueRepository;
        $this->user = $userRepository;
        $this->datagroup = $datagroupRepository;
        $this->datafield = $datafieldRepository;
    }
}
