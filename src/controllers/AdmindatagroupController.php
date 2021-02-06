<?php declare(strict_types=1);

namespace VitesseCms\Core\Controllers;

use VitesseCms\Admin\AbstractAdminController;
use VitesseCms\Core\Interfaces\RepositoriesInterface;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Datagroup\Helpers\DatagroupHelper;
use VitesseCms\Core\Helpers\Item;
use VitesseCms\Core\Helpers\ItemHelper;
use VitesseCms\Datagroup\Forms\DataGroupForm;
use VitesseCms\Datagroup\Models\Datagroup;
use VitesseCms\Core\Utils\ArrayUtil;

class AdmindatagroupController extends AbstractAdminController implements RepositoriesInterface
{
    public function onConstruct(): void
    {
        parent::onConstruct();

        $this->class = Datagroup::class;
        $this->classForm = DataGroupForm::class;
        $this->listSortable = true;
        $this->listNestable = true;
    }

    public function getSlugCategories(AbstractCollection $datagroup): array
    {
        $slugCategories = $slugTmpCategories = $slugRows = [];
        if ($datagroup->_('slugCategories')) :
            $slugTmpCategories = (array)$datagroup->_('slugCategories');
        endif;

        $DatagroupPath = DatagroupHelper::getPathFromRoot($datagroup);
        /** @var Datagroup $DatagroupItem */
        foreach ($DatagroupPath as $DatagroupItem) :
            $id = (string)$DatagroupItem->getId();
            if (!isset($slugTmpCategories[$id])) :
                $slugCategories[$id] = new \stdClass();
                $slugCategories[$id]->id = $id;
                $slugCategories[$id]->published = false;
            else :
                $slugCategories[$id] = new \stdClass();
                $slugCategories[$id]->id = $id;
                $slugCategories[$id]->published = $slugTmpCategories[$id]['published'];
            endif;
        endforeach;

        foreach ($slugCategories as $key => $slugCategorie) :
            $slugCategorie = (array)$slugCategorie;
            $model = Datagroup::findById($slugCategorie['id']);
            if (\is_object($model) && (string)$datagroup->getId() !== $slugCategorie['id']) :
                if (!isset($slugCategorie['published'])) :
                    $slugCategorie['published'] = false;
                endif;

                $row = new \stdClass();
                $row->rowState = ItemHelper::getRowStateClass($slugCategorie['published']);
                $row->name = $model->_('name');
                $row->fieldId = $slugCategorie['id'];
                $row->key = $key;
                $row->buttons = [
                    [
                        'text'   => ItemHelper::getPublishText($slugCategorie['published']),
                        'icon'   => ItemHelper::getPublishIcon($slugCategorie['published']),
                        'action' => 'togglePublishSlugCategory',
                        'rowId'  => 'publish_slug' . $slugCategorie['id'],
                    ],
                ];

                $slugRows[] = $row;
            endif;
        endforeach;

        return $slugRows;
    }

    public function getSeoTitleCategories(AbstractCollection $datagroup): array
    {
        $slugCategories = $slugTmpCategories = $slugRows = [];
        if ($datagroup->_('seoTitleCategories')) :
            $slugTmpCategories = (array)$datagroup->_('seoTitleCategories');
        endif;
        $DatagroupPath = DatagroupHelper::getPathFromRoot($datagroup);
        /** @var Datagroup $DatagroupItem */
        foreach ($DatagroupPath as $DatagroupItem) :
            $id = (string)$DatagroupItem->getId();
            if (!isset($slugTmpCategories[$id])) :
                $slugCategories[$id] = new \stdClass();
                $slugCategories[$id]->id = $id;
                $slugCategories[$id]->published = false;
            else :
                $slugCategories[$id] = new \stdClass();
                $slugCategories[$id]->id = $id;
                $slugCategories[$id]->published = $slugTmpCategories[$id]['published'];
            endif;
        endforeach;

        foreach ($slugCategories as $key => $slugCategorie) :
            $slugCategorie = (array)$slugCategorie;
            $model = Datagroup::findById($slugCategorie['id']);
            if (\is_object($model) && (string)$datagroup->getId() !== $slugCategorie['id']) :
                if (!isset($slugCategorie['published'])) :
                    $slugCategorie['published'] = false;
                endif;

                $row = new \stdClass();
                $row->rowState = ItemHelper::getRowStateClass($slugCategorie['published']);
                $row->name = $model->_('name');
                $row->fieldId = $slugCategorie['id'];
                $row->key = $key;
                $row->buttons = [
                    [
                        'text'   => ItemHelper::getPublishText($slugCategorie['published']),
                        'icon'   => ItemHelper::getPublishIcon($slugCategorie['published']),
                        'action' => 'togglePublishSeoTitleCategory',
                        'rowId'  => 'publish_seoTitle' . $slugCategorie['id'],
                    ],
                ];

                $slugRows[] = $row;
            endif;
        endforeach;

        return $slugRows;
    }

