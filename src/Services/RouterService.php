<?php declare(strict_types=1);

namespace VitesseCms\Core\Services;

use Phalcon\Http\Request;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Router\RouteInterface;
use VitesseCms\Admin\Utils\AdminUtil;
use VitesseCms\Configuration\Services\ConfigService;
use VitesseCms\Content\Models\Item;
use VitesseCms\Content\Repositories\ItemRepository;
use VitesseCms\Core\Helpers\ItemHelper;
use VitesseCms\Sef\Helpers\SefHelper;
use VitesseCms\Sef\Utils\SefUtil;
use VitesseCms\User\Models\User;
use function strlen;

class RouterService
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ConfigService
     */
    protected $config;

    /**
     * @var array
     */
    protected $urlParts;

    /**
     * @var array
     */
    protected $urlPath;

    /**
     * @var UrlService
     */
    protected $url;

    /**
     * @var CacheService
     */
    protected $cache;

    /**
     * @var ViewService
     */
    protected $view;

    /**
     * @var string
     */
    protected $modulePrefix;

    /**
     * @var ItemRepository
     */
    protected $itemRepository;

    private Router $router;

    public function __construct(
        User           $user,
        Request        $request,
        ConfigService  $config,
        UrlService     $url,
        CacheService   $cache,
        ViewService    $view,
        ItemRepository $itemRepository,
        Router $router
    )
    {
        $this->user = $user;
        $this->request = $request;
        $this->config = $config;
        $this->url = $url;
        $this->cache = $cache;
        $this->view = $view;
        $this->modulePrefix = '';
        $this->itemRepository = $itemRepository;
        $this->router = $router;

        if (substr_count($this->request->getServer('REQUEST_URI'), '//') > 0) :
            $this->doRedirect(str_replace('//', '/', $this->request->getServer('REQUEST_URI')));
        endif;

        $this->urlParts = parse_url($this->request->getServer('REQUEST_URI'));
        $this->urlParts['path'] = str_replace(
            '/' . $this->config->getLanguageShort() . '/',
            '/', $this->urlParts['path']
        );
        $this->urlPath = explode('/', $this->urlParts['path']);

        if (User::count() === 0) :
            $this->setInstallerRoute();
        elseif (AdminUtil::isAdminPage()) :
            $this->setAdminPageRoute();
        else :
            $item = $this->getItemFromSlug();
            if ($item) :
                $this->setItemRoute($item);
            else :
                SefHelper::redirect($this->urlParts['path']);
                $this->setSystemRoute();
            endif;
        endif;
    }

    public function doRedirect(string $location): void
    {
        header('HTTP/1.1 301 Moved Permanently');
        header('X-Robots-Tag: noindex');
        header('Location: ' . $location);
        die();
    }

    protected function setInstallerRoute(): void
    {
        if (count($this->urlPath) === 5) :
            $this->add($this->urlParts['path'],
                [
                    'namespace' => 'VitesseCms\\' . ucfirst(strtolower($this->urlPath[1])) . '\\Controllers',
                    'module' => strtolower($this->urlPath[1]),
                    'controller' => strtolower($this->urlPath[2]),
                    'action' => strtolower($this->urlPath[3]),
                ]
            );
        else :
            $this->setDefaultNamespace('VitesseCms\Install\Controllers');
            $this->setDefaultModule('install');
            $this->setDefaultController('index');
            $this->setDefaultAction('index');
        endif;
    }

    protected function setAdminPageRoute(): void
    {
        if ($this->config->getAccount() === $this->urlPath[2]) :
            $namespace = 'VitesseCms\\' . ucfirst($this->urlPath[2]) . '\\' . ucfirst($this->urlPath[3]) . '\\Controllers';
            $pattern = '/admin/' . $this->config->getAccount() . '/:module/:controller/:action/:params';
        else :
            $namespace = 'VitesseCms\\' . ucfirst($this->urlPath[2]) . '\\Controllers';
            $pattern = '/admin/:module/:controller/:action/:params';
        endif;

        $parts = [
            'namespace' => $namespace,
            'module' => 1,
            'controller' => 2,
            'action' => 3,
            'params' => 4,
        ];
        $this->add($pattern, $parts);
        $this->add('/' . $this->config->getLanguageShort() . $pattern, $parts);
    }

    protected function getItemFromSlug(): ?Item
    {
        $item = null;
        if ($this->urlParts['path'] === '/') :
            $item = $this->itemRepository->getHomePage();
            $this->view->setVar('homepage', true);
        else :
            $item = $this->itemRepository->findBySlug(
                substr($this->urlParts['path'], 1, strlen($this->urlParts['path'])),
                $this->config->getLanguageShort()
            );

            if ($item === null) :
                $item = $this->itemRepository->findBySlug(
                    substr($this->urlParts['path'], 1, strlen($this->urlParts['path'])) . '/',
                    $this->config->getLanguageShort()
                );
                if ($item !== null) :
                    header('HTTP/1.1 301 Moved Permanently');
                    header('Location: ' . $this->url->getBaseUri() . substr($this->urlParts['path'], 1,
                            strlen($this->urlParts['path'])) . '/');
                    die();
                endif;
            endif;
        endif;

        if ($item) {
            return $item;
        }

        return null;
    }

    protected function setItemRoute(Item $item): void
    {
        if (AdminUtil::isAdminPage()) :
            ItemHelper::setEditLink($item, $this->user);
        endif;

        if (
            $this->request->get('embedded') !== null
            && SefUtil::clientIsBot($this->request->getUserAgent())
        ) :
            $this->doRedirect($this->url->getBaseUri() . $item->getSlug());
        endif;

        $parts = [
            'namespace' => 'VitesseCms\\Content\\Controllers',
            'module' => 'content',
            'controller' => 'index',
            'action' => 'index',
        ];

        $this->add($this->urlParts['path'], $parts);
        $this->add($this->urlParts['path'] . $this->config->getLanguageShort() . '/', $parts);
        $this->add('/' . $this->config->getLanguageShort() . $this->urlParts['path'], $parts);

        $this->view->setCurrentId((string)$item->getId());
        ItemHelper::parseBeforeMainContent($item);
        $this->view->setCurrentItem($item);
    }

    public function setSystemRoute(): void
    {
        if ($this->config->getAccount() === $this->urlPath[1]) :
            $namespace = 'VitesseCms\\' . ucfirst($this->urlPath[1]) . '\\' . ucfirst($this->urlPath[2]) . '\\Controllers';
            $this->add(
                '/' . $this->config->getAccount() . '/:module/:controller/:action/:params',
                [
                    'namespace' => $namespace,
                    'module' => 1,
                    'controller' => 2,
                    'action' => 3,
                    'params' => 4,
                ]
            );
            /** @deprecated get rid of $this->>view */
            $this->view->set('aclModulePrefix', $this->config->getAccount() . '\\');
            $this->modulePrefix = $this->config->getAccount() . '\\';
        else :
            switch (substr_count($this->urlParts['path'], '/')) :
                case 2:
                    $parts = [
                        'namespace' => 'VitesseCms\\' . ucfirst(strtolower($this->urlPath[1])) . '\\Controllers',
                        'module' => strtolower($this->urlPath[1]),
                        'controller' => 'index',
                        'action' => strtolower($this->urlPath[2]),
                    ];
                    $this->add($this->urlParts['path'], $parts);
                    $this->add('/' . $this->config->getLanguageShort() . $this->urlParts['path'], $parts);
                    break;
                case 3:
                    $parts = [
                        'namespace' => 'VitesseCms\\' . ucfirst(strtolower($this->urlPath[1])) . '\\Controllers',
                        'module' => strtolower($this->urlPath[1]),
                        'controller' => strtolower($this->urlPath[2]),
                        'action' => strtolower($this->urlPath[3]),
                    ];
                    $this->add($this->urlParts['path'], $parts);
                    $this->add('/' . $this->config->getLanguageShort() . $this->urlParts['path'], $parts);
                    break;
                case 4:
                    $parts = [
                        'namespace' => 'VitesseCms\\' . ucfirst(strtolower($this->urlPath[1])) . '\\Controllers',
                        'module' => strtolower($this->urlPath[1]),
                        'controller' => strtolower($this->urlPath[2]),
                        'action' => strtolower($this->urlPath[3]),
                        'params' => strtolower($this->urlPath[4]),
                    ];
                    $this->add($this->urlParts['path'], $parts);
                    $this->add('/' . $this->config->getLanguageShort() . $this->urlParts['path'], $parts);
                    break;
            endswitch;
        endif;
    }

    public function getParams(): array
    {
        $params = $this->router->getParams();
        if (!empty($this->_defaultParams)) :
            $params = $this->_defaultParams;
        endif;

        return $params;
    }

    public function getModulePrefix(): string
    {
        return $this->modulePrefix;
    }

    public function add(string $pattern, $paths = null): RouteInterface
    {
        return $this->router->add($pattern, $paths);
    }

    public function handle(string $uri): void
    {
        $this->router->handle($uri);
    }

    public function getMatchedRoute(): ?RouteInterface
    {
        return $this->router->getMatchedRoute();
    }

    public function getModuleName(): string
    {
        return $this->router->getModuleName();
    }

    public function getNamespaceName(): string
    {
        return $this->router->getNamespaceName();
    }

    public function getControllerName(): string
    {
        return $this->router->getControllerName();
    }

    public function getActionName(): string
    {
        return $this->router->getActionName();
    }
}
