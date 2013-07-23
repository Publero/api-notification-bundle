<?php
namespace Publero\ApiNotificationBundle\Storage;

use Publero\ApiNotificationBundle\Model\Notification;

interface Storage
{
    /**
     * @param Notification $notification
     */
    public function persist(Notification $notification);

    /**
     * @param string $code
     * @return Notification[]
     */
    public function findByCode($code);

    /**
     * @param string $id
     * @param string $code
     * @return Notification
     */
    public function findOneByIdAndCode($id, $code);

    /**
     * @param Notification $notification
     */
    public function remove(Notification $notification);

    /**
     * @param string $id
     * @param string $code
     */
    public function removeByIdAndCode($id, $code);
}
