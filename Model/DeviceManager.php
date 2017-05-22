<?php

namespace Reliefapps\NotificationBundle\Model;

use Reliefapps\NotificationBundle\Model\DeviceManagerInterface;


abstract class DeviceManager implements DeviceManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function createDevice($uuid, $platform)
    {
        $class = $this->getClass();
        return new $class($uuid, $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function findDevicesByPlatforms(array $types)
    {
        return $this->findDevicesBy(array('type' => $types));
    }

    /**
     * {@inheritdoc}
     */
    public function findDeviceByUUID($uuid)
    {
        return $this->findOneDeviceBy(array('uuid' => $uuid));
    }

}