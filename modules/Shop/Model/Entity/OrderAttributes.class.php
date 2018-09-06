<?php

namespace Cx\Modules\Shop\Model\Entity;

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
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
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
     */
    public function setAttributeName($attributeName)
    {
        $this->attributeName = $attributeName;
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
     */
    public function setOptionName($optionName)
    {
        $this->optionName = $optionName;
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
     */
    public function setPrice($price)
    {
        $this->price = $price;
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
     */
    public function setOrderItems(\Cx\Modules\Shop\Model\Entity\OrderItems $orderItems = null)
    {
        $this->orderItems = $orderItems;
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
