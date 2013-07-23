<?php
namespace Publero\ApiNotificationBundle\Notifier;

use Doctrine\ORM\EntityNotFoundException;
use Publero\ApiNotificationBundle\Client\Client;
use Publero\ApiNotificationBundle\Model\Notification;
use Publero\ApiNotificationBundle\Scheduler\Scheduler;
use Publero\ApiNotificationBundle\Storage\Storage;

class SimpleNotifier implements Notifier
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Scheduler
     */
    private $scheduler;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $uri;

    public function __construct(Client $client, Scheduler $scheduler, Storage $storage, $code, $uri)
    {
        $this->client = $client;
        $this->scheduler = $scheduler;
        $this->storage = $storage;
        $this->code = $code;
        $this->uri = $uri;
    }

    public function notify($id, $data)
    {
        $notification = $this->storage->findOneByIdAndCode($id, $this->code);
        if ($notification === null) {
            $notification = new Notification();
            $notification
                ->setId($id)
                ->setCode($this->code)
            ;
        } else {
            $notification
                ->setSentAt(null)
                ->setSentCount(0)
            ;
        }

        $notification->setData($data);
        $this->storage->persist($notification);

        return $notification;
    }

    /**
     * @return int
     */
    public function sendNotifications()
    {
        $notifications = $this->storage->findByCode($this->code);
        $sentNotifications = 0;
        foreach ($notifications as $notification) {
            $sent = $this->send($notification);
            $sentNotifications += $sent;
        }

        return $sentNotifications;
    }

    /**
     * @param string $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Notification
     */
    public function sendNotification($id)
    {
        $notification = $this->storage->findOneByIdAndCode($id, $this->code);
        if ($notification === null) {
            throw new EntityNotFoundException();
        }

        $this->send($notification);

        return $notification;
    }

    /**
     * @param Notification $notification
     * @return bool
     */
    private function send(Notification $notification)
    {
        if ($notification->getScheduledAt() > new \DateTime()) {
            return false;
        }

        $received = $this->client->send($this->uri, $notification->getData());

        if ($received) {
            $this->storage->remove($notification);
        } else {
            $this->scheduleNotification($notification);
        }

        return true;
    }

    private function scheduleNotification(Notification $notification)
    {
        $notification->incrementSentCount();

        $seconds = $this->scheduler->getDiffInSeconds($notification->getSentCount());
        $notification
            ->setSentAt(new \DateTime())
            ->setScheduledAt((new \DateTime())->modify("+$seconds seconds"))
        ;

        $this->storage->persist($notification);
    }
}
