<?php declare(strict_types=1);

namespace VitesseCms\Core;

use Phalcon\Di\DiInterface;
use Phalcon\Mvc\Dispatcher;
use VitesseCms\Core\Interfaces\ModuleInterface;
use VitesseCms\Core\Interfaces\RepositoryCollectionInterface;

abstract class AbstractModule implements ModuleInterface
{
    public function registerAutoloaders(DiInterface $di = null)
    {
    }

    public function registerServices(DiInterface $di, string $module = null)
    {
        $di->set(
            'dispatcher',
            function () use ($module): Dispatcher {
                $dispatcher = new Dispatcher();
                $dispatcher->setDefaultNamespace('VitesseCms\\' . $module . '\\Controllers');
                $dispatcher->setModuleName($module);

                return $dispatcher;
            }
        );
    }

    public function getRepositories(): ?RepositoryCollectionInterface
    {
        return null;
    }
}
