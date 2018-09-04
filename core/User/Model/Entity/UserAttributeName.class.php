<?php

namespace Cx\Core\User\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserAttributeName
 */
class UserAttributeName extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $attributeId = 0;

    /**
     * @var integer
     */
    protected $langId = 0;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var \Cx\Core\User\Model\Entity\UserAttribute
     */
    protected $userAttribute;


    /**
     * Set attributeId
     *
     * @param integer $attributeId
     * @return UserAttributeName
     */
    public function setAttributeId($attributeId)
    {
        $this->attributeId = $attributeId;

        return $this;
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
     * Set langId
     *
     * @param integer $langId
     * @return UserAttributeName
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;

        return $this;
    }

    /**
     * Get langId
     *
     * @return integer 
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return UserAttributeName
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set userAttribute
     *
     * @param \Cx\Core\User\Model\Entity\UserAttribute $userAttribute
     * @return UserAttributeName
     */
    public function setUserAttribute(\Cx\Core\User\Model\Entity\UserAttribute $userAttribute = null)
    {
        $this->userAttribute = $userAttribute;

        return $this;
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
