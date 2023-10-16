<?php
declare(strict_types=1);

namespace VitesseCms\Core\Services;

use Phalcon\Mvc\View;
use Phalcon\Mvc\ViewInterface;
use VitesseCms\Configuration\Services\ConfigService;
use VitesseCms\Content\Models\Item;
use VitesseCms\Core\Utils\DirectoryUtil;

class ViewService implements ViewInterface
{
    protected ConfigService $configuration;

    protected ViewInterface $view;

    protected ?Item $currentItem;

    protected string $currentId;

    protected string $coreTemplateDir;

    protected string $vendorNameDir;

    public function __construct(string $coreTemplateDir, string $vendorNameDir, ViewInterface $view)
    {
        $this->coreTemplateDir = $coreTemplateDir;
        $this->vendorNameDir = $vendorNameDir;
        $this->view = $view;
        $this->currentItem = null;
    }

    /**
     * @deprecated move to view listener
     */
    public function renderTemplate(
        string $template,
        string $templatePath,
        array $params = []
    ): string {
        if (empty($templatePath)):
            $templatePath = $this->coreTemplateDir;
            $template = str_replace($templatePath, '', $template);
        endif;

        if (
            !DirectoryUtil::exists($templatePath)
            && !is_file(
                $this->view->getViewsDir() . $template . '.mustache'
            )
        ) :
            $templatePath = $this->coreTemplateDir . 'views/' . $templatePath;
        endif;

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->render($templatePath, $template, $params);
        $return = $this->view->getContent();
        $this->view->setRenderLevel(View::LEVEL_MAIN_LAYOUT);

        return $return;
    }

    public function getViewsDir()
    {
        return $this->view->getViewsDir();
    }

    public function setRenderLevel(int $level): ViewInterface
    {
        return $this->view->setRenderLevel($level);
    }

    public function render($controllerName, $actionName, $params = [])
    {
        $this->view->render($controllerName, $actionName, $params);
    }

    public function getContent(): string
    {
        return $this->view->getContent();
    }

    public function getCurrentItem(): ?Item
    {
        return $this->currentItem;
    }

    public function setCurrentItem(Item $currentItem): ViewService
    {
        $this->currentItem = $currentItem;
        $this->set('currentItem', $currentItem);

        return $this;
    }

    public function set(string $key, $value): self
    {
        $this->setVar($key, $value);

        return $this;
    }

    public function setVar($key, $value)
    {
        $this->view->setVar($key, $value);
    }

    public function hasCurrentItem(): bool
    {
        return $this->currentItem !== null;
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

    public function setViewsDir($viewsDir)
    {
        $this->view->setViewsDir($viewsDir);
    }

    public function setPartialsDir($partialsDir)
    {
        $this->view->setPartialsDir($partialsDir);
    }

    public function getPartialsDir(): string
    {
        return $this->view->getPartialsDir();
    }

    public function registerEngines(array $engines): void
    {
        $this->view->registerEngines($engines);
    }

    public function getVar(string $key)
    {
        return $this->view->getVar($key);
    }

    public function getVarAsString(string $key): string
    {
        return (string)$this->view->getVar($key);
    }

    public function setLayoutsDir($layoutsDir)
    {
        $this->view->setLayoutsDir($layoutsDir);
    }

    public function getLayoutsDir(): string
    {
        return $this->view->getLayoutsDir();
    }

    public function setBasePath($basePath)
    {
        $this->view->setBasePath($basePath);
    }

    public function getBasePath(): string
    {
        return $this->view->getBasePath();
    }

    public function setMainView($viewPath)
    {
        $this->view->setMainView($viewPath);
    }

    public function getMainView(): string
    {
        return $this->view->getMainView();
    }

    public function setLayout($layout)
    {
        $this->view->setLayout($layout);
    }

    public function getLayout(): string
    {
        return $this->view->getLayout();
    }

    public function setTemplateBefore($templateBefore): void
    {
        $this->view->setTemplateBefore($templateBefore);
    }

    public function cleanTemplateBefore(): void
    {
        $this->view->cleanTemplateBefore();
    }

    public function setTemplateAfter($templateAfter): void
    {
        $this->view->setTemplateAfter($templateAfter);
    }

    public function cleanTemplateAfter()
    {
        $this->view->cleanTemplateAfter();
    }

    public function getControllerName(): string
    {
        return $this->view->getControllerName();
    }

    public function getActionName(): string
    {
        return $this->view->getActionName();
    }

    public function getParams(): array
    {
        return $this->view->getParams();
    }

    public function start()
    {
        $this->view->start();
    }

    public function pick($renderView)
    {
        $this->view->pick($renderView);
    }

    public function finish()
    {
        $this->view->finish();
    }

    public function getActiveRenderPath(): string
    {
        return $this->view->getActiveRenderPath();
    }

    public function disable(): void
    {
        $this->view->disable();
    }

    public function enable(): void
    {
        $this->view->enable();
    }

    public function reset(): void
    {
        $this->view->reset();
    }

    public function isDisabled(): bool
    {
        return $this->view->isDisabled();
    }

    public function setParamToView($key, $value)
    {
        $this->view->setParamToView($key, $value);
    }

    public function getParamsToView(): array
    {
        return $this->view->getParamsToView();
    }

    public function getCache()
    {
        return $this->view->getCache();
    }

    public function cache($options = true)
    {
        $this->view->cache($options);
    }

    public function setContent($content)
    {
        $this->view->setContent($content);
    }

    public function partial($partialPath, $params = null)
    {
        $this->view->partial($partialPath, $params);
    }
}
