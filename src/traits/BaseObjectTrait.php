<?php declare(strict_types=1);

namespace VitesseCms\Core\Traits;

use VitesseCms\Core\Interfaces\BaseObjectInterface;
use VitesseCms\Language\Models\Language;
use Phalcon\Di;

trait BaseObjectTrait
{
    /**
     * @var string
     */
    public $parentId;

    /**
     * @var bool
     */
    public $hasChildren;

    public $name;

    public $slug;

    public function _(string $key, string $languageShort = null)
    {
        $return = '';
        $languageDefault = null;
        if (isset($this->$key)) :
            $return = $this->$key;
        endif;

        if ($languageShort === null) :
            $languageShort = Di::getDefault()->get('configuration')->getLanguageShort();
            $languageDefault = Di::getDefault()->get('configuration')->getLanguageShortDefault();
        endif;

        if (
            is_object($return)
            && isset($return->$languageShort)
        ) :
            $return = $return->$languageShort;
        endif;

        if (
            is_array($return)
            && isset($return[$languageShort])
        ) :
            $return = $return[$languageShort];
        elseif (
            is_array($return)
            && isset($return[$languageDefault])
        ) :
            return $return[$languageDefault];
        endif;

        $keyFunction = 'get' . ucfirst($key);
        if (
            !is_bool($return)
            && empty($return)
            && method_exists($this, $keyFunction)
        ) :
            $return = $this->$keyFunction();
        endif;

        return $return;
    }

    public function getRaw($key)
    {
        $return = '';
        if (isset($this->$key)) :
            $return = $this->$key;
        endif;

        return $return;
    }

    public function set(
        string $key,
        $value,
        bool $multilang = false,
        string $languageShort = null
    ): BaseObjectInterface
    {
        if ($multilang) :
            if (!isset($this->$key) || !is_array($this->$key)) :
                $this->$key = [];
            endif;

            if ($languageShort !== null) :
                $this->$key[$languageShort] = $value;
            else :
                Language::setFindPublished(false);
                $languages = Language::findAll();
                foreach ($languages as $language) :
                    $this->$key[$language->_('short')] = $value;
                endforeach;
            endif;
        else :
            $this->$key = $value;
        endif;

        return $this;
    }

    public function add(
        string $name,
        $value,
        string $key = null,
        bool $multilang = false,
        string $languageShort = null
    ): void
    {
        if ($multilang) :
            die('nog te implementeren');
        else:
            if (!isset($this->$name)) :
                $this->$name = [];
            endif;

            if (is_array($this->$name)) :
                $this->$name[$key] = $value;
            endif;
        endif;
    }

    public function has(string $key): bool
    {
        return !empty($this->$key);
    }

    public function bind(array $array): void
    {
        foreach ($array as $key => $value) :
            $this->$key = $value;
        endforeach;
    }

    public function hasChildren(): bool
    {
        return (bool)$this->_('hasChildren');
    }

    public function hasSlug(): bool
    {
        return (bool)$this->_('slug');

    }

    public function hasParent(): bool
    {
        return (bool)$this->_('parentId');
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function setParent(?string $id): self
    {
        $this->set('parentId', $id);

        return $this;
    }

    public function getNameField(string $languageShort = null): string
    {
        if (is_string($this->name)):
            return $this->name;
        endif;

        if (is_array($this->name)):
            if ($languageShort === null) :
                $languageShort = Di::getDefault()->get('configuration')->getLanguageShort();
            endif;

            return $this->name[$languageShort] ?? reset($this->name);
        endif;

        return '';
    }
}
