<?php

namespace Reliefapps\NotificationBundle\Model;

use Reliefapps\NotificationBundle\Model\DeviceManagerInterface;


abstract class DeviceManager implements DeviceManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function createDevice($uuid, $platform = null)
    {
        $class = $this->getClass();
        return new $class($uuid, $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function findDevicesByPlatforms(array $types)
    {
        $this->findDevicesBy(array('type' => $types));
    }

    /**
     * {@inheritdoc}
     */
    public function findDeviceByUUID($uuid)
    {
        $this->findOneDeviceBy(array('uuid' => $uuid));
    }

    /**
     *  {@inheritdoc}
     */
    public function updateToken($uuid, $token, $platform = null)
    {
        if( null === $device = $this->findDeviceByUUID($uuid)){
            $device = $this->createDevice($uuid, $platform);
        }
        $device->setToken($token);
        $this->updateDevice($device);

        return $device;
    }

}