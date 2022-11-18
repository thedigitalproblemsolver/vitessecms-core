<?php declare(strict_types=1);

namespace VitesseCms\Core;

use Phalcon\Di\DiInterface;
use VitesseCms\Core\Repositories\RepositoryCollection;
use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;
use VitesseCms\User\Repositories\UserRepository;

class Module extends AbstractModule
{
    public function registerServices(DiInterface $di, string $string = null)
    {
        parent::registerServices($di, 'Core');
        $di->setShared('repositories', new RepositoryCollection(
            new UserRepository(),
            new DatagroupRepository(),
            new DatafieldRepository()
        ));
    }
}
