<?php
namespace Publero\ApiNotificationBundle\Scheduler;

class IncrementalScheduler implements Scheduler
{
    /**
     * @var int
     */
    private $incrementSeconds;

    /**
     * @var int
     */
    private $maxSeconds;

    /**
     * @param int $incrementSeconds
     * @param int $maxSeconds
     */
    public function __construct($incrementSeconds = 30, $maxSeconds = 86400)
    {
        $this->incrementSeconds = $incrementSeconds;
        $this->maxSeconds = $maxSeconds;
    }

    /**
     * @param int $attempt
     * @return int
     */
    public function getDiffInSeconds($attempt)
    {
        $diff = $attempt * $this->incrementSeconds;
        if ($diff > $this->maxSeconds) {
            return $this->maxSeconds;
        }

        return $diff;
    }
}
