<?php
namespace Publero\ApiNotificationBundle\Tests\Entity;

use Publero\ApiNotificationBundle\Model\Notification;

class NotificationTest extends \PHPUnit_Framework_TestCase
{
    public function testIncrementSentCount()
    {
        $notification = new Notification();
        $this->assertEquals(0, $notification->getSentCount());

        $notification->incrementSentCount();
        $this->assertEquals(1, $notification->getSentCount());
    }
}
