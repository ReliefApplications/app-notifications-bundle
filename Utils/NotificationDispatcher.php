<?php

namespace Reliefapps\NotificationBundle\Utils;

use Reliefapps\NotificationBundle\Utils\PushManager;

//Entities
use Reliefapps\NotificationBundle\Entity\Notification;

class NotificationDispatcher{

    private $pushManager;
    private $em;

    public function __construct(PushManager $pushManager)
    {
        $this->pushManager = $pushManager;
    }

    //send notification to all the users found in the database
    public function sendNotificationtoUser($users, $options)
    {
        $deviceTokens = array();
        $returnMessage = "";

        $title  = array_key_exists("title",  $options) ? $options["title"]  : "";
        $body   = array_key_exists("body",   $options) ? $options["body"]   : "";
        $type   = array_key_exists("type",   $options) ? $options["type"]   : Notification::NONE;

        foreach($users as $user)
        {
            try{
                $idPhone = $user->getIdPhone();
                array_push($deviceTokens,$user->getIdPhone());
            }catch(Exception $e){
                $returnMessage = "ERROR : the user ".$user." does not have a getIdPhone method - ".$e;
            }
        }

        $this->pushManager->sendPush($deviceTokens, $title);
        return $returnMessage;
    }
}


?>