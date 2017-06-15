<?php

namespace Reliefapps\NotificationBundle\Model;


/**
 *  Context
 *
 *  A context stores information about how to send the notifications
 */
class Context
{
    private $name;

    private $android_server_key;

    private $android_gcm_server;

    private $ios_push_certificate;

    private $ios_push_passphrase;

    private $ios_protocol;

    private $ios_apns_server;

    private $ios_apns_topic;

    public function __construct($name, $android_server_key, $android_gcm_server, $ios_push_certificate, $ios_push_passphrase, $ios_protocol, $ios_apns_server, $ios_apns_topic)
    {
        $this->name = $name;
        $this->android_server_key = $android_server_key;
        $this->android_gcm_server = $android_gcm_server;
        $this->ios_push_certificate = $ios_push_certificate;
        $this->ios_push_passphrase = $ios_push_passphrase;
        $this->ios_protocol = $ios_protocol;
        $this->ios_apns_server = $ios_apns_server;
        $this->ios_apns_topic = $ios_apns_topic;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAndroidServerKey()
    {
        return $this->android_server_key;
    }

    public function getAndroidGcmServer()
    {
        return $this->android_gcm_server;
    }

    public function getIosPushCertificate()
    {
        return $this->ios_push_certificate;
    }

    public function getIosPushPassphrase()
    {
        return $this->ios_push_passphrase;
    }

    public function getIosProtocol()
    {
        return $this->ios_protocol;
    }

    public function getIosApnsServer()
    {
        return $this->ios_apns_server;
    }

    public function getIosApnsTopic()
    {
        return $this->ios_apns_topic;
    }
}