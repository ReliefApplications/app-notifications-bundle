<?php

namespace Reliefapps\NotificationBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class Device
{
    const TYPE_ANDROID = 0;
    const TYPE_IOS     = 1;
    const TYPE_WINDOWS = 2;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=255)
     */
    protected $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    protected $token;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="smallint", nullable=true)
     */
    protected $type;

    /**
    * @var boolean
    *
    * @ORM\Column(name="acceptPush", type="boolean", nullable=true)
    */
    protected $acceptPush;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationDate", type="datetime", nullable=true)
     */
    protected $creationDate;


    // ==================================================================================
    // Automatic generate functions
    // ==================================================================================

    public function __construct(){
        $this->setAcceptPush(true);
        $this->setCreationDate(new \DateTime);
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return UserDevice
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return UserDevice
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return UserDevice
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set acceptPush
     *
     * @param boolean $acceptPush
     *
     * @return UserDevice
     */
    public function setAcceptPush($acceptPush)
    {
        $this->acceptPush = $acceptPush;

        return $this;
    }

    /**
     * Get acceptPush
     *
     * @return boolean
     */
    public function getAcceptPush()
    {
        return $this->acceptPush;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return UserDevice
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }
}
