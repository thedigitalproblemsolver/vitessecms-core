<?php declare(strict_types=1);

namespace VitesseCms\Core;

use Phalcon\Exception;
use Phalcon\Http\Request;
use VitesseCms\Admin\Utils\AdminUtil;
use VitesseCms\Core\Enum\EnvEnum;
use VitesseCms\Core\Services\BootstrapService;
use VitesseCms\Core\Utils\DebugUtil;

require_once __DIR__ . '/Services/BootstrapService.php';
require_once __DIR__ . '/AbstractInjectable.php';
require_once __DIR__ . '/Services/AbstractInjectableService.php';
require_once __DIR__ . '/Services/CacheService.php';
require_once __DIR__ . '/Services/UrlService.php';
require_once __DIR__ . '/../../configuration/src/Services/ConfigServiceInterface.php';
require_once __DIR__ . '/../../configuration/src/Services/ConfigService.php';
require_once __DIR__ . '/Utils/DirectoryUtil.php';
require_once __DIR__ . '/Utils/SystemUtil.php';
require_once __DIR__ . '/Utils/BootstrapUtil.php';
require_once __DIR__ . '/../../configuration/src/Utils/AccountConfigUtil.php';
require_once __DIR__ . '/../../configuration/src/Utils/DomainConfigUtil.php';
require_once __DIR__ . '/Utils/DebugUtil.php';
require_once __DIR__ . '/../../core/src/AbstractEnum.php';
require_once __DIR__ . '/Enum/EnvEnum.php';

$cacheLifeTime = (int)getenv(EnvEnum::CACHE_LIFE_TIME);

$cacheKey = null;
$bootstrap = (new BootstrapService())
    ->setSession()
    ->setCache(
        __DIR__ . '/../../../../cache/' . strtolower((new Request())->getHttpHost()) . '/',
        $cacheLifeTime
    )
    ->setUrl()
    ->loadConfig();

if (
    empty($_POST)
    && empty($_SESSION)
    && (count($_GET) === 0 || isset($_GET['_url']))
    && substr_count($_SERVER['REQUEST_URI'], 'admin') === 0
    && substr_count( $_SERVER['REQUEST_URI'], 'import/index/index') === 0
    && !$bootstrap->getConfiguration()->hasMovedTo()
) :
    $cacheKey = str_replace('/', '_', $_SERVER['REQUEST_URI']);
    $cacheResult = $bootstrap->getCache()->get($cacheKey);
    if ($cacheResult !== null) :
        echo $cacheResult;
        die();
    endif;
endif;

$bootstrap
    ->loaderSystem()
    ->setIsAdmin(AdminUtil::isAdminPage())
    ->database()
    ->setLanguage()
    ->setCookies()
    ->security()
    ->flash()
    ->user()
    ->view()
    ->queue()
    ->setting()
    ->content()
    ->mailer()
    ->shop()
    ->log()
    ->router()
    ->acl()
    ->assets()
    ->block()
    ->form()
    ->search();

$application = $bootstrap->application()->attachListeners();

try {
    if (!$bootstrap->getUser()->hasAdminAccess()) :
        $content = $application->content->parseContent($application->handle()->getContent());
        if ($cacheKey !== null) :
            $application->cache->save($cacheKey, $content);
        endif;

        echo $content;
    else :
        $application->cache->delete($cacheKey);
        echo $application->content->parseContent(
            $application->handle()->getContent(),
            false,
            false
        );
    endif;
} catch (Exception $e) {
    if (DebugUtil::isDev()) :
        var_dump($e->getMessage());
        die();
    endif;
    $application->router->doRedirect($application->url->getBaseUri());
}
