<?php


namespace Reliefapps\NotificationBundle\Doctrine;


use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Reliefapps\NotificationBundle\Model\DeviceManager as BaseDeviceManager;

class DeviceManager extends BaseDeviceManager
{
    private $objectManager;
    private $class;

    /**
     * Constructor.
     *
     * @param ObjectManager            $om
     * @param string                   $class
     */
    public function __construct(ObjectManager $om, $class)
    {
        $this->objectManager = $om;
        $this->class = $class;
    }

    /**
     *  Get the Doctrine Repository for the Device Entity
     *
     * @return ObjectRepository
     */
    public function getRepository()
    {
        return $this->objectManager->getRepository($this->getClass());
    }

    /**
     * {@inheritdoc}
     */
    public function removeDevice($device)
    {
        $this->objectManager->remove($device);
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        if (false !== strpos($this->class, ':')) {
            $metadata = $this->objectManager->getClassMetadata($this->class);
            $this->class = $metadata->getName();
        }

        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function findDevicesBy(array $criteria)
    {
        return $this->getRepository()->findBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneDeviceBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findDevices()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function reloadDevice($device)
    {
        $this->objectManager->refresh($device);
    }

    /**
     * {@inheritdoc}
     */
    public function updateDevice($device, $andFlush = true)
    {
        $this->objectManager->persist($device);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }
}