<?php

namespace Reliefapps\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserDevice
 *
 * @ORM\Table(name="user_device")
 * @ORM\Entity(repositoryClass="Reliefapps\NotificationBundle\Repository\UserDeviceRepository")
 */
class UserDevice
{
    const TYPE_ANDROID = 0;
    const TYPE_IOS     = 1;
    const TYPE_WINDOWS = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="smallint", nullable=true)
     */
    private $type;

    /**
    * @var boolean
    *
    * @ORM\Column(name="acceptPush", type="boolean", nullable=true)
    */
    private $acceptPush;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationDate", type="datetime", nullable=true)
     */
    private $creationDate;


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
}
