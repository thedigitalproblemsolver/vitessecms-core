<?php declare(strict_types=1);

namespace VitesseCms\Core\Interfaces;

interface BaseObjectInterface
{
    public function _(string $key, string $languageShort = null);

    public function set(
        string $key,
        $value,
        bool $multilang = false,
        string $languageShort = null
    ): BaseObjectInterface;

    public function add(
        string $name,
        $value,
        string $key = null,
        bool $multilang = false,
        string $languageShort = null
    );

    public function has(string $key);

    public function bind(array $array);

    public function hasChildren(): bool;

    public function hasParent(): bool;

    public function getParentId(): ?string;
}
