<?php
namespace Publero\ApiNotificationBundle\Tests\Client;

use Publero\ApiNotificationBundle\Client\BuzzClient;

class BuzzClientTest extends \PHPUnit_Framework_TestCase
{
    public function testSend()
    {
        $data = 'something';
        $uri = 'test';

        $response = $this->getMock('Buzz\Message\Response');
        $response
            ->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(200))
        ;

        $browser = $this->getMock('Buzz\Browser');
        $browser
            ->expects($this->once())
            ->method('put')
            ->with(
                $this->equalTo($uri),
                $this->equalTo([]),
                $this->equalTo($data)
            )
            ->will($this->returnValue($response))
        ;

        $notifier = new BuzzClient($browser);
        $sent = $notifier->send($uri, $data);

        $this->assertTrue($sent);
    }

    public function testSendReturnFalseIfStatusCodeIsNotInSuccessInterval()
    {
        $data = 'something';
        $uri = 'test';

        $response = $this->getMock('Buzz\Message\Response');
        $response
            ->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(300))
        ;

        $browser = $this->getMock('Buzz\Browser');
        $browser
            ->expects($this->once())
            ->method('put')
            ->will($this->returnValue($response))
        ;

        $notifier = new BuzzClient($browser);
        $sent = $notifier->send($uri, $data);

        $this->assertFalse($sent);
    }
}
