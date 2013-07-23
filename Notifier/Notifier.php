<?php
namespace Publero\ApiNotificationBundle\Notifier;

use Publero\ApiNotificationBundle\Client\Client;
use Publero\ApiNotificationBundle\Scheduler\Scheduler;
use Publero\ApiNotificationBundle\Storage\Storage;

interface Notifier
{
    /**
     * @param Client $client
     * @param Scheduler $scheduler
     * @param Storage $storage
     * @param string $code
     * @param string $uri
     */
    public function __construct(Client $client, Scheduler $scheduler, Storage $storage, $code, $uri);

    /**
     * @param string $id
     * @param string $data
     * @return Notification
     */
    public function notify($id, $data);

    /**
     * @return int
     */
    public function sendNotifications();

    /**
     * @param string $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return Notification
     */
    public function sendNotification($id);
}
