<?php
namespace Publero\ApiNotificationBundle\Notifier;

class NotifierList
{
    /**
     * @var Notifier[]
     */
    private $notifierList = [];

    /**
     * @param string $code
     * @param Notifier $notifier
     * @return self
     */
    public function addNotifier($code, Notifier $notifier)
    {
        $this->notifierList[$code] = $notifier;

        return $this;
    }

    /**
     * @param string $code
     * @throws \OutOfRangeException
     * @retun notifier
     */
    public function getNotifierByCode($code)
    {
        if (isset($this->notifierList[$code])) {
            return $this->notifierList[$code];
        }
        $notifiers = implode(', ', $this->notifierList);

        throw new \OutOfRangeException("Notifier '$code' is not set. Available notifiers: $notifiers");
    }

    /**
     * @return notifier[]
     */
    public function getNotifiers()
    {
        return $this->notifierList;
    }
}
