<?php declare(strict_types=1);

namespace VitesseCms\Core\Controllers;

use VitesseCms\Core\AbstractController;
use VitesseCms\Core\AbstractModule;
use VitesseCms\Core\Interfaces\RepositoriesInterface;
use VitesseCms\Core\Services\BeanstalkService;
use VitesseCms\Database\Utils\MongoUtil;
use VitesseCms\User\Models\User;
use Phalcon\Exception;
use \DateTime;

class JobqueueController extends AbstractController implements RepositoriesInterface
{
    public function executeAction(): void
    {
        $this->parseJobs($this->jobQueue);

        $this->disableView();
    }

    public function parseJobs(BeanstalkService $beanstalkService): void
    {
        $job = $beanstalkService->peekReady();
        if($job !== null):
            try {
                $task = $job->getBody();
                $_POST = $task['post'];
                $_REQUEST = $_POST;
                if(isset($task['eventInputs'])) :
                    $this->content->setEventInputs($task['eventInputs']);
                endif;

                $controllerNamespace = 'VitesseCms\\' .
                    str_replace(
                        'Communicationcommunication',
                        'Communication',
                        ucfirst($task['module'])
                    ) .
                    '\\Controllers\\' .
                    ucfirst($task['controller'] . 'Controller');
                /** @var AbstractController $controller */
                $controller = new $controllerNamespace();
                $controller->setIsJobProcess(true);
                $action = $task['action'] . 'Action';
                if (isset($task['userId']) && MongoUtil::isObjectId((string)$task['userId'])) :
                    $controller->user = User::findById($task['userId']);
                endif;

                $moduleNamespace = 'VitesseCms\\' .
                    str_replace(
                        'Communicationcommunication',
                        'Communication',
                        ucfirst($task['module'])
                    ) .'\\Module';
                if(class_exists($moduleNamespace)) :
                    /** @var AbstractModule $module */
                    $module = new $moduleNamespace();
                    $controller->repositories = $module->getRepositories();
                endif;

                ob_start();
                $controller->$action($task['params'][0]);
                $message = ob_get_contents();
                ob_end_clean();

                $jobQueue = $this->repositories->jobQueue->getFirstByJobId((int)$job->getId());
                if ($jobQueue) :
                    $jobQueue->set('published', true)
                        ->set('parsed', (new DateTime())->format('Y-m-d H:i:s'))
                        ->set('message', trim(strip_tags($message)))
                        ->save()
                    ;
                    echo 'Job with id <a href="'.$this->url->getBaseUri().'admin/core/adminjobqueue/edit/'.$jobQueue->getId().'" target="_blank ">'.$jobQueue->getId().'</a> is executed.';
                endif;
                $job->delete();
            } catch (Exception $exception) {
                $jobQueue = $this->repositories->jobQueue->getFirstByJobId((int)$job->getId());
                if ($jobQueue) :
                    $jobQueue->set('message', 'task burried')->save();
                endif;

                $this->mailer->sendMail(
                    'info@craftbeermerchandise.com',
                    'JobQueue failed',
                    $exception->getMessage()
                );
                $job->bury();
            }
        endif;

        echo '<br />JobQueues completed';

        $this->view->disable();
    }
}
