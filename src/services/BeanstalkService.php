<?php declare(strict_types=1);

namespace VitesseCms\Core\Services;

use VitesseCms\Core\AbstractController;
use VitesseCms\Core\Factories\JobQueueFactory;
use VitesseCms\User\Models\User;
use Phalcon\Http\RequestInterface;
use Phalcon\Mvc\User\Component;
use Phalcon\Queue\Beanstalk;
use Phalcon\Queue\Beanstalk\Job;

class BeanstalkService extends Beanstalk
{
    public function createByController(AbstractController $controller): int
    {
        $router = $controller->router;
        $user = $controller->user;
        $request = $controller->request;

        return $this->create($router, $request, $user);
    }

    public function create(
        RouterService $router,
        RequestInterface $request,
        ?User $user = null,
        array $jobOptions = []
    ): int {
        $userId = null;
        if ($user) :
            $userId = $user->getId();
        endif;
        $data = [
            'module'      => $router->getModuleName(),
            'controller'  => $router->getControllerName(),
            'action'      => $router->getActionName(),
            'params'      => $router->getParams(),
            'userId'      => $userId,
            'post'        => $request->getPost(),
            'eventInputs' => (new Component())->content->getEventInputs(),
        ];
        $delay = $jobOptions['delay'] ?? null;

        $jobId = $this->put($data, $jobOptions);

        JobQueueFactory::create(
            $router->getModuleName().
            '/'.$router->getControllerName().
            '/'.$router->getActionName(),
            serialize($router->getParams()),
            $jobId,
            '',
            false,
            $delay
        )->save();

        return $jobId;
    }

    public function peekReady(): ?Job
    {
        return parent::peekReady() ?: null;
    }
}
