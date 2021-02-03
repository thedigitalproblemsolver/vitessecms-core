<?php declare(strict_types=1);

namespace VitesseCms\Core;

use VitesseCms\Block\Repositories\BlockRepository;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Core\Repositories\DatagroupRepository;
use VitesseCms\Core\Repositories\JobQueueRepository;
use VitesseCms\Core\Repositories\RepositoryCollection;
use VitesseCms\User\Repositories\UserRepository;
use Phalcon\DiInterface;

class Module extends AbstractModule
{
    public function registerServices(DiInterface $di, string $string = null)
    {
        parent::registerServices($di, 'Core');
        $di->setShared('repositories', new RepositoryCollection(
            new JobQueueRepository(),
            new UserRepository(),
            new DatagroupRepository(),
            new DatafieldRepository()
        ));
    }
}
