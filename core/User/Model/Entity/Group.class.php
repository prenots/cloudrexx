<?php

namespace Cx\Core\User\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Group
 */
class Group extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $groupId;

    /**
     * @var string
     */
    protected $groupName = '';

    /**
     * @var string
     */
    protected $groupDescription = '';

    /**
     * @var integer
     */
    protected $isActive = 1;

    /**
     * @var enum_user_group_type
     */
    protected $type = 'frontend';

    /**
     * @var string
     */
    protected $homepage = '';

    /**
     * @var integer
     */
    protected $toolbar = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get groupId
     *
     * @return integer 
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set groupName
     *
     * @param string $groupName
     * @return Group
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;

        return $this;
    }

    /**
     * Get groupName
     *
     * @return string 
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * Set groupDescription
     *
     * @param string $groupDescription
     * @return Group
     */
    public function setGroupDescription($groupDescription)
    {
        $this->groupDescription = $groupDescription;

        return $this;
    }

    /**
     * Get groupDescription
     *
     * @return string 
     */
    public function getGroupDescription()
    {
        return $this->groupDescription;
    }

    /**
     * Set isActive
     *
     * @param integer $isActive
     * @return Group
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return integer 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set type
     *
     * @param enum_user_group_type $type
     * @return Group
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return enum_user_group_type 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set homepage
     *
     * @param string $homepage
     * @return Group
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;

        return $this;
    }

    /**
     * Get homepage
     *
     * @return string 
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * Set toolbar
     *
     * @param integer $toolbar
     * @return Group
     */
    public function setToolbar($toolbar)
    {
        $this->toolbar = $toolbar;

        return $this;
    }

    /**
     * Get toolbar
     *
     * @return integer 
     */
    public function getToolbar()
    {
        return $this->toolbar;
    }

    /**
     * Add user
     *
     * @param \Cx\Core\User\Model\Entity\User $user
     * @return Group
     */
    public function addUser(\Cx\Core\User\Model\Entity\User $user)
    {
        $this->user[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \Cx\Core\User\Model\Entity\User $user
     */
    public function removeUser(\Cx\Core\User\Model\Entity\User $user)
    {
        $this->user->removeElement($user);
    }

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUser()
    {
        return $this->user;
    }
}
