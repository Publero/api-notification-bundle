<?php
namespace Publero\ApiNotificationBundle\Tests\Notifier;

use Publero\ApiNotificationBundle\Notifier\NotifierList;

class NotifierListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NotifierList
     */
    private $notifierList;

    /**
     * @var Notifier
     */
    private $notifier;

    public function setUp()
    {
        $this->notifierList = new NotifierList();
        $this->notifier = $this
            ->getMockBuilder('Publero\ApiNotificationBundle\Notifier\Notifier')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    public function testAddNotifier()
    {
        $code = 'test';
        $this->notifierList->addNotifier($code, $this->notifier);

        $this->assertSame($this->notifier, $this->notifierList->getNotifierByCode($code));
        $this->assertEquals(1, count($this->notifierList->getNotifiers()));
    }

    /**
     * @expectedException \OutOfRangeException
     */
    public function testGetNotifierByCodeThrowsExceptionIfNotifierDoesNotExist()
    {
        $this->notifierList->getNotifierByCode('test');
    }

    public function testGetNotifiersReturnsIndexedArrayByCodes()
    {
        $codes = [
            'test',
            'yohoo2'
        ];

        foreach ($codes as $code) {
            $this->notifierList->addNotifier($code, $this->notifier);
        }
        $keys = array_keys($this->notifierList->getNotifiers());

        $this->assertEquals($codes, $keys);
    }
}
