<?php
namespace Publero\ApiNotificationBundle\Tests\Notifier;

use Publero\ApiNotificationBundle\Model\Notification;
use Publero\ApiNotificationBundle\Notifier\SimpleNotifier;

class SimpleNotifierTest extends \PHPUnit_Framework_TestCase
{
    const URI = 'http//example.com';
    const CODE = 'test';
    const NOTIFICATION_CLASS = 'Publero\ApiNotificationBundle\Model\Notification';
    /**
     * @var SimpleNotifier
     */
    private $notifier;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $scheduler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $storage;

    public function setUp()
    {
        $this->client = $this->getMock('Publero\ApiNotificationBundle\Client\Client');
        $this->scheduler = $this->getMock('Publero\ApiNotificationBundle\Scheduler\Scheduler');
        $this->storage = $this->getMock('Publero\ApiNotificationBundle\Storage\Storage');

        $this->notifier = new SimpleNotifier($this->client, $this->scheduler, $this->storage, self::CODE, self::URI);
    }

    public function testNotify()
    {
        $id = 'test';
        $data = json_encode(['test2']);

        $this->storage
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(self::NOTIFICATION_CLASS))
        ;
        $notification = $this->notifier->notify($id, $data);

        $this->assertInstanceOf(self::NOTIFICATION_CLASS, $notification);
    }

    public function testSendNotificationsReturnsPositiveNumberEqualsToNotificationCount()
    {
        $notificationCount = 2;
        $notifications = [];

        for ($index = 1; $index <= $notificationCount; $index++) {
            $notification = $this->createNotification('test_notification_' . $index, 'test_data');
            $notifications[] = $notification;
            $this->client
                ->expects($this->at($index - 1))
                ->method('send')
                ->with(
                    $this->equalTo(self::URI),
                    $this->equalTo($notification->getData())
                )
                ->will($this->returnValue(true))
            ;
            $this->storage
                ->expects($this->at($index))
                ->method('remove')
                ->with($this->equalTo($notification))
            ;
        }
        $this->storage
            ->expects($this->once())
            ->method('findByCode')
            ->will(
                $this->returnValue($notifications)
            )
        ;

        $sentCount = $this->notifier->sendNotifications();
        $this->assertEquals($notificationCount, $sentCount);
    }

    public function testSendNotificationsReturnsZeroIfThereAreNoNotifications()
    {
        $this->client
            ->expects($this->never())
            ->method('send')
        ;
        $this->storage
            ->expects($this->once())
            ->method('findByCode')
            ->will(
                $this->returnValue([])
            )
        ;

        $sentCount = $this->notifier->sendNotifications();
        $this->assertEquals(0, $sentCount);
    }

    public function testSendNotification()
    {
        $id = 'id';
        $notification = $this->createNotification($id, self::CODE);
        $this->storage
            ->expects($this->at(0))
            ->method('findOneByIdAndCode')
            ->with(
                $this->equalTo('id'),
                $this->equalTo(self::CODE)
            )
            ->will($this->returnValue($notification))
        ;
        $this->storage
            ->expects($this->once())
            ->method('remove')
            ->with(
                $this->equalTo($notification)
            )
        ;
        $this->client
            ->expects($this->once())
            ->method('send')
            ->with(
                self::URI,
                $this->equalTo($notification->getData())
            )
            ->will($this->returnValue(true))
        ;

        $sendedNotification = $this->notifier->sendNotification('id');

        $this->assertSame($notification, $sendedNotification);

        $this->setExpectedException('Doctrine\ORM\EntityNotFoundException');
        $this->notifier->sendNotification('id');
    }

    public function testSendNotificationSchedulesNotificationIfClientsReturnsFalse()
    {
        $id = 'id';
        $notification = $this->createNotification($id, self::CODE);
        $this->storage
            ->expects($this->at(0))
            ->method('findOneByIdAndCode')
            ->with(
                $this->equalTo('id'),
                $this->equalTo(self::CODE)
            )
            ->will($this->returnValue($notification))
        ;
        $this->client
            ->expects($this->once())
            ->method('send')
            ->with(
                self::URI,
                $this->equalTo($notification->getData())
            )
            ->will($this->returnValue(false))
        ;
        $this->scheduler
            ->expects($this->once())
            ->method('getDiffInSeconds')
            ->with($this->equalTo(1))
            ->will($this->returnValue(5))
        ;
        $this->storage
            ->expects($this->once())
            ->method('persist')
            ->with(
                $this->equalTo($notification)
            )
        ;

        $sendedNotification = $this->notifier->sendNotification('id');
        $this->assertSame($notification, $sendedNotification);

        $dateTime = new \DateTime();
        $this->assertEquals($dateTime, $notification->getSentAt());
        $dateTime->modify('+5 seconds');
        $this->assertEquals($dateTime, $notification->getScheduledAt());
    }

    /**
     * @expectedException Doctrine\ORM\EntityNotFoundException
     */
    public function testSendNotificationThrowsExceptionIfNotificationDoesNotExist()
    {
        $id = 'i_dont_exist';
        $this->storage
            ->expects($this->once())
            ->method('findOneByIdAndCode')
            ->with(
                $this->equalTo($id),
                $this->equalTo(self::CODE)
            )
        ;

        $this->notifier->sendNotification($id);
    }

    /**
     * @param string $id
     * @param string $data
     * @return Notification
     */
    private function createNotification($id, $data)
    {
        $notification = new Notification();
        $notification
            ->setId($id)
            ->setData($data)
            ->setSentAt(new \DateTime())
            ->setSentCount(0)
        ;

        return $notification;
    }
}
