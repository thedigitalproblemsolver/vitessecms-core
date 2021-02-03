<?php declare(strict_types=1);

namespace VitesseCms\Core\Models;

use VitesseCms\Datafield\Repositories\DatafieldRepository;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Core\Factories\DatagroupFactory;
use VitesseCms\Admin\Utils\AdminUtil;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Datafield\AbstractField;

class Datagroup extends AbstractCollection
{
    /**
     * @var array
     */
    protected $excludeFields;

    /**
     * @var array
     */
    public $slugDatafields;

    /**
     * @var array
     */
    public $seoTitleDatafields;

    /**
     * @var array
     */
    public $seoTitleCategories;

    /**
     * @var array
     */
    public $datafields;

    /**
     * @var array
     */
    public $slugCategories;

    /**
     * @var bool
     */
    public $hasFilterableFields;

    /**
     * @var string
     */
    public $slugDelimiter;

    public function onConstruct(): void
    {
        $this->excludeFields = [];
        $this->slugDatafields = [];
        $this->seoTitleDatafields = [];
    }

    public function afterFetch()
    {
        parent::afterFetch();
        if (AdminUtil::isAdminPage()) :
            $this->adminListName = ucfirst($this->_('component')) . ' : ' . $this->getNameField();
        endif;
    }

    public function buildItemForm(AbstractForm $form, AbstractCollection $data = null): void
    {
        $datafieldRepository = new DatafieldRepository();
        foreach ($this->getDatafields() as $fieldId => $params) :
            if ($params['published'] !== false) :
                $datafield = $datafieldRepository->getById($params['id']);
                if ($datafield && !isset($this->excludeFields[$datafield->getCallingName()])) :
                    $class = $datafield->getClass();
                    /** @var AbstractField $field */
                    $field = new $class();
                    if ($datafield->isMultilang() && AdminUtil::isAdminPage()) :
                        $field->setOption('multilang', true);
                    endif;

                    if (!empty($params['required'])) :
                        $field->setOption('required', 'required');
                    endif;

                    if ($data !== null) :
                        $field->setOption('value', $data->_($datafield->_('calling_name')));
                    elseif (isset($datafield->defaultValue)) :
                        $field->setOption('defaultValue', $datafield->defaultValue);
                    endif;

                    $field->buildItemFormElement($form, $datafield, $data);
                endif;
            endif;
        endforeach;

        if ($data !== null && $data->getId()) :
            $form->_('hidden', 'id', 'id', ['value' => $data->getId()]);
        endif;
    }

    public function getAdminlistName(): string
    {
        return ucfirst($this->_('component')) . ' : ' . $this->_('name');
    }

    public function addExcludeField(string $calling_name): void
    {
        $this->excludeFields[$calling_name] = true;
    }

    public function addDatafield(Datafield $datafield): Datagroup
    {
        if (!isset($this->datafields)) :
            $this->datafields = [];
        endif;

        $dataFields = (array)$this->datafields;
        $datafieldId = (string)$datafield->getId();
        if (!isset($dataFields[$datafieldId])) :
            $dataFields[$datafieldId] = DatagroupFactory::createDatafieldEntry($datafield);
            $this->datafields = $dataFields;
        endif;

        return $this;
    }

    public function getDatafields(): array
    {
        return $this->datafields ?? [];
    }

    public function getSlugDatafields(): array
    {
        return $this->slugDatafields ?? [];
    }

    public function setSlugDatafields(array $slugDatafields): Datagroup
    {
        $this->slugDatafields = $slugDatafields;

        return $this;
    }

    public function getSlugCategories(): array
    {
        return $this->slugCategories ?? [];
    }

    public function setSlugCategories(array $slugCategories): Datagroup
    {
        $this->slugCategories = $slugCategories;

        return $this;
    }

    public function getSeoTitleDatafields(): array
    {
        return $this->seoTitleDatafields ?? [];
    }

    public function setSeoTitleDatafields(array $seoTitleDatafields): Datagroup
    {
        $this->seoTitleDatafields = $seoTitleDatafields;

        return $this;
    }

    public function getSeoTitleCategories(): array
    {
        return $this->seoTitleCategories ?? [];
    }

    public function setSeoTitleCategories(array $seoTitleCategories): Datagroup
    {
        $this->seoTitleCategories = $seoTitleCategories;

        return $this;
    }

    public function hasFilterableFields(): bool
    {
        return (bool)$this->hasFilterableFields;
    }

    public function getSlugDelimiter(): string
    {
        return $this->slugDelimiter ?? '';
    }
}
