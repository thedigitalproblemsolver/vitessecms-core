<?php declare(strict_types=1);

namespace VitesseCms\Core\Services;

use VitesseCms\Configuration\Services\ConfigService;
use VitesseCms\Content\Models\Item;
use VitesseCms\Core\Utils\DirectoryUtil;
use Phalcon\Mvc\View;

class ViewService extends View
{
    /**
     * @var ConfigService
     */
    protected $configuration;

    /**
     * @var Item
     */
    protected $currentItem;

    /**
     * @var string
     */
    protected $currentId;

    public function __construct(ConfigService $configService, array $options = [])
    {
        parent::__construct($options);
        $this->configuration = $configService;
    }

    public function renderTemplate(
        string $template,
        string $templatePath,
        array $params = []
    ): string {
        $search = ['default/','templates/'];
        $replace = ['core/','template/'];
        $template = str_replace($search,$replace,$template);
        $templatePath = str_replace($search,$replace,$templatePath);

        if (empty($templatePath)):
            $templatePath = $this->configuration->getCoreTemplateDir().'../../';
        endif;

        if (
            !DirectoryUtil::exists($templatePath)
            && !is_file(
                $this->getViewsDir().$template.'.mustache'
            )
        ) :
            $templatePath = $this->configuration->getCoreTemplateDir().'views/'.$templatePath;
        endif;

        $this->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->render($templatePath, $template, $params);
        $return = $this->getContent();
        $this->setRenderLevel(View::LEVEL_MAIN_LAYOUT);

        return $return;
    }

    public function renderModuleTemplate(
        string $module,
        string $template,
        string $templatePath,
        array $params = []
    ): string {
        $newTemplatePath = $this->configuration->getSystemDir(). $module. '/resources/views/'. $templatePath;
        if(!is_dir($newTemplatePath)) :
            $newTemplatePath = $this->configuration->getVendorNameDir().$module.'/src/resources/views/'.$templatePath;
        endif;

        return $this->renderTemplate($template, $newTemplatePath, $params);
    }

    public function set(string $key, $value): self
    {
        $this->setVar($key, $value);

        return $this;
    }

    public function getCurrentItem(): Item
    {
        return $this->currentItem;
    }

    public function hasCurrentItem(): bool
    {
        return $this->currentItem !== null;
    }

    public function setCurrentItem(Item $currentItem): ViewService
    {
        $this->currentItem = $currentItem;
        $this->set('currentItem', $currentItem);

        return $this;
    }

    public function getCurrentId(): string
    {
        return $this->currentId;
    }

    public function setCurrentId(string $currentId): ViewService
    {
        $this->currentId = $currentId;
        $this->set('currentId', $currentId);

        return $this;
    }
}
