<?php

namespace Cx\Core\User\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserAttributeValue
 */
class UserAttributeValue extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $attributeId;

    /**
     * @var integer
     */
    protected $userId;

    /**
     * @var integer
     */
    protected $history = 0;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var \Cx\Core\User\Model\Entity\User
     */
    protected $user;

    /**
     * @var \Cx\Core\User\Model\Entity\UserAttribute
     */
    protected $userAttribute;


    /**
     * Set attributeId
     *
     * @param integer $attributeId
     */
    public function setAttributeId($attributeId)
    {
        $this->attributeId = $attributeId;
    }

    /**
     * Get attributeId
     *
     * @return integer 
     */
    public function getAttributeId()
    {
        return $this->attributeId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set history
     *
     * @param integer $history
     */
    public function setHistory($history)
    {
        $this->history = $history;
    }

    /**
     * Get history
     *
     * @return integer 
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Set value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set user
     *
     * @param \Cx\Core\User\Model\Entity\User $user
     */
    public function setUser(\Cx\Core\User\Model\Entity\User $user = null)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return \Cx\Core\User\Model\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set userAttribute
     *
     * @param \Cx\Core\User\Model\Entity\UserAttribute $userAttribute
     */
    public function setUserAttribute(\Cx\Core\User\Model\Entity\UserAttribute $userAttribute = null)
    {
        $this->userAttribute = $userAttribute;
    }

    /**
     * Get userAttribute
     *
     * @return \Cx\Core\User\Model\Entity\UserAttribute
     */
    public function getUserAttribute()
    {
        return $this->userAttribute;
    }
}