    public function beforeSave(AbstractCollection $item)
    {
        if (!isset($item->datafields)) :
            $item->datafields = [];
        endif;

        $dataFields = (array)$item->datafields;
        if (
            $this->request->getPost('datafield')
            && !isset($dataFields[(string)$this->request->getPost('datafield')])
        ) :
            $dataFields[$this->request->getPost('datafield')] = [
                'id'         => $this->request->getPost('datafield'),
                'published'  => false,
                'required'   => false,
                'filterable' => false,
            ];
            $item->datafields = $dataFields;
        endif;

        $_POST['datafield'] = null;
    }

    public function deletedatafieldAction(): void
    {
        $item = Datagroup::findById($this->dispatcher->getParam(0));
        $dataFields = (array)$item->_('datafields');
        if (isset($dataFields[$this->request->get('key')])) :
            unset($dataFields[$this->request->get('key')]);
            $item->set('datafields', $dataFields)->save();
            $this->flash->setSucces('ADMIN_ITEM_DELETED', [$item->_('name')]);
        else :
            $this->flash->setError('ADMIN_ITEM_DELETED_FAILED');
        endif;

        $this->redirect();
    }

    public function togglePublishDatafieldAction(): void
    {
        $this->toggle('published');

        $this->redirect();
    }

    public function toggleRequiredDatafieldAction(): void
    {
        $this->toggle('required');

        $this->redirect();
    }

    public function toggleFilterableDatafieldAction(): void
    {
        $datagroup = $this->toggle('filterable');
        $datagroup->set('hasFilterableFields', DatagroupHelper::hasFilterableFields($datagroup))
            ->save()
        ;

        $this->redirect();
    }

    public function togglePublishSlugAction(): void
    {
        Datagroup::setFindPublished(false);
        $item = Datagroup::findById($this->dispatcher->getParam(0));
        $slugDatafields = (array)$item->_('slugDatafields');
        if (!isset($slugDatafields[$this->request->get('key')])) :
            if (isset($slugDatafields[0])) :
                unset($slugDatafields[0]);
            endif;
            $slugDatafields[$this->request->get('key')] = new \stdClass();
            $slugDatafields[$this->request->get('key')]->id = $this->request->get('key');
            $slugDatafields[$this->request->get('key')]->published = false;

            $item->set('slugDatafields', $slugDatafields)->save();
        endif;

        $this->toggle('published', 'slugDatafields');

        $this->redirect();
    }

    public function togglePublishSeoTitleAction(): void
    {
        Datagroup::setFindPublished(false);
        $item = Datagroup::findById($this->dispatcher->getParam(0));
        $slugDatafields = (array)$item->_('seoTitleDatafields');
        if (!isset($slugDatafields[$this->request->get('key')])) :
            if (isset($slugDatafields[0])) :
                unset($slugDatafields[0]);
            endif;
            $slugDatafields[$this->request->get('key')] = new \stdClass();
            $slugDatafields[$this->request->get('key')]->id = $this->request->get('key');
            $slugDatafields[$this->request->get('key')]->published = false;

            $item->set('seoTitleDatafields', $slugDatafields)->save();
        endif;

        $this->toggle('published', 'seoTitleDatafields');

        $this->redirect();
    }

