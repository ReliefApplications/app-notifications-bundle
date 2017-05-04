<?php

namespace Reliefapps\NotificationBundle\Resources\Model;


class NotificationBody
{
    const PAYLOAD_ARRAY_ANDROID  = 0;
    const PAYLOAD_JSON_IOS      = 1;

    /**
     *  @var String Title of the notification
     */
    private $title;

    /**
     *  @var String Body of the notification
     */
    private $body;

    /**
     *  @var Integer Badge number to display on the app
     */
    private $badge;

    /**
     *  Set the title of the notification
     *
     *  @param String $title
     *
     *  @return NotificationBody
     */
    public function setTitle( $title )
    {
        $this->title = $title;

        return $this;
    }

    /**
     *  Get the title of the notification
     *
     *  @return String $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *  Set the body of the notification
     *
     *  @param String $body
     *
     *  @return NotificationBody
     */
    public function setBody( $body )
    {
        $this->body = $body;

        return $this;
    }

    /**
     *  Get the body of the notification
     *
     *  @return String $body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     *  Set the badge of the notification
     *
     *  @param Integer $badge
     *
     *  @return NotificationBody
     */
    public function setBadge( $badge )
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     *  Get the badge of the notification
     *
     *  @return String $badge
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     *  Get Payload for a given device
     *
     *  @param Integer Payload Type (see constants)
     *
     *  @return String Json payload
     */
    public function getPayload($payload_type)
    {
        switch ($payload_type) {
            case self::PAYLOAD_JSON_IOS:
                return $this->getiOSPayload();
                break;
            case self::PAYLOAD_ARRAY_ANDROID:
                return $this->getAndroidPayload();
                break;

            default:
                throw new \Exception('Invalid Payload type : ' . $payload_type);
                break;
        }
    }

    private function getiOSPayload()
    {
        $payload = array(
                'aps' => array(
                    "alert" => array(
                        "title" => $this->getTitle(),
                        "body"  => $this->getBody(),
                    ),
                    "badge" => $this->getBadge(),
                )
        );

        return json_encode($payload);
    }

    private function getAndroidPayload()
    {
        $payload = array( "title" => $this->getTitle() , "message" => $this->getBody() );

        return $payload;
    }
}