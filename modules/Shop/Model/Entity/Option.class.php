<?php

namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Option
 */
class Option extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $attributeId;

    /**
     * @var string
     */
    protected $price;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $relProductAttributes;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Attribute
     */
    protected $attribute;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->relProductAttributes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set attributeId
     *
     * @param integer $attributeId
     * @return Option
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
     * Set price
     *
     * @param string $price
     * @return Option
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Add relProductAttributes
     *
     * @param \Cx\Modules\Shop\Model\Entity\RelProductAttribute $relProductAttributes
     * @return Option
     */
    public function addRelProductAttribute(\Cx\Modules\Shop\Model\Entity\RelProductAttribute $relProductAttributes)
    {
        $this->relProductAttributes[] = $relProductAttributes;

        return $this;
    }

    /**
     * Remove relProductAttributes
     *
     * @param \Cx\Modules\Shop\Model\Entity\RelProductAttribute $relProductAttributes
     */
    public function removeRelProductAttribute(\Cx\Modules\Shop\Model\Entity\RelProductAttribute $relProductAttributes)
    {
        $this->relProductAttributes->removeElement($relProductAttributes);
    }

    /**
     * Get relProductAttributes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRelProductAttributes()
    {
        return $this->relProductAttributes;
    }

    /**
     * Set attribute
     *
     * @param \Cx\Modules\Shop\Model\Entity\Attribute $attribute
     * @return Option
     */
    public function setAttribute(\Cx\Modules\Shop\Model\Entity\Attribute $attribute = null)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Get attribute
     *
     * @return \Cx\Modules\Shop\Model\Entity\Attribute 
     */
    public function getAttribute()
    {
        return $this->attribute;
    }
}
