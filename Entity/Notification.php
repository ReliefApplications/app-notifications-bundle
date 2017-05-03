<?php

  namespace Reliefapps\NotificationBundle\Entity;

  use RwNotificationsBundle\Entity\User;
  use Doctrine\ORM\Mapping as ORM;

/**
* Notification
* @ORM\Entity()
*/
class Notification
{
  const NONE = -1;

  const EVERYONE = 0;

  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
  **/
  private $id;

  /**
   * @ORM\Column(type="text", nullable=true)
   */
  private $content;

  /**
   * @ORM\Column(type="smallint", nullable=true)
   */
  private $type;

  /**
   * @ORM\Column(type="boolean", nullable=true)
   */
  private $sent;

  /**
   * @ORM\Column(type="datetime", nullable=true)
   */
  private $creationDate;


  public function __construct()
  {
    $this->setContent("");
    $this->setSent(false);
    $this->setType(Notification::NONE);
    $this->setCreationDate(new \DateTime());
  }

// ==================================================================================
// Automatic generate functions
// ==================================================================================


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Notification
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Notification
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set sent
     *
     * @param boolean $sent
     *
     * @return Notification
     */
    public function setSent($sent)
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * Get sent
     *
     * @return boolean
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Notification
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
}