    public function togglePublishSeoTitleCategoryAction(): void
    {
        Datagroup::setFindPublished(false);
        $item = Datagroup::findById($this->dispatcher->getParam(0));
        $slugDatafields = (array)$item->_('seoTitleCategories');
        if (!isset($slugDatafields[$this->request->get('key')])) :
            if (isset($slugDatafields[0])) :
                unset($slugDatafields[0]);
            endif;
            $slugDatafields[$this->request->get('key')] = new \stdClass();
            $slugDatafields[$this->request->get('key')]->id = $this->request->get('key');
            $slugDatafields[$this->request->get('key')]->published = false;

            $item->set('seoTitleCategories', $slugDatafields)->save();
        endif;

        $this->toggle('published', 'seoTitleCategories');

        $this->redirect();
    }

    public function togglePublishSlugCategoryAction(): void
    {
        Datagroup::setFindPublished(false);
        $item = Datagroup::findById($this->dispatcher->getParam(0));
        $slugDatafields = (array)$item->_('slugCategories');
        if (!isset($slugDatafields[$this->request->get('key')])) :
            if (isset($slugDatafields[0])) :
                unset($slugDatafields[0]);
            endif;
            $slugDatafields[$this->request->get('key')] = new \stdClass();
            $slugDatafields[$this->request->get('key')]->id = $this->request->get('key');
            $slugDatafields[$this->request->get('key')]->published = false;

            $item->set('slugCategories', $slugDatafields)->save();
        endif;

        $this->toggle('published', 'slugCategories');

        $this->redirect();
    }

    public function toggleExportableDatafieldAction(): void
    {
        $this->toggle('exportable');

        $this->redirect();
    }

    public function saveDataFieldSortingAction(): void
    {
        $this->saveFieldsSorting('datafields');
    }

    public function saveSlugSortingAction(): void
    {
        $this->saveFieldsSorting('slugDatafields');
    }

    public function saveSeoTitleSortingAction(): void
    {
        $this->saveFieldsSorting('seoTitleCategories');
    }

    protected function saveFieldsSorting(string $fieldName): void
    {
        Datagroup::setFindPublished(false);
        $item = Datagroup::findById($this->dispatcher->getParam(0));
        $keyOrder = $this->request->getPost('order');

        if (isset($item->$fieldName) && \count($item->$fieldName) > 0) :
            $dataFields = (array)$item->$fieldName;
            $dataFields = ArrayUtil::sortArrayByArray($dataFields, $keyOrder, 'slug');
        else :
            $dataFields = [];
            foreach ((array)$keyOrder as $key => $id) :
                $dataFields[$id] = new \stdClass();
                $dataFields[$id]->id = $id;
                $dataFields[$id]->published = false;
            endforeach;
        endif;
        $item->set($fieldName, $dataFields)->save();

        $this->flash->setSucces('ADMIN_SEQUENCE_SAVED');

        $this->redirect();
    }

    protected function toggle(string $fieldName, string $collection = 'datafields'): Datagroup
    {
        Datagroup::setFindPublished(false);
        $datagroup = Datagroup::findById($this->dispatcher->getParam(0));
        $dataFields = $datagroup->_($collection);
        if (isset($dataFields[$this->request->get('key')])) :
            if (
                isset($dataFields[$this->request->get('key')][$fieldName])
                && $dataFields[$this->request->get('key')][$fieldName] === true
            ) :
                $dataFields[$this->request->get('key')][$fieldName] = false;
            else :
                $dataFields[$this->request->get('key')][$fieldName] = true;
            endif;

            $datagroup->set($collection, $dataFields)->save();

            $this->flash->setSucces('ADMIN_STATE_CHANGE_SUCCESS', [ucfirst($fieldName)]);
        else :
            $this->flash->setError('ADMIN_STATE_CHANGE_FAILED', [ucfirst($fieldName)]);
        endif;

        return $datagroup;
    }
}
