<?php
namespace Publero\ApiNotificationBundle\Storage;

use Doctrine\Common\Persistence\ObjectManager;
use Publero\ApiNotificationBundle\Model\Notification;

class DoctrineStorage implements Storage
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    private $repository;

    /**
     * @param ObjectManager $manager
     * @param string $entityName
     */
    public function __construct(ObjectManager $manager, $entityName)
    {
        $this->manager = $manager;
        $this->repository = $manager->getRepository($entityName);
    }

    /**
     * @param Notification $notification
     */
    public function persist(Notification $notification)
    {
        $this->manager->persist($notification);
        $this->manager->flush();
    }

    /**
     * @param string $code
     * @return Notification[]
     */
    public function findByCode($code)
    {
        return $this->repository->findBy(['code' => $code]);
    }

    /**
     * @param string $id
     * @param string $code
     * @return Notification[]
     */
    public function findOneByIdAndCode($id, $code)
    {
        return $this->repository->findOneBy(['id' => $id, 'code' => $code]);
    }

    /**
     * @param string $id
     * @param string $code
     */
    public function removeByIdAndCode($id, $code)
    {
        $notification = $this->findOneByIdAndCode($id, $code);

        $this->remove($notification);
    }

    /**
     * @param Notification $notification
     */
    public function remove(Notification $notification)
    {
        $this->manager->remove($notification);
        $this->manager->flush($notification);
    }
}
