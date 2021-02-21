<?php declare(strict_types=1);

namespace VitesseCms\Core;

use Phalcon\Mvc\Application;
use VitesseCms\Content\Services\ContentService;
use VitesseCms\Core\Interfaces\InjectableInterface;

/**
 * @property ContentService $content
 */
class CoreApplicaton extends Application implements InjectableInterface
{

}
