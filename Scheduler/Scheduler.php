<?php
namespace Publero\ApiNotificationBundle\Scheduler;

interface Scheduler
{
    /**
     * @param int $attempt
     * @return int
     */
    public function getDiffInSeconds($attempt);
}
