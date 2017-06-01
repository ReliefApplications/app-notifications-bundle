<?php

namespace Reliefapps\NotificationBundle\Resources\Model;


class AndroidAction
{
    /**
     *  @var string
     *  Optional. The name of a drawable resource to use as the small-icon. The name should not include the extension.
     */
    private $icon;

    /**
     *  @var string
     *  Required. The label to display for the action button.
     */
    private $title;

    /**
     *  @var string
     *  Required. The function to be executed or the event to be emitted when the action button is pressed. The function must be accessible from the global namespace. If you provide myCallback then it amounts to calling window.myCallback. If you provide app.myCallback then there needs to be an object call app, with a function called myCallback accessible from the global namespace, i.e. window.app.myCallback. If there isn't a function with the specified name an event will be emitted with the callback name.
     */
    private $callback;

    /**
     *  @var boolean
     *  Optional. Whether or not to bring the app to the foreground when the action button is pressed. (Default true)
     */
    private $foreground;

    /**
     *  @var boolean
     *  Optional. Whether or not to provide a quick reply text field to the user when the button is clicked. (Default false)
     */
    private $inline;

    public function __construct()
    {
        $this->icon         = null;
        $this->title        = null;
        $this->callback     = null;
        $this->foreground   = null;
        $this->inline       = null;
    }

    public function toArray(){
        return array(
            "icon" => $this->getIcon(),
            "title" => $this->getTitle(),
            "callback" => $this->getCallback(),
            "foreground" => $this->getForeground(),
            "inline" => $this->getInline(),
        );
    }

    /**
     * Get the value of Icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set the value of Icon
     *
     * @param string icon
     *
     * @return self
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get the value of Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of Title
     *
     * @param string title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of Callback
     *
     * @return string
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Set the value of Callback
     *
     * @param string callback
     *
     * @return self
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Get the value of Foreground
     *
     * @return boolean
     */
    public function getForeground()
    {
        return $this->foreground;
    }

    /**
     * Set the value of Foreground
     *
     * @param boolean foreground
     *
     * @return self
     */
    public function setForeground($foreground)
    {
        $this->foreground = $foreground;

        return $this;
    }

    /**
     * Get the value of Inline
     *
     * @return boolean
     */
    public function getInline()
    {
        return $this->inline;
    }

    /**
     * Set the value of Inline
     *
     * @param boolean inline
     *
     * @return self
     */
    public function setInline($inline)
    {
        $this->inline = $inline;

        return $this;
    }
}
