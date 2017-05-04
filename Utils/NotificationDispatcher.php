<?php

namespace Reliefapps\NotificationBundle\Utils;

use Reliefapps\NotificationBundle\Utils\PushManager;

//Entities
use Reliefapps\NotificationBundle\Entity\Notification;
use Reliefapps\NotificationBundle\Entity\UserDevice;

class NotificationDispatcher{

    private $pushManager;
    private $em;

    public function __construct(PushManager $pushManager)
    {
        $this->pushManager = $pushManager;
    }

    //sendPush to all the allowed users found in the database
    public function sendNotificationtoUser($userDevices, $options)
    {
        $allowedUserDevices = array();

        foreach($userDevices as $userDevice){
            if($userDevice->getAcceptPush()){
                array_push($allowedUserDevices->getToken(), $userDevice):
            }
        }

        $title  = array_key_exists("title",  $options) ? $options["title"]  : "";
        $body   = array_key_exists("body",   $options) ? $options["body"]   : "";
        $type   = array_key_exists("type",   $options) ? $options["type"]   : Notification::NONE;

        $this->pushManager->sendPush($allowedUserDevices, $title);
    }
}


?>