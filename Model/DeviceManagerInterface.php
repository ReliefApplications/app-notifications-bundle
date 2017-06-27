<?php

namespace Reliefapps\NotificationBundle\Model;

/**
 *  DeviceManagerInterface
 *  This interface define a device manager.
 */
interface DeviceManagerInterface
{
    /**
     *  Create a new Device Entity
     *
     *  @param String $uuid
     *  @param Integer $platform
     *  @return Device
     */
    public function createDevice($uuid, $platform);

    /**
     *  Remove a Device Entity
     *
     *  @param Device
     */
    public function removeDevice($device);

    /**
     *  Get the Device Class name
     *
     *  @return String ClassName
     */
    public function getClass();

    /**
     *  Find all devices meeting the criteria
     *
     *  @param Array $criteria
     *  @return Array[Device]
     */
    public function findDevicesBy(array $criteria);

    /**
     *  Find one device meeting the criteria
     *
     *  @param Array $criteria
     *  @return Device
     */
    public function findOneDeviceBy(array $criteria);

    /**
     *  Find all devices for the given platforms
     *
     *  @param Array[Integer] $types
     *  @return Array[Device]
     */
    public function findDevicesByPlatforms(array $types);

    /**
     *  Find the device with the given UUID
     *
     *  @param String
     *  @return Device
     */
    public function findDeviceByUUID($uuid);

    /**
     *  Find all devices
     *
     *  @return Array[Device]
     */
    public function findDevices();

    /**
     *  Reload the given device entity
     *
     *  @param Device
     */
    public function reloadDevice($device);

    /**
     *  Update the given device entity and flush it (unless $andFlush is false)
     *
     *  @param Device
     *  @param Boolean $andFlush
     */
    public function updateDevice($device, $andFlush = true);
}
