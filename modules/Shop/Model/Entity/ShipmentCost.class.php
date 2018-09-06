<?php

namespace Cx\Modules\Shop\Model\Entity;

/**
 * ShipmentCost
 */
class ShipmentCost extends \Cx\Model\Base\EntityBase {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $shipperId;

    /**
     * @var integer
     */
    protected $maxWeight;

    /**
     * @var string
     */
    protected $freeFrom;

    /**
     * @var string
     */
    protected $fee;

    /**
     * @var \Cx\Modules\Shop\Model\Entity\Shipper
     */
    protected $shipper;


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
     * Set shipperId
     *
     * @param integer $shipperId
     */
    public function setShipperId($shipperId)
    {
        $this->shipperId = $shipperId;
    }

    /**
     * Get shipperId
     *
     * @return integer 
     */
    public function getShipperId()
    {
        return $this->shipperId;
    }

    /**
     * Set maxWeight
     *
     * @param integer $maxWeight
     */
    public function setMaxWeight($maxWeight)
    {
        $this->maxWeight = $maxWeight;
    }

    /**
     * Get maxWeight
     *
     * @return integer 
     */
    public function getMaxWeight()
    {
        return $this->maxWeight;
    }

    /**
     * Set freeFrom
     *
     * @param string $freeFrom
     */
    public function setFreeFrom($freeFrom)
    {
        $this->freeFrom = $freeFrom;
    }

    /**
     * Get freeFrom
     *
     * @return string 
     */
    public function getFreeFrom()
    {
        return $this->freeFrom;
    }

    /**
     * Set fee
     *
     * @param string $fee
     */
    public function setFee($fee)
    {
        $this->fee = $fee;
    }

    /**
     * Get fee
     *
     * @return string 
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * Set shipper
     *
     * @param \Cx\Modules\Shop\Model\Entity\Shipper $shipper
     */
    public function setShipper(\Cx\Modules\Shop\Model\Entity\Shipper $shipper = null)
    {
        $this->shipper = $shipper;
    }

    /**
     * Get shipper
     *
     * @return \Cx\Modules\Shop\Model\Entity\Shipper 
     */
    public function getShipper()
    {
        return $this->shipper;
    }
}
