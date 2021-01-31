<?php declare(strict_types=1);

namespace VitesseCms\Core;

use VitesseCms\Content\Services\ContentService;
use VitesseCms\Core\Interfaces\InjectableInterface;
use Phalcon\Mvc\Application;

/**
 * @property ContentService $content
 */
class CoreApplicaton extends Application implements InjectableInterface
{

}
