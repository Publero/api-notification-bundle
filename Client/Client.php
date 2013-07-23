<?php
namespace Publero\ApiNotificationBundle\Client;

use Publero\ApiNotificationBundle\Model\Notification;

interface Client
{
    /**
     * @param string $uri
     * @param string $data
     */
    public function send($uri, $data);
}
