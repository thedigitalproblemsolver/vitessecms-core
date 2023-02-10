<?php declare(strict_types=1);

namespace VitesseCms\Core\Listeners\Services;

use Phalcon\Encryption\Security;
use Phalcon\Events\Event;

class SecurityServiceListener
{
    private Security $securityService;

    public function __construct(Security $securityService)
    {
        $this->securityService = $securityService;
    }

    public function attach( Event $event): Security
    {
        return $this->securityService;
    }
}
