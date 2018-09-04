<?php

namespace Cx\Core\User\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProfileTitle
 */
class ProfileTitle extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var integer
     */
    protected $orderId = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $userProfile;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userProfile = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set title
     *
     * @param string $title
     * @return ProfileTitle
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set orderId
     *
     * @param integer $orderId
     * @return ProfileTitle
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return integer 
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Add userProfile
     *
     * @param \Cx\Core\User\Model\Entity\UserProfile $userProfile
     * @return ProfileTitle
     */
    public function addUserProfile(\Cx\Core\User\Model\Entity\UserProfile $userProfile)
    {
        $this->userProfile[] = $userProfile;

        return $this;
    }

    /**
     * Remove userProfile
     *
     * @param \Cx\Core\User\Model\Entity\UserProfile $userProfile
     */
    public function removeUserProfile(\Cx\Core\User\Model\Entity\UserProfile $userProfile)
    {
        $this->userProfile->removeElement($userProfile);
    }

    /**
     * Get userProfile
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserProfile()
    {
        return $this->userProfile;
    }
}
