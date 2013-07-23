<?php
namespace Publero\ApiNotificationBundle\Tests\Scheduler;

use Publero\ApiNotificationBundle\Scheduler\IncrementalScheduler;

class NotificationSchedulerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProviderDefaultValues
     */
    public function testConstructDefaultValues()
    {
        $scheduler = new IncrementalScheduler();
        $scheduler->getDiffInSeconds(5);
    }

    public function dataProviderDefaultValues()
    {
        return [
            [0, 0],
            [1, 30],
            [200000, 86400],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetDiffInSeconds($incrementSeconds, $maxSeconds, $attempt, $expectedDiff)
    {
        $scheduler = new IncrementalScheduler($incrementSeconds, $maxSeconds);

        $diff = $scheduler->getDiffInSeconds($attempt);

        $this->assertEquals($expectedDiff, $diff);
    }

    public function dataProvider()
    {
        return [
            [60, 3600, 0, 0],
            [60, 3600, 1, 60],
            [60, 3600, 2, 120],
            [60, 3600, 2000, 3600],
        ];
    }
}
