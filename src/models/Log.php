<?php declare(strict_types=1);

namespace VitesseCms\Core\Models;

use MongoDB\BSON\ObjectId;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\User\Models\User;

class Log extends AbstractCollection
{
    /**
     * @var ObjectId
     */
    public $itemId;

    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $message;

    /**
     * @var ObjectId
     */
    public $userId;

    /**
     * @var string
     */
    public $ipAddress;

    /**
     * @var string
     */
    public $property;

    /**
     * @var string
     */
    public $sourceUri;

    /**
     * @var string
     */
    public $post;

    public function setItemId(ObjectId $itemId): self
    {
        $this->itemId = $itemId;

        return $this;
    }

    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function setUserId(ObjectId $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function setProperty(string $property): self
    {
        $this->property = $property;

        return $this;
    }

    public function setSourceUri(string $sourceUri): self
    {
        $this->sourceUri = $sourceUri;

        return $this;
    }

    public function setPost(string $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function afterFetch()
    {
        $name = [$this->getCreateDate()->format('Y-m-d H:i:s')];
        if ($this->_('userId')) :
            User::setFindPublished(false);
            User::setFindDeletedOn(false);
            $user = User::findById($this->_('userId'));
            if ($user) :
                $name[] = $user->_('email');
            endif;
        endif;
        $name[] = $this->_('message');

        $this->set('name', implode(' - ', $name));

        parent::afterFetch();
    }
}
