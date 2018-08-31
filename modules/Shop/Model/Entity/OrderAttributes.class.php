<?php

namespace Cx\Modules\Shop\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderAttributes
 */
class OrderAttributes extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $itemId;

    /**
     * @var string
     */
    protected $attributeName;

    /**
     * @var string
     */
    protected $optionName;

    /**
     * @var string
     */
    protected $price;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\OrderItems
     */
    protected $orderItems;


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
     * Set itemId
     *
     * @param integer $itemId
     * @return OrderAttributes
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * Get itemId
     *
     * @return integer 
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Set attributeName
     *
     * @param string $attributeName
     * @return OrderAttributes
     */
    public function setAttributeName($attributeName)
    {
        $this->attributeName = $attributeName;

        return $this;
    }

    /**
     * Get attributeName
     *
     * @return string 
     */
    public function getAttributeName()
    {
        return $this->attributeName;
    }

    /**
     * Set optionName
     *
     * @param string $optionName
     * @return OrderAttributes
     */
    public function setOptionName($optionName)
    {
        $this->optionName = $optionName;

        return $this;
    }

    /**
     * Get optionName
     *
     * @return string 
     */
    public function getOptionName()
    {
        return $this->optionName;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return OrderAttributes
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
     * Set orderItems
     *
     * @param \Cx\Modules\Shop\Model\Entity\OrderItems $orderItems
     * @return OrderAttributes
     */
    public function setOrderItems(\Cx\Modules\Shop\Model\Entity\OrderItems $orderItems = null)
    {
        $this->orderItems = $orderItems;

        return $this;
    }

    /**
     * Get orderItems
     *
     * @return \Cx\Modules\Shop\Model\Entity\OrderItems 
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }
}
