<?php declare(strict_types=1);

namespace VitesseCms\Core\Interfaces;

use VitesseCms\Core\Repositories\JobQueueRepository;
use VitesseCms\Database\Interfaces\BaseRepositoriesInterface;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\User\Repositories\UserRepository;

/**
 * Interface RepositoryInterface
 * @property JobQueueRepository $jobQueue
 * @property UserRepository $user
 * @property DatagroupRepository $datagroup
 * @property DatafieldRepository $datafield
 */
interface RepositoryInterface extends BaseRepositoriesInterface
{
}
