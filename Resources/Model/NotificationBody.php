<?php

namespace Reliefapps\NotificationBundle\Resources\Model;


class NotificationBody
{
    const PAYLOAD_ARRAY_ANDROID  = 0;
    const PAYLOAD_JSON_IOS      = 1;

    /**
     *  @var String
     *  Title of the notification
     */
    private $title;

    /**
     *  @var String
     *  Body of the notification
     */
    private $body;

    /**
     *  @var Integer
     *  Badge number to display on the app
     */
    private $badge;

    /**
     *  @var Array
     *  Color or the led for android
     */
    private $ledColor;

    /**
     *  @var String
     *  Category of the notification, used for custom type coded on app side
     */
    private $category;

    /**
     *  @var Array
     *  List of android actions
     */
    private $actions;

    public function __construct()
    {
        $this->title    = null;
        $this->body     = null;
        $this->badge    = null;
        $this->ledColor = null;
        $this->category = null;
        $this->actions  = null;
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
                "aps" => array(
                    "alert" => array(
                        "title" => $this->getTitle(),
                        "body"  => $this->getBody(),
                    ),
                )
        );
        if($this->getBadge()){
            $payload["aps"]["badge"] = $this->getBadge();
        }
        if($this->getCategory()){
            $payload["aps"]["category"] = $this->getCategory();
        }

        return json_encode($payload);
    }

    private function getAndroidPayload()
    {
        $payload = array();
        if($this->getTitle()){
            $payload["title"] = $this->getTitle();
        }
        if($this->getBody()){
            $payload["message"] = $this->getBody();
        }
        if($this->getActions()){
            $payload["actions"] = $this->getActions();
        }
        if($this->getLedColor()){
            $payload["ledColor"] = $this->getLedColor();
        }

        return $payload;
    }

    /**
     * Get the value of Title
     *
     * @return String
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of Title
     *
     * @param String title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of Body
     *
     * @return String
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the value of Body
     *
     * @param String body
     *
     * @return self
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get the value of Badge
     *
     * @return Integer
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * Set the value of Badge
     *
     * @param Integer badge
     *
     * @return self
     */
    public function setBadge($badge)
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * Get the value of Led Color
     *
     * @return Array
     */
    public function getLedColor()
    {
        return $this->ledColor;
    }

    /**
     * Set the value of Led Color
     *
     * @param Array ledColor
     *
     * @return self
     */
    public function setLedColor($ledColor)
    {
        $this->ledColor = $ledColor;

        return $this;
    }

    /**
     * Get the value of Category
     *
     * @return String
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set the value of Category
     *
     * @param String category
     *
     * @return self
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the value of Actions
     *
     * @return Array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Add an action
     *
     * @return Array
     */
    public function addAction($action)
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * Set the value of Actions
     *
     * @param Array actions
     *
     * @return self
     */
    public function setActions($actions)
    {
        $this->actions = $actions;

        return $this;
    }

}