<?php

namespace Cx\Core\User\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserAttribute
 */
class UserAttribute extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var enum_user_userattribute_type
     */
    protected $type = 'text';

    /**
     * @var enum_user_userattribute_mandatory
     */
    protected $mandatory = '0';

    /**
     * @var enum_user_userattribute_sorttype
     */
    protected $sortType = 'asc';

    /**
     * @var integer
     */
    protected $orderId = 0;

    /**
     * @var enum_user_userattribute_accessspecial
     */
    protected $accessSpecial = '';

    /**
     * @var integer
     */
    protected $accessId;

    /**
     * @var integer
     */
    protected $readAccessId;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $parent;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $userAttributeName;

    /**
     * @var \Cx\Core\User\Model\Entity\UserAttribute
     */
    protected $children;

    /***
     * @var enum_user_userattribute_accessspecial
     */
    protected $isDefault;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parent = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userAttributeName = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set type
     *
     * @param enum_user_userattribute_type $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return enum_user_userattribute_type 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set mandatory
     *
     * @param enum_user_userattribute_mandatory $mandatory
     */
    public function setMandatory($mandatory)
    {
        $this->mandatory = $mandatory;
    }

    /**
     * Get mandatory
     *
     * @return enum_user_userattribute_mandatory 
     */
    public function getMandatory()
    {
        return $this->mandatory;
    }

    /**
     * Set sortType
     *
     * @param enum_user_userattribute_sorttype $sortType
     */
    public function setSortType($sortType)
    {
        $this->sortType = $sortType;
    }

    /**
     * Get sortType
     *
     * @return enum_user_userattribute_sorttype 
     */
    public function getSortType()
    {
        return $this->sortType;
    }

    /**
     * Set orderId
     *
     * @param integer $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
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
     * Set accessSpecial
     *
     * @param enum_user_userattribute_accessspecial $accessSpecial
     */
    public function setAccessSpecial($accessSpecial)
    {
        $this->accessSpecial = $accessSpecial;
    }

    /**
     * Get accessSpecial
     *
     * @return enum_user_userattribute_accessspecial 
     */
    public function getAccessSpecial()
    {
        return $this->accessSpecial;
    }

    /**
     * Set accessId
     *
     * @param integer $accessId
     */
    public function setAccessId($accessId)
    {
        $this->accessId = $accessId;
    }

    /**
     * Get accessId
     *
     * @return integer 
     */
    public function getAccessId()
    {
        return $this->accessId;
    }

    /**
     * Set readAccessId
     *
     * @param integer $readAccessId
     */
    public function setReadAccessId($readAccessId)
    {
        $this->readAccessId = $readAccessId;
    }

    /**
     * Get readAccessId
     *
     * @return integer 
     */
    public function getReadAccessId()
    {
        return $this->readAccessId;
    }

    /**
     * Set default
     *
     * @param enum_user_userattribute_type $isDefault
     */
    public function setDefault($isDefault)
    {
        $this->isDefault = $isDefault;
    }

    /**
     * Get default
     *
     * @return enum_user_userattribute_type
     */
    public function getDefault()
    {
        return $this->isDefault;
    }

    /**
     * Add parent
     *
     * @param \Cx\Core\User\Model\Entity\UserAttribute $parent
     */
    public function addParent(\Cx\Core\User\Model\Entity\UserAttribute $parent)
    {
        $this->parent[] = $parent;
    }

    /**
     * Remove parent
     *
     * @param \Cx\Core\User\Model\Entity\UserAttribute $parent
     */
    public function removeParent(\Cx\Core\User\Model\Entity\UserAttribute $parent)
    {
        $this->parent->removeElement($parent);
    }

    /**
     * Get parent
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add userAttributeName
     *
     * @param \Cx\Core\User\Model\Entity\UserAttributeName $userAttributeName
     */
    public function addUserAttributeName(\Cx\Core\User\Model\Entity\UserAttributeName $userAttributeName)
    {
        $this->userAttributeName[] = $userAttributeName;
    }

    /**
     * Remove userAttributeName
     *
     * @param \Cx\Core\User\Model\Entity\UserAttributeName $userAttributeName
     */
    public function removeUserAttributeName(\Cx\Core\User\Model\Entity\UserAttributeName $userAttributeName)
    {
        $this->userAttributeName->removeElement($userAttributeName);
    }

    /**
     * Get userAttributeName
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserAttributeName()
    {
        return $this->userAttributeName;
    }

    /**
     * Set children
     *
     * @param \Cx\Core\User\Model\Entity\UserAttribute $children
     */
    public function setChildren(\Cx\Core\User\Model\Entity\UserAttribute $children = null)
    {
        $this->children = $children;
    }

    /**
     * Get children
     *
     * @return \Cx\Core\User\Model\Entity\UserAttribute 
     */
    public function getChildren()
    {
        return $this->children;
    }
}
