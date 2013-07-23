<?php
namespace Publero\ApiNotificationBundle\Model;

class Notification
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $code;

    /**
     * @var array
     */
    private $data;

    /**
     * @var \DateTime
     */
    private $sentAt = null;

    /**
     * @var \DateTime
     */
    private $scheduledAt;

    /**
     * @var int
     */
    private $sentCount = 0;

    public function __construct()
    {
        $this->scheduledAt = new \DateTime();
    }

    /**
     * @param string $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $code
     * @return self
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $data
     * @return self
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \DateTime $sentAt
     * @return self
     */
    public function setSentAt(\DateTime $sentAt = null)
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    /**s
     * @return \DateTime
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * @param \DateTime $scheduledAt
     * @return self
     */
    public function setScheduledAt(\DateTime $scheduledAt = null)
    {
        $this->scheduledAt = $scheduledAt;

        return $this;
    }

    /**s
     * @return \DateTime
     */
    public function getScheduledAt()
    {
        return $this->scheduledAt;
    }

    /**
     * @return self
     */
    public function incrementSentCount()
    {
        $this->sentCount++;

        return $this;
    }

    /**
     * @param int $sentCount
     * @return self
     */
    public function setSentCount($sentCount)
    {
        $this->sentCount = $sentCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getSentCount()
    {
        return $this->sentCount;
    }
}
