<?php declare(strict_types=1);

namespace VitesseCms\Core\Traits;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceInterface;

trait DiInterfaceTrait
{
    public static function getDefault(): ?DiInterface
    {
        // TODO: Implement getDefault() method.
    }

    public static function reset(): void
    {
        // TODO: Implement reset() method.
    }

    public static function setDefault(DiInterface $container): void
    {
        // TODO: Implement setDefault() method.
    }

    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
    }

    public function offsetGet($offset)
    {
        // TODO: Implement offsetGet() method.
    }

    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }

    public function attempt(string $name, $definition, bool $shared = false)
    {
        // TODO: Implement attempt() method.
    }

    public function get(string $name, $parameters = null)
    {
        // TODO: Implement get() method.
    }

    public function getRaw(string $name)
    {
        // TODO: Implement getRaw() method.
    }

    public function getService(string $name): ServiceInterface
    {
        // TODO: Implement getService() method.
    }

    public function getServices(): array
    {
        // TODO: Implement getServices() method.
    }

    public function getShared(string $name, $parameters = null)
    {
        // TODO: Implement getShared() method.
    }

    public function has(string $name): bool
    {
        // TODO: Implement has() method.
    }

    public function remove(string $name): void
    {
        // TODO: Implement remove() method.
    }

    public function set(string $name, $definition, bool $shared = false): ServiceInterface
    {
        // TODO: Implement set() method.
    }

    public function setService(string $name, ServiceInterface $rawDefinition): ServiceInterface
    {
        // TODO: Implement setService() method.
    }

    public function setShared(string $name, $definition): ServiceInterface
    {
        // TODO: Implement setShared() method.
    }
}