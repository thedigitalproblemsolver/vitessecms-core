<?php declare(strict_types=1);

namespace VitesseCms\Core\Interfaces;

use VitesseCms\Block\Services\BlockService;
use VitesseCms\Communication\Services\MailchimpService;
use VitesseCms\Communication\Services\MailerService;
use VitesseCms\Content\Models\Item;
use VitesseCms\Content\Services\ContentService;
use VitesseCms\Core\Services\BeanstalkService;
use VitesseCms\Core\Services\CacheService;
use VitesseCms\Configuration\Services\ConfigService;
use VitesseCms\Core\Services\FlashService;
use VitesseCms\Core\Services\LogService;
use VitesseCms\Core\Services\RouterService;
use VitesseCms\Core\Services\UrlService;
use VitesseCms\Core\Services\ViewService;
use VitesseCms\Etsy\Services\EtsyService;
use VitesseCms\Export\Services\ChannelEngineService;
use VitesseCms\Form\Services\FormService;
use VitesseCms\Language\Services\LanguageService;
use VitesseCms\Media\Services\AssetsService;
use VitesseCms\Search\Models\Elasticsearch;
use VitesseCms\Setting\Services\SettingService;
use VitesseCms\Shop\Services\ShopService;
use VitesseCms\User\Models\User;
use VitesseCms\User\Services\AclService;
use Phalcon\Config;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Security;
use Phalcon\Session\Adapter\Files;

/**
 * Class InjectableInterface
 *
 * @property AclService $acl
 * @property Files $session
 * @property User $user
 * @property Config config
 * @property ShopService $shop
 * @property LogService $log
 * @property ConfigService $configuration
 * @property FlashService $flash
 * @property MailerService $mailer
 * @property Response $response
 * @property BeanstalkService $jobQueue
 * @property ContentService $content
 * @property Request $request
 * @property UrlService $url
 * @property ViewService $view
 * @property Security $security
 * @property Item $currentItem
 * @property SettingService $setting
 * @property EtsyService $etsy
 * @property CacheService $cache
 * @property ChannelEngineService $channelEngine
 * @property AssetsService $assets
 * @property MailchimpService $mailchimp
 * @property RouterService $router
 * @property LanguageService $language
 * @property BlockService $block
 * @property FormService $form
 * @property Elasticsearch $search
 */
interface InjectableInterface
{
}
